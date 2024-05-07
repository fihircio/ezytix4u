<template>
    <div>
        <div v-if="Object.keys(booking).length > 0">
            <div class="card-header py-3">
                <h4 class="card-title mb-0 text-center h4 text-black">{{ booking.event_title }}</h4>
            </div>
            <table class="table mb-0">
                <tbody>
                    <tr>
                        <th class="small">{{ trans('em.customer') }}</th>
                        <td class="small">{{ booking.customer_name }} <small>{{booking.customer_email}}</small></td>
                    </tr>
                    <tr>
                        <th class="small">{{ trans('em.booked_on') }}</th>
                        <td class="small">{{ moment(convert_date_to_local(booking.created_at)).format(date_format.vue_date_format) }}</td>
                    </tr>   
                    <tr>
                        <th class="small">{{ trans('em.payment') }}</th>
                        <td class="small fw-bold">
                            {{ booking.payment_type }} / 
                            <small class="text-success" v-if="booking.is_paid">{{ trans('em.paid') }}</small>
                            <small class="text-danger" v-else>{{ trans('em.unpaid') }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th class="small">{{ trans('em.ticket') }}</th>
                        <td class="small fw-bold">{{ booking.ticket_title }} <strong>{{ ' x '+booking.quantity }}</strong></td>
                    </tr>
                    <tr>
                        <th class="small">{{ trans('em.order') }}</th>
                        <td class="small">{{ booking.order_number }}</td>
                    </tr>
                    <tr>
                        <th class="small">{{ trans('em.checked_in') }}</th>
                        <td class="small fw-bold">
                            <span class="badge bg-success" v-if="booking.checked_in > 0">{{ trans('em.yes') }}</span>
                            <span class="badge bg-secondary" v-else>{{ trans('em.no') }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- <div v-if="booking.checked_in > 0" class="alert alert-danger"><h5><i class="fas fa-hand"></i> {{ trans('em.already_cheked_in') }}</h5></div> -->
        </div>

        <div v-else>
            <div class="alert alert-info"><h5><i class="fas fa-qrcode"></i> {{ trans('em.scan_ticket') }}</h5></div>
        </div>

        <div class="alert alert-danger"  v-if="errorMessage != '' "><i class="fas fa-camera"></i> {{ errorMessage }}</div>

        <qrcode-stream :camera="camera" v-if="hide_scanner <= 0" @decode="onDecode" @init="onInit"></qrcode-stream>
        
        
           
    </div>
    
</template>

<script>

import mixinsFilters from '../../mixins.js';

export default {

    mixins:[
        mixinsFilters
    ],

    props: ['date_format'],
    
    data() {
        return {
            moment     : moment,
            decodedContent  : '',
            errorMessage    : '',
            hide_scanner    : 0,
            booking         : [],
            camera: 'auto',
        }
    },

    methods: {
        onDecode(content) {
            this.decodedContent = JSON.parse(content);  
            if(Object.keys(this.decodedContent).length > 0)
                this.getBooking();
        },

        getBooking() {
            let post_url = route('eventmie.get_booking') ;
            let post_data = {
                'id'            : this.decodedContent.id,
                'order_number'  : this.decodedContent.order_number,
            };
            axios.post(post_url, post_data)
            .then(res => {
                this.booking   = res.data.booking;
                
                this.VerifyTicket();
            })
            .catch(error => {
            
                this.camera = 'off';
                let serrors = Vue.helpers.axiosErrors(error);
                if (serrors.length) {
                    this.serverValidate(serrors);
                }
                let _this = this;
                setTimeout(()=> {
                    _this.camera = 'auto';
                },2000);
            });
        },

        onInit(promise) {
            promise.then(() => {
                console.log('Successfully initilized! Ready for scanning now!')
            })
            .catch(error => {
                if (error.name === 'NotAllowedError') {
                    this.errorMessage = trans('em.camera_access_required');
                } else if (error.name === 'NotFoundError') {
                    this.errorMessage = trans('em.camera_not_detected');
                } else if (error.name === 'NotSupportedError') {
                    this.errorMessage = trans('em.camera_https_required');
                } else if (error.name === 'NotReadableError') {
                    this.errorMessage = trans('em.camera_not_detected');
                } else if (error.name === 'OverconstrainedError') {
                    this.errorMessage = trans('em.camera_not_detected');
                } else {
                    this.errorMessage = trans('em.camera_not_detected');
                }
            })
        },

        // verify ticket after qr code scanner
        VerifyTicket() {
            document.getElementById("booking_id").value                 = this.booking.id; 
            document.getElementById("order_number").value               = this.booking.order_number; 
            document.getElementById('check_in_button').style.display    = 'block';

            this.hide_scanner = 1;
        },

    },
}
</script>
