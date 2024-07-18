<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style type="text/css" media="screen">
    * {
        padding: 0;
        margin: 0; 
        font-family: 'DejaVu Sans', sans-serif;
    }
    body {
        margin: 0 auto !important;
        padding: 0 !important;
        height: 100% !important;
        width: 100% !important;
        font-size: 14px;
        font-family: 'DejaVu Sans', sans-serif;
    }
    table {
        width: 95%;
        padding: 1px;
        margin: 0 auto !important;
        border-spacing: 0 !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
    }
    table table table {
        table-layout: auto;
    }
    table td {
        padding: 5px;
        font-size: 14px;
    }
    .center {
        text-align: center;
    }
    .text-left {
        text-align: left;
    }
    .text-right {
        text-align: right;
    }
    [dir=rtl] .text-right {
        text-align: left;
    }
    [dir=rtl] .text-left {
        text-align: right;
    }
    p {
        font-size: 18px;
        display: block;
    }
    .title-bar {
        padding: 0 !important;
    }
    .title-bar .s-heading {
        color: #797979;
        font-size: 14px;
        margin: 0 0 5px 0;
    }
    .title-bar .m-heading {
        color: #3c3c3c;
        font-size: 16px;
        margin: 0;
    }

    h4 {
        margin-top: 0;
        margin-bottom: 0.5rem;
    }

    p {
        margin-top: 0;
        margin-bottom: 1rem;
    }

    strong {
        font-weight: bolder;
    }

    img {
        vertical-align: middle;
        border-style: none;
    }

    table {
        border-collapse: collapse;
    }

    th {
        text-align: inherit;
        font-size: 12px;
    }

    h4, .h4 {
        margin-bottom: 0.5rem;
        font-weight: 500;
        line-height: 1.2;
    }

    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
    }

    .table th,
    .table td {
        padding: 0.75rem;
        vertical-align: top;
    }

    .table.table-items td {
        border-top: 1px solid #dee2e6;
    }

    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
    }

    .mt-5 {
        margin-top: 3rem !important;
    }

    .pr-0,
    .px-0 {
        padding-right: 0 !important;
    }

    .pl-0,
    .px-0 {
        padding-left: 0 !important;
    }

    .text-right {
        text-align: right !important;
    }

    .text-center {
        text-align: center !important;
    }

    .text-uppercase {
        text-transform: uppercase !important;
    }
    body, h1, h2, h3, h4, h5, h6, table, th, tr, td, p, div {
        line-height: 1.1;
    }
    .party-header {
        font-size: 14px;
        font-weight: 600;
    }
    .total-amount {
        font-size: 12px;
        font-weight: 700;
    }
    .border-0 {
        border: none !important;
    }
    .cool-gray {
        color: #6B7280;
    }
    .name-info p {
        font-size: 12px;
        line-height: 15px !important;
    }
</style>
</head>

