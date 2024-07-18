
<table class="table">
    <thead>
        <tr>
            <th scope="col">{{ __('eventmie-pro::em.order') }}</th>
            <th scope="col">{{ __('eventmie-pro::em.ticket') }}</th>
            <th scope="col">{{ __('eventmie-pro::em.price') }}</th>
            <th scope="col">{{ __('eventmie-pro::em.quantity') }}</th>
        </tr>
    </thead>
    <tbody>

        @php
            $bookings = Classiebit\Eventmie\Models\Booking::with(['attendees' => function ($query) {
                $query->where(['status' => 1]);
            }, 'attendees.seat'])->whereIn('id',collect($mail_data->mail_data)->pluck('id')->all() )->get();
            
            
        @endphp
        
        @foreach($bookings as $val)
        
        <tr>
            <th scope="row">{{$val['order_number']}}</th>
            <td>{{$val['ticket_title']}}</td>
            <td>{{$val['ticket_price']}} {{$val['currency']}}</td>
            <td>{{$val['quantity']}}</td>
            <td>
                @if($val->attendees->isNotEmpty())
                    <tr>
                        <th>@lang('eventmie-pro::em.name')</th>
                        <th>@lang('eventmie-pro::em.phone')</th>
                        <th>@lang('eventmie-pro::em.address')</th>
                        <th>@lang('eventmie-pro::em.seat')</th>
                    </tr>
                    @foreach($val->attendees as $key => $attendee)
                        <tr>
                            <td ><p>{{ucfirst($attendee['name'])}}</p></td>
                            <td ><p>{{ $attendee['phone'] }}</p></td>
                            <td ><p>{{ $attendee['address'] }}</p></td>
                            <td ><p>{{ !empty($attendee['seat']) ? $attendee['seat']['name'] : '' }}</p></td>
                            
                        </tr>
                    @endforeach    
                @endif
            </td>
        </tr>
      @endforeach
      
    </tbody>
  </table>
