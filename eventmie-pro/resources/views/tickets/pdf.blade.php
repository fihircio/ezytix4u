<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<style>
    * {
        padding: 0;
        margin: 0;
        font-family: 'DejaVu Sans', sans-serif;
    }

    body {
        height: 100% !important;
        width: 100% !important;
        font-size: 14px;
        font-family: 'DejaVu Sans', sans-serif;
    }

    #bottom_table {
        max-width: auto;
    }

    #bottom_table tr {
        text-align: center;
    }

    #bottom_table td {
        padding: 5px 10px !important;
    }

    table {
        width: 100%;
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
        word-wrap: break-word;
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
        font-weight: 600;
    }
    .label {
        color: #797979;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 600;
        margin-top: 15px;
    }
    .value-data {
        color: #000;
        font-size: 20px;
    }
    .divider {
        color: #797979;
        border: 1px dashed #797979;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .text-capitalize {
        text-transform: capitalize;
    }
    .text-large {
        font-size: 20px;
        font-weight: 600;
    }
    .text-small {
        font-size: 16px;
    }
    .row-divide {
        padding-bottom: 15px;
    }
    .mt-0 {
        margin-top: 0px !important;
    }
</style>
</head>
<body {!! is_rtl() ? 'dir="rtl"' : '' !!}>
    <!-- when testing  -->
    @if(!$img_path)
    <div style="max-width: 600px;margin: 0 auto;">
    @else
    <!-- when generating  -->
    <div>
    @endif

        <!-- 1. Branding -->
        <div>
            <table>
                <tr>
                    <td class="title-bar center">
                        <table>
                            <tr>
                                <td style="padding-top: 10px;text-align: center;" class="text-center">
                                    <img src="{{ "data:image/png;base64,".base64_encode(file_get_contents(public_path('/storage/'.setting('site.logo')))) }}" style="width: 64px;">
                                    <p class="m-heading">
                                        {{ setting('site.site_name') ? setting('site.site_name') : config('app.name') }}
                                    </p>
                                    <p class="s-heading">{{ setting('site.site_slogan') }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Divider -->
        <div>
            <table>
                <tr>
                    <td>
                        <hr class="divider" style="margin-top: 0;">
                    </td>
                </tr>
            </table>
        </div>

        <!-- 2. QrCode -->
        <div>
            <table>
                
                <tr>
                    <td style="text-align: center;padding-top: 5px;">
                        @php $qrcode = $booking['customer_id'].'/'.$booking['id'].'-'.$booking['order_number'].'.png'; @endphp
                        <img src="{{ "data:image/png;base64,".base64_encode(file_get_contents(public_path('/storage/qrcodes/'.$qrcode))) }}" style="width: 50%;">
                    </td>
                </tr>
            </table>
        </div>

        <!-- Divider -->
        <div>
            <table>
                <tr>
                    <td>
                        <hr class="divider">
                    </td>
                </tr>
            </table>
        </div>

        <!-- 1. Event details -->
        <div>
            <table>
                <tr>
                    <td style="padding: 15px 25px 15px 25px;">
                        <div>
                            <table>
                                <tr>
                                    <td class="row-divide">
                                        <p class="label mt-0">@lang('eventmie-pro::em.customer')</p>
                                        <p class="value-data text-capitalize text-large">{{ ucfirst($booking['customer_name']) }}</p>
                                        <p class="value-data">#{{ $booking['order_number'] }}</p>
                                        
                                        <p class="label">@lang('eventmie-pro::em.ticket')</p>
                                        <p class="value-data">
                                            {{ $booking['ticket_title'] }} <strong> x {{ $booking['quantity'] }}</strong> &nbsp;
                                            @if (!empty($booking['attendees'][0]['seat'])) ({{ $booking['attendees'][0]['seat'] }}) @endif
                                        </p>

                                        <p class="label">@lang('eventmie-pro::em.timings')</p>
                                        <p class="value-data">
                                            {{ userTimezone($booking['event_start_date'] . ' ' . $booking['event_start_time'], 'Y-m-d H:i:s', format_carbon_date(false)) }} -<br>
                                            {{ userTimezone($booking['event_end_date'] . ' ' . $booking['event_end_time'], 'Y-m-d H:i:s', format_carbon_date(false)) }}

                                            {{ showTimezone() }}
                                        </p>

                                        <p class="label">@lang('eventmie-pro::em.venue')</p>
                                        <p class="value-data">{{ ucfirst($event->venue) }} | {{ ucfirst($event->address) }}</p>
                                    </td>
                                    <td class="row-divide">
                                        <p class="label" style="margin-top: 0px;margin-left: 78px;float: left;display: inline-block; font-size: 10px;text-transform: capitalize;padding-bottom: 5px;">{{ $event->title }}</p><br>
                                        <img style="width: 80%;border-radius: 12px;float: right;margin-top: 5px;" src="{{ "data:image/png;base64,".base64_encode(file_get_contents(public_path('/storage/'.$event->thumbnail))) }}">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>

                </tr>

            </table>
        </div>

        <!-- Divider -->
        <div>
            <table>
                <tr>
                    <td>
                        <hr class="divider">
                    </td>
                </tr>
            </table>
        </div>

    </div>


</body>
</html>