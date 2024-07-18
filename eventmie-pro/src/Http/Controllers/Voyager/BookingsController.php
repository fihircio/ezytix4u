<?php

namespace Classiebit\Eventmie\Http\Controllers\Voyager;
use Facades\Classiebit\Eventmie\Eventmie;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataRestored;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadImagesDeleted;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use Illuminate\Http\RedirectResponse;
use Auth;

use Classiebit\Eventmie\Models\Commission;
use Classiebit\Eventmie\Models\Event;
use Classiebit\Eventmie\Scopes\BulkScope;
use Classiebit\Eventmie\Models\Transaction;
use Classiebit\Eventmie\Http\Controllers\SendEmailController;

class BookingsController extends VoyagerBaseController
{
    use BreadRelationshipParser;

    public function __construct()
    {
        // disable modification functions that are not managed from admin panel
        $route_name     = "voyager.bookings";
        $enable_routes = [
            "$route_name.index", 
            "$route_name.show", 
            "$route_name.edit", 
            "$route_name.update", 
            "$route_name.destroy",

            "$route_name.bulk_bookings",
            "$route_name.bulk_edit",
            "$route_name.bulk_update",
            "$route_name.bulk_delete",
            "$route_name.bulk_show",
            "$route_name.bulk_export"
        ];
        if(! in_array(\Route::current()->getName(), $enable_routes))
        {
            return redirect()->route('voyager.bookings.index')->send();
        }
        // ---------------------------------------------------------------------

        $this->commission   = new Commission; 
       // $this->transaction   = new Transaction; 
       // $this->booking       = new Booking;
       // $this->event         = new Event;   
    }

    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Browse our Data Type (B)READ
    //
    //****************************************
    public function index(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];