<body {!! is_rtl() ? 'dir="rtl"' : '' !!}>
<!-- when testing  -->
<div style="max-width: 680px;margin: 0 auto;">
<!-- when generating  -->
{{-- <div> --}}
    
    {{-- Header --}}
    <div>
        <table style="padding: 0;margin: 0;width: 100%">
            <tr>
                <td class="title-bar"> 
                    <table style="padding: 0;margin: 0;width: 100%">
                        <tr>
                            <td style="width: 10%;">
                                <img src="{{ "data:image/png;base64,".base64_encode(file_get_contents(public_path('/storage/'.setting('site.logo')))) }}" style="width: 64px;">
                            </td>
                            <td style="width: 80%;">
                                <p class="m-heading">{{ (setting('site.site_name') ? setting('site.site_name') : config('app.name')) }}</p>
                                <p class="s-heading">{{ setting('site.site_slogan') }}</p>
                            </td>
                            <td style="width: 10%;">
                                <p class="m-heading" style="font-weight: 600;">@lang('eventmie-pro::em.invoice')</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    
    {{-- Seller - Buyer --}}
    <table class="table" style="margin-top: 10px !important;">
        <thead style="border-bottom: 1px solid #dee2e6;border-top: 1px solid #dee2e6;">
            <tr>
                <th class="border-0 pl-0 party-header" width="48.5%">@lang('eventmie-pro::em.sold_by')</th>
                <th class="border-0" width="3%"></th>
                <th class="border-0 pl-0 party-header"  style="float: right;">@lang('eventmie-pro::em.sold_to')</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-0 name-info">
                    <p><strong>{{ $organizer->seller_name }}</strong></p>
                    <p>{!! nl2br($organizer->seller_info) !!}</p>
                    <p>{!! nl2br($organizer->seller_tax_info) !!}</p>
                </td>
                <td class="border-0"></td>
                <td class="px-0 name-info" style="float: right;">
                    <p><strong>{{ $customer->name }}</strong></p>
                    <p>{{ $customer->email }}</p>
                    <p>{{ $customer->phone }}</p>
                    <p>{!! $customer->address !!}</p>
                    <p>{{ $customer->taxpayer_number }}</p>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Order number --}}
    <table class="table mt-5">
        <tbody>
            <tr>
                <td class="border-0 pl-0 name-info">
                    
                    <p>@lang('eventmie-pro::em.order_number') <strong>{{ $bookings[0]['common_order'] }}</strong></p>
                    <p>@lang('eventmie-pro::em.order_date') <strong>{{ \Carbon\Carbon::parse($bookings[0]['created_at'])->translatedFormat(format_carbon_date(true)) }}</strong></p>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Table --}}
    <table class="table table-items" style="border-bottom: 1px solid #dee2e6;"> 
        <thead>
            <tr>
                <th class="border-0 pl-0">@lang('eventmie-pro::em.invoice_desc')</th>
                <th class="text-center border-0">@lang('eventmie-pro::em.unit_price')</th>
                <th class="text-center border-0">@lang('eventmie-pro::em.unit_qty')</th>
                <th class="text-center border-0">@lang('eventmie-pro::em.invoice_net_amount')</th>
                <th class="text-center border-0">@lang('eventmie-pro::em.invoice_tax')</th>
                <th class="text-right border-0 pr-0">@lang('eventmie-pro::em.reward')</th>
                <th class="text-right border-0 pr-0">@lang('eventmie-pro::em.invoice_total_amount')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings->groupBy('ticket_id') as $items)
                
                <tr>
                    <td class="pl-0 name-info">
                        <p>{{ $items[0]['event_title'] }}</p>
                        <p>{{ $items[0]['ticket_title'] }}</p>
                        <p>
                        @foreach($items as $key => $booking)
                            
                            @if($booking['attendees']->isNotEmpty())
                                @foreach($booking['attendees'] as $key => $attendee)
                                {{ !empty($attendee['seat']) ? ' | '.$attendee['seat']['name'].' | ' : '' }}
                                @endforeach    
                            @endif
                        @endforeach    
                        </p>
                    </td>
                    
                    <td class="text-center">{{ $booking['ticket_price'] }} {{ $items[0]['currency'] }}</td>
                    <td class="text-center">{{ $items->count() }}</td>
                    <td class="text-center">{{ $items->sum('price') }} {{ $items[0]['currency'] }}</td>
                    <td class="text-center">{{ $items->sum('tax') }} {{ $items[0]['currency'] }}</td>
                    <td class="text-center">{{ $items->sum('promocode_reward') }} {{ $items[0]['currency'] }}</td>
                    <td class="text-right pr-0">{{ $items->sum('net_price') }} {{ $items[0]['currency'] }}</td>
                </tr>
                
            @endforeach
            <tr>
                <td colspan="5" class="border-0"></td>
                <td class="text-right pl-0">@lang('eventmie-pro::em.invoice_total')</td>
                <td class="text-right pr-0 total-amount">{{ $bookings->sum('net_price') }} {{ $bookings[0]['currency'] }}</td>
            </tr>
            
        </tbody>
    </table>

    @if($organizer->seller_signature)
    <table style="padding: 0;margin: 0;margin-top: 10px !important;margin-bottom: 10px !important;width: 100%;border-bottom: 1px solid #dee2e6;">
        <tr>
            <td class="title-bar"> 
                <table style="padding: 0;margin: 0;width: 100%">
                    <tr>
                        <td style="float: right;">
                            <img src="{{ "data:image/png;base64,".base64_encode(file_get_contents(public_path('/storage/'.$organizer->seller_signature))) }}" style="width: 64px;text-align: center;margin-bottom: 5px !important;">
                            <p class="m-heading" style="font-size: 12px;font-weight: 600;">@lang('eventmie-pro::em.seller_signature')</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    @endif
    
    <p style="margin-top: 10px; font-size: 11px;">{{ $organizer->seller_note }}</p>
    
</div>
    
</body>
</html>
