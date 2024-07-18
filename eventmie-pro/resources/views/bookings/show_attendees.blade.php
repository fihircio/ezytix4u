@if($booking->attendees->isNotEmpty())

<div class="row">
    <div class="col-md-12">
        <h3>@lang('eventmie-pro::em.attendees')</h3>
        <table class="table table-striped table-hover">
            <tr>
                <th>@lang('eventmie-pro::em.name')</th>
                <th>@lang('eventmie-pro::em.phone')</th>
                <th>@lang('eventmie-pro::em.address')</th>
                <th>@lang('eventmie-pro::em.seat')</th>
            </tr>
            @foreach ($booking->attendees as $attendee)
                <tr>
                    <td>{{$attendee->name}}</td>
                    <td>{{$attendee->phone}}</td>
                    <td>{{$attendee->address}}</td>
                    <td>{{ !empty($attendee['seat']) ? $attendee['seat']['name'] : '' }}</td>
                </tr>      
            @endforeach
          

        </table>
    </div>
</div>
    
    

@endif