        $searchNames = [];
        if ($dataType->server_side) {
            $searchable = SchemaManager::describeTable(app($dataType->model_name)->getTable())->pluck('name')->toArray();
            $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->get();
            foreach ($searchable as $key => $value) {
                $field = $dataRow->where('field', $value)->first();
                $displayName = ucwords(str_replace('_', ' ', $value));
                if ($field !== null) {
                    $displayName = $field->getTranslatedAttribute('display_name');
                }
                $searchNames[$value] = $displayName;
            }
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', $dataType->order_direction);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query = $model->{$dataType->scope}();
            } else {
                $query = $model::select('*');
            }

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model)) && Auth::user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            }

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($model);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'browse', $isModelTranslatable);

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        // Actions
        $actions = [];
        if (!empty($dataTypeContent->first())) {
            foreach (Voyager::actions() as $action) {
                $action = new $action($dataType, $dataTypeContent->first());

                if ($action->shouldActionDisplayOnDataType()) {
                    $actions[] = $action;
                }
            }
        }

        // Define showCheckboxColumn
        $showCheckboxColumn = false;
        if (Auth::user()->can('delete', app($dataType->model_name))) {
            $showCheckboxColumn = true;
        } else {
            foreach ($actions as $action) {
                if (method_exists($action, 'massAction')) {
                    $showCheckboxColumn = true;
                }
            }
        }

        // Define orderColumn
        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + ($showCheckboxColumn ? 1 : 0);
            $orderColumn = [[$index, $sortOrder ?? 'desc']];
        }


        // if have booking email data then send booking notification
        $is_success = !empty(session('booking_email_data')) ? 1 : 0;
        
        $view = 'eventmie::vendor.voyager.bookings.browse';

        return Eventmie::view($view, compact(
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted',
            'showCheckboxColumn',
            'is_success'
        ));
    }

    

     
    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        
        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $data = $model->withTrashed()->findOrFail($id);
        } else {
            $data = $model->findOrFail($id);
        }
        
        // Check permission
        $this->authorize('edit', $data);
        
        $old_is_paid = $data->is_paid;
        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        if(empty($old_is_paid)  && !empty($request->is_paid))
            session(['booking_email_data' => [$data->refresh()] ]);

        
        $send_email = new SendEmailController;
            
        $send_email->send_email($request);

        // extra data update ========================================================================
        
        $params = [
            'booking_id'       => $id,
            'organiser_id'     => $data->organiser_id,
            'status'           => $request->status == "on" ? 1 : 0,
        ];
        
        // edit commision table status when change booking table status change by organiser 
        $edit_commission  = $this->commission->edit_commission($params);    
        
        if(empty($edit_commission))
            return error('Commission not found!', Response::HTTP_BAD_REQUEST );
        // extra data update ========================================================================

        event(new BreadDataUpdated($dataType, $data));

        if (auth()->user()->can('browse', app($dataType->model_name))) {
            $redirect = redirect()->route("voyager.{$dataType->slug}.index");
        } else {
            $redirect = redirect()->back();
        }

        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated')." {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

       /**
     *  bulk booking index page
     */
    public function bulk_bookings(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        
        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];

        $searchNames = [];
        if ($dataType->server_side) {
            $searchable = SchemaManager::describeTable(app($dataType->model_name)->getTable())->pluck('name')->toArray();
            
            $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->get();
            

            foreach ($searchable as $key => $value) {
                $field = $dataRow->where('field', $value)->first();
                $displayName = ucwords(str_replace('_', ' ', $value));
                if ($field !== null) {
                    $displayName = $field->getTranslatedAttribute('display_name');
                }
                $searchNames[$value] = $displayName;
            }
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', $dataType->order_direction);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query = $model->{$dataType->scope}();
            } else {
                $query = $model::select('*', DB::raw('COUNT(ticket_id) as bulk_quantity'))->groupBy('ticket_id', 'bulk_code');
                
            }

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model)) && Auth::user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            }

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }
        
        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($model);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'browse', $isModelTranslatable);

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        // Actions
        $actions = [];
        if (!empty($dataTypeContent->first())) {
            foreach (Voyager::actions() as $action) {
                $action = new $action($dataType, $dataTypeContent->first());

                if ($action->shouldActionDisplayOnDataType()) {
                    $actions[] = $action;
                }
            }
        }

        // Define showCheckboxColumn
        $showCheckboxColumn = false;
        if (Auth::user()->can('delete', app($dataType->model_name))) {
            $showCheckboxColumn = true;
        } else {
            foreach ($actions as $action) {
                if (method_exists($action, 'massAction')) {
                    $showCheckboxColumn = true;
                }
            }
        }

        // Define orderColumn
        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + ($showCheckboxColumn ? 1 : 0);
            $orderColumn = [[$index, $sortOrder ?? 'desc']];
        }


        // if have booking email data then send booking notification
        $is_success = !empty(session('booking_email_data')) ? 1 : 0;
        
        $view = 'vendor.eventmie-pro.vendor.voyager.bookings.bulk';
        
        return Eventmie::view($view, compact(
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted',
            'showCheckboxColumn',
            'is_success'
        ));
    }    

    /**
     *  bulk booking edit
     */

    // POST BR(E)AD
    public function bulk_bookings_edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (strlen($dataType->model_name) != 0) {
            //CUSTOM
            $model = app($dataType->model_name)->withoutGlobalScope(BulkScope::class);
            //CUSTOM
            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);

        $view = 'vendor.eventmie-pro.vendor.voyager.bookings.bulk_edit';

        return Eventmie::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    /**
     *  UPDATE
     */
    public function bulk_bookings_update(Request $request, $id)
    {
        // demo mode restrictions
        if(config('voyager.demo_mode'))
        {
            return redirect()
                    ->route("voyager.users.index")
                    ->with([
                        'message'    => 'Demo mode',
                        'alert-type' => 'info',
                    ])
                    ->send();
        }

        /* VoyagerUserController update method */
        if (Auth::user()->getKey() == $id) {
            $request->merge([
                'role_id'                              => Auth::user()->role_id,
                'user_belongstomany_role_relationship' => Auth::user()->roles->pluck('id')->toArray(),
            ]);
        }
        /* VoyagerUserController update method */

        /*  */

        /* VoyagerBaseController update method */
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        // $model = app($dataType->model_name);
        //CUSTOM
        $model = app($dataType->model_name)->withoutGlobalScope(BulkScope::class);
        //CUSTOM
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $data = $model->withTrashed()->findOrFail($id);
        } else {
            $data = $model->findOrFail($id);
        }

        /* CUSTOM */
        // Current user role id
        $currentRoleId = $data->role_id;
        /* CUSTOM */

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        event(new BreadDataUpdated($dataType, $data));

        if (auth()->user()->can('browse', app($dataType->model_name))) {
            $redirect = redirect()->route("voyager.bookings.bulk_bookings");
        } else {
            $redirect = redirect()->back();
        }

        /* CUSTOM */
        // If approved customer to organizer
        if($request->role_id > $currentRoleId) {
            $this->approvedOrganiserNotification($data);
        }
        
        /* CUSTOM */

        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated')." {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

    /**
     *  Delete
     */
    
    /**
     *   only admin can delete booking
     */

    public function bulk_bookings_delete($id = null)
    {
        // only admin can delete booking
        if(Auth::check() && !Auth::user()->hasRole('admin'))
        {
            return redirect()->route('eventmie.events');
        }

        // get event by event_slug
        if(empty($id))
            return error('Booking Not Found!', Response::HTTP_BAD_REQUEST );
        
        $params    = [
            'id'     => $id,
        ];

        $delete_booking     = Booking::withoutGlobalScope(BulkScope::class)->where($params)->delete();

        if(empty($delete_booking))
        {
            return error(__('eventmie-pro::em.booking_deleted_fail'), Response::HTTP_BAD_REQUEST );   
        }

        $msg = __('eventmie-pro::em.booking_deleted');
        
        return redirect()
        ->route("voyager.bookings.bulk_bookings")
        ->with([
            'message'    => $msg,
            'alert-type' => 'success',
        ]);
        
    }

    /**
     *  bulk booking show
     */
    public function bulk_bookings_show($id = null, $view = 'eventmie::bookings.show', $extra = [])
    {
        
        $id    = (int) $id;
        $organiser_id  = Auth::id(); 

        if(!$id)
              // redirect no matter what so that it never turns back
              return response(['status'=>__('eventmie-pro::em.invalid').' '.__('eventmie-pro::em.data'), 'url'=>'/events'], Response::HTTP_OK);    

        // admin can see booking detail page
        if(Auth::user()->hasRole('admin'))
        {
            // when admin wiil be login and he can see booking help or organiser id
            $params   = [
                'id'  => $id,
            ];

            $booking   = Booking::withoutGlobalScope(BulkScope::class)->where($params)->first();
            if(empty($booking))
                // redirect no matter what so that it never turns back
                return success_redirect(__('eventmie-pro::em.booking').' '.__('eventmie-pro::em.not_found'), route('eventmie.events_index'));  

            $organiser_id  = $booking->organiser_id;
        }

        $params = [
            'organiser_id' => $organiser_id,
            'id'           => $id,
        ];

        // get customer booking by orgniser
        $booking = Booking::withoutGlobalScope(BulkScope::class)->where($params)->first();   
    
        if(empty($booking))
        {
            // redirect no matter what so that it never turns back
            return success_redirect(__('eventmie-pro::em.booking').' '.__('eventmie-pro::em.not_found'), route('eventmie.events_index'));  
        }    

        $currency   = setting('regional.currency_default');
        
        $params = [
            'transaction_id' => $booking['transaction_id'],
            'order_number'   => $booking['order_number']
        ];

        // get transaction information by orgniser for this booking
        $payment = $this->transaction->organiser_payment_info($params);   
        
        return Eventmie::view($view, compact('booking', 'payment', 'currency', 'extra'));

    }

    /**
     * bulk export_attendees 
     */

    public function bulk_export_attendees($ticket_id = null, $bulk_code = null)
    {
        // check event is valid or not
        if(!Auth::user()->hasRole('admin'))
            abort('404');
            
        $ticket_id      = (int) $ticket_id;
        $bulk_code      = (int)($bulk_code);

        // get the booking
        
        $bookings = Booking::withoutGlobalScope(BulkScope::class)->where(['ticket_id' => $ticket_id, 'bulk_code' => $bulk_code])->get()->all();

        if(empty($bookings))
            return error_redirect('Booking Not Found!');

        // customize column values
        $bookings_csv = [];
        $bookings_custom = [];
        foreach($bookings as $key => $item)
        {
            $bookings[$key]['event_repetitive'] = $item['event_repetitive'] ? __('eventmie-pro::em.yes') : __('eventmie-pro::em.no');
            $bookings[$key]['is_paid']          = $item['is_paid'] ? __('eventmie-pro::em.yes') : __('eventmie-pro::em.no');
            
            
            if($item['booking_cancel'] == 1)
                $bookings[$key]['booking_cancel']       = __('eventmie-pro::em.pending');
            elseif($item['booking_cancel'] == 2)
                $bookings[$key]['booking_cancel']       = __('eventmie-pro::em.approved');
            elseif($item['booking_cancel'] == 3)
                $bookings[$key]['booking_cancel']       = __('eventmie-pro::em.refunded');
            else
                $bookings[$key]['booking_cancel']   = __('eventmie-pro::em.no_cancellation');

            
            if($item['status'])
                $bookings[$key]['status']           = __('eventmie-pro::em.enabled');
            else
                $bookings[$key]['status']           = __('eventmie-pro::em.disabled');

            
            $bookings[$key]['checked_in']           = $item['checked_in'].' / '.$item['quantity'];

            /* CUSTOM */
            $bookings_custom[$key]['ID']    = $item['id'];
            $bookings_custom[$key]['Order Number']    = $item['order_number'];
            $bookings_custom[$key]['Event Title']    = $item['event_title'];
            $bookings_custom[$key]['Ticket Title']    = $item['ticket_title'];
            
            $bookings[$key]['A'] = json_encode($bookings_custom[$key]);
            /* CUSTOM */
        }    

        // convert array to collection for csv
        $bookings = collect($bookings);
        $bookings_custom = collect($bookings_custom);

        // create object of laracsv
        $csvExporter = new \Laracsv\Export();

        
        // create csv 
        $csvExporter->build($bookings_custom, [
            
            //events fields which will be include
            'ID',
            'Order Number',
            'Event Title',
            'Ticket Title',
            
        ]);
        
        // download csv
        $csvExporter->download($bookings[0]['ticket_id'].'-'.$bookings[0]['bulk_code'].'-attendies.csv');
    } 

    


    
    
}
