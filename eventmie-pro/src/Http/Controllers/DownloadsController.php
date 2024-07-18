<?php

namespace Classiebit\Eventmie\Http\Controllers;
use App\Http\Controllers\Controller; 
use Facades\Classiebit\Eventmie\Eventmie;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Auth;
use Classiebit\Eventmie\Models\Event;
use Classiebit\Eventmie\Models\Ticket;
use Classiebit\Eventmie\Models\Booking;
use Classiebit\Eventmie\Models\Transaction;
use Classiebit\Eventmie\Models\Commission;
use Classiebit\Eventmie\Models\User;
use Classiebit\Eventmie\Scopes\BulkScope;


class DownloadsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // language change
        $this->middleware('common');

        // download only after login
        $this->middleware('auth');
    
        $this->event        = new Event;
        $this->ticket       = new Ticket;
        $this->booking      = new Booking;
        $this->transaction  = new Transaction;
        $this->commission   = new Commission;
    }
    
    /**
     * Show my booking
     *
     * @return array
     */
    public function index($id = NULL, $order_number = NULL)
    {
        if(!empty(setting('booking.hide_ticket_download')) &&(Auth::user()->hasRole('organiser') || Auth::user()->hasRole('customer')))
            abort('404');
            
        $id             = (int) $id;
        $order_number   = trim($order_number);

        //CUSTOM
        if(!Auth::user()->hasRole('admin'))
        {
        //CUSTOM
            $booking = $this->booking->get_event_bookings(['id'=>$id, 'order_number'=>$order_number]);

        //CUSTOM
        }
        else
        {
            $booking = Booking::with(['attendees' => function ($query) {
                $query->where(['status' => 1]);
            }, 'attendees.seat'])->withoutGlobalScope(BulkScope::class)->where(['id'=>$id])->get()->all();
        }

        if(empty($booking))
            abort('404');

        $booking = $booking[0];

        // customer can see only their bookings
        if(Auth::user()->hasRole('customer'))
            if($booking['customer_id'] != Auth::id())
                abort('404');

        // organiser can see only their events bookings
        if(Auth::user()->hasRole('organiser'))
            if($booking['organiser_id'] != Auth::id())
                abort('404');
        
        // generate QrCode
        $qrcode_data = [
            'id'            => $booking['id'],
            'order_number'  => $booking['order_number'],
        ];
        $this->createQrcode($booking, $qrcode_data);

        // get event data for ticket pdf
        $event      = $this->event->get_event(null, $booking['event_id']);
        $currency   = setting('regional.currency_default');
        
        // generate PDF
        // test PDF
        // $img_path = '';
        // return Eventmie::view('eventmie::tickets.pdf', compact('booking', 'event', 'currency', 'img_path'));
        // use http url only
        $img_path   = str_replace('https://', 'http://', url(''));
        $pdf_html   = (string) \View::make('eventmie::tickets.pdf', compact('booking', 'event', 'currency', 'img_path'));
        $pdf_name   = $booking['id'].'-'.$booking['order_number'];
        $this->generatePdf($pdf_html, $pdf_name, $booking);
        
        // download PDF
        $path           = '/storage/ticketpdfs/'.$booking['customer_id'];
        $pdf_file    = public_path().$path.'/'.$booking['id'].'-'.$booking['order_number'].'.pdf';
        if (!\File::exists($pdf_file))
            abort('404');

        return response()->download($pdf_file);
        
    }

    protected function createQrcode($data = [], $qrcode_data = [])
    {
        $path           = '/storage/qrcodes/'.$data['customer_id'];
        // first check if directory exists or not
        if (! \File::exists(public_path().$path))
            \File::makeDirectory(public_path().$path, 0755, true);
    
        $qrcode_file    = public_path().$path.'/'.$data['id'].'-'.$data['order_number'].'.png';
        
        // only create if not already created
        // if (\File::exists($qrcode_file))
        //     return TRUE;
        
        // generate QrCode
        \QrCode::format('png')->size(512)->generate(json_encode($qrcode_data), $qrcode_file);

        return TRUE;
    }

    /**
     *  generate pdf
     */
    protected function generatePdf($html = null, $pdf_name = null, $data = [])
    {
        $path           = '/storage/ticketpdfs/'.$data['customer_id'];

        // first check if directory exists or not
        if (! \File::exists(public_path().$path))
            \File::makeDirectory(public_path().$path, 0755, true);

        $pdf_file    = public_path().$path.'/'.$data['id'].'-'.$data['order_number'].'.pdf';
        
        // only create if not already created
        // if (\File::exists($pdf_file))
        //     return TRUE;
            
        // start PDF generation

        // remove white spaces and comments
        $html =  preg_replace('/>\s+</', '><', $html);
        if(empty($html))
            return false;

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $options = [
            'defaultFont' => 'sans-serif',
            'isRemoteEnabled' => TRUE,
            'isJavascriptEnabled' => FALSE,
            'debugKeepTemp' => TRUE,
            'isHtml5ParserEnabled' => TRUE,
            'enable_html5_parser' => TRUE,
        ];
        \PDF::setOptions($options)
        ->loadHTML($html)
        ->setWarnings(false)
        ->setPaper('a4', 'portrait')
        ->save($pdf_file);

        return TRUE;
    }
    
        //CUSTOM
    public function getQrCode(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|numeric|gt:0'
        ]);

        $id             = (int) $request->booking_id;
        
        // get the booking
        $booking = $this->booking->get_event_bookings(['id'=>$id]);
        if(empty($booking))
            abort('404');

        $booking = $booking[0];

        // customer can see only their bookings
        if(Auth::user()->hasRole('customer'))
            if($booking['customer_id'] != Auth::id())
                abort('404');

        // organiser can see only their events bookings
        if(Auth::user()->hasRole('organiser'))
            if($booking['organiser_id'] != Auth::id())
                abort('404');
        
        
        // If QrCode image already exists, then return qrcode image path
        $qrcode_file           = '/storage/qrcodes/'.$booking['customer_id'].'/'.$booking['id'].'-'.$booking['order_number'].'.png';
        if (\File::exists(public_path().$qrcode_file)) {
            return response()->json([ 'qrcode_file' => $qrcode_file , 'status' => true]);
        }
        
        // generate QrCode
        $qrcode_data = [
            'id'            => $booking['id'],
            'order_number'  => $booking['order_number'],
        ];
        
        $this->createQrcode($booking, $qrcode_data);

        return response()->json([ 'qrcode_file' =>  $qrcode_file, 'status' => true]);

    }

    /**
     * create zip for bulk bookings
     *
     * @return array
     */
    public function create_bulk_zip($ticket_id = NULL, $bulk_code = NULL)
    {
        if(!Auth::user()->hasRole('admin'))
            abort('404');
            
        $ticket_id      = (int) $ticket_id;
        $bulk_code      = (int)($bulk_code);

        // get the booking
        
        $bookings = Booking::withoutGlobalScope(BulkScope::class)->where(['ticket_id' => $ticket_id, 'bulk_code' => $bulk_code])->get()->all();
        
        //CUSTOM
        
        if(empty($bookings))
            abort('404');

        foreach($bookings as $key => $booking)
        {
            // generate QrCode
            $qrcode_data = [
                'id'            => $booking['id'],
                'order_number'  => $booking['order_number'],
                'ticket_hash'  => $booking['order_number'],
            ];

            $this->createQrcode($booking, $qrcode_data);
    

            // get event data for ticket pdf
            $event      = $this->event->get_event(null, $booking['event_id']);
            $currency   = setting('regional.currency_default');

            //CUSTOM
            if(!empty($event->currency)){
                $currency   = $event->currency;
            }  
            //CUSTOM
            
            // generate PDF
            // test PDF
            // $img_path = '';
            // return Eventmie::view('eventmie::tickets.pdf', compact('booking', 'event', 'currency', 'img_path'));
            // use http url only
            $img_path   = str_replace('https://', 'http://', url(''));
            
            
            // $pdf_html   = (string) \View::make('eventmie::tickets.pdf', compact('booking', 'event', 'currency', 'img_path'));
            $pdf_html   = (string) \View::make('eventmie::tickets.pdf', compact('booking', 'event', 'currency', 'img_path'));

            $pdf_name   = $booking['id'].'-'.$booking['order_number'];
            $this->generatePdf($pdf_html, $pdf_name, $booking);
            
            // $this->bulk_createQrcode($booking, $qrcode_data);
        }

        return $this->download_bulk_zip($bookings);
    }

    /**
     *  create qrcode for bulk booking
     */

    protected function bulk_createQrcode($data = [], $qrcode_data = [])
    {
        $path           = '/storage/qrcodes/'.$data['customer_id'];
        // first check if directory exists or not
        if (! \File::exists(public_path().$path))
            \File::makeDirectory(public_path().$path, 0755, true);
    
        $qrcode_file    = public_path().$path.'/'.$data['id'].'-'.$data['order_number'].'.png';
        
        // only create if not already created
        // if (\File::exists($qrcode_file))
        //     return TRUE;
        
        // generate QrCode
        \QrCode::format('png')->size(256)->generate(json_encode($qrcode_data), $qrcode_file);

        return TRUE;
    }

    /**
     *   download bulk zip
     */

    protected function download_bulk_zip($bookings = NULL)
    {
        // Define Dir Folder
        $public_dir  = public_path().'/storage/zip';

        // Zip File Name
        $zipFileName = $bookings[0]['bulk_code'].'.zip';

        // first check if directory exists or not
        if (! \File::exists($public_dir))
           \File::makeDirectory($public_dir, 0755, true);
        
        // Create ZipArchive Obj
        $zip = new ZipArchive;

        if($zip->open($public_dir.'/'.$zipFileName, ZipArchive::CREATE) === TRUE) 
        {
            foreach($bookings as $key => $data)
            {
                $path           = public_path().'/storage/ticketpdfs/'.$data['customer_id'];
                $pdf_file       = $path.'/'.$data['id'].'-'.$data['order_number'].'.pdf';
                // Add File in ZipArchive
                $zip->addFile($pdf_file, $bookings[0]['bulk_code'].'/'.$data['id'].'-'.$data['order_number'].'.pdf');
            }
     
            // Close ZipArchive     
            $zip->close();
        }
        
        // Set Header
        $headers = array(
            'Content-Type' => 'application/octet-stream',
        );


        $filetopath = $public_dir.'/'.$zipFileName;

        // Create Download Response
        if(file_exists($filetopath))
        {
            return response()->download($filetopath,$zipFileName,$headers);
        }
    }

        /**
     *  download inovoice
     */

     public function downloadInvoice(Booking $booking)
     {
         $common_order = $booking['common_order'];
 
         // if have no common_order then update
         if(empty($common_order))
             $booking->common_order = time().rand(1,988);
 
         $booking->save();
 
         $common_order = $booking->refresh()->common_order;
 
         
         
         // get the booking
          
         //CUSTOM
         if(!Auth::user()->hasRole('admin'))
         {
         //CUSTOM
             
             $bookings = Booking::with(['attendees' => function ($query) {
                 $query->where(['status' => 1]);
             }, 'attendees.seat'])->where(['common_order' => $common_order])->get();
         
         //CUSTOM
         }
         else
         {
             
             $bookings = Booking::with(['attendees' => function ($query) {
                 $query->where(['status' => 1]);
             }, 'attendees.seat'])->withoutGlobalScope(BulkScope::class)->where(['common_order' => $common_order])->get();
         }
 
     
         //CUSTOM
         if($bookings->isEmpty())
             abort('404');
 
         $booking = $bookings[0];
 
         // customer can see only their bookings
         if(Auth::user()->hasRole('customer'))
             if($booking['customer_id'] != Auth::id())
                 abort('404');
 
         // organiser can see only their events bookings
         if(Auth::user()->hasRole('organiser'))
             if($booking['organiser_id'] != Auth::id())
                 abort('404');
         
         $common_order = $booking['common_order'];
                 
         $file = public_path('/storage/invoices/'.$booking['customer_id'].'/'.$booking['common_order'].'-invoice.pdf');
         
         if(!\File::exists($file)) 
         {
             $img_path      = str_replace('https://', 'http://', url(''));
 
             $organizer     = User::where(['id' => $booking['organiser_id']])->first();
 
             //buyer
             $customer      = User::where(['id' => $booking['customer_id']])->first();
            
             // test
             // $img_path      = '';
             // return view('invoice.invoice', compact('booking', 'organizer', 'customer', 'img_path'));
             $pdf_html      = (string) \View::make('invoice.invoice', compact('bookings', 'organizer', 'customer', 'img_path'));
             
             $pdf_name      = 'invoices/'.$booking['customer_id'];
     
             $invoice       = new  InvoicesController;
             $file          = $invoice->generatePdf($pdf_html, $pdf_name, $booking);
         }
         
         return response()->download($file);
         
     }


}
