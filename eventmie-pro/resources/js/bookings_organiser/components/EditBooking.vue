<template>
    <div class="custom_model">
        <div class="modal show" v-if="openModal">
            <div class="modal-dialog modal-lg w-100">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-3">{{ trans('em.update_booking_status') }}</h1>
                        <button type="button" class="btn btn-sm bg-danger text-white close" data-bs-dismiss="modal" aria-label="Close" @click="close()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form ref="form" @submit.prevent="validateForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" class="form-control"  name="customer_id" v-model="booking.customer_id">
                            <input type="hidden" class="form-control"  name="event_id" v-model="booking.event_id">
                            <input type="hidden" class="form-control"  name="booking_id" v-model="booking.id">
                            <input type="hidden" class="form-control"  name="ticket_id" v-model="booking.ticket_id">

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('em.booking_cancellation') }}</label>
                                    <select  class="form-control"  name="booking_cancel"  v-model="booking_cancel" v-validate="'required'">
                                        <option  value="0">{{ trans('em.no_cancellation') }} </option>
                                        <option  value="1">{{ trans('em.cancellation_pending') }} </option>
                                        <option  value="2">{{ trans('em.cancellation_approved') }} </option>
                                        <option  value="3">{{ trans('em.amount_refunded') }} </option>
                                    </select>
                                    <span v-show="errors.has('booking_cancel')" class="help text-danger">{{ errors.first('booking_cancel') }}</span>
                                </div>
                                    
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form-label">{{ trans('em.booking_status') }}</label>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="radio-inline">
                                            <input type="radio" value="1" name="status" v-model="status"> {{ trans('em.enable') }}
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" value="0" name="status" v-model="status"> {{ trans('em.disable') }}
                                        </label>
                                    </div>
                                    <span v-show="errors.has('status')" class="help text-danger">{{ errors.first('status') }}</span>
                                </div>
                                    
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form-label">{{ trans('em.booking_paid') }} </label>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="radio-inline">
                                            <input type="radio" value="1" name="is_paid" v-model="is_paid"> {{ trans('em.yes') }}
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" value="0" name="is_paid" v-model="is_paid"> {{ trans('em.no') }}
                                        </label>
                                    </div>
                                    <span v-show="errors.has('is_paid')" class="help text-danger">{{ errors.first('is_paid') }}</span>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-sd-card"></i> {{ trans('em.save') }}</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>


import mixinsFilters from '../../mixins.js';

export default {
    props: ["booking"],

     mixins:[
        mixinsFilters
    ],

    data() {
        return {
            
            openModal: false,
            
            booking_cancel       : null,
            status               : null,
            is_paid              : 0,
           
        }
    },
    methods: {
        // reset form and close modal
        close: function () {    
            this.booking_cancel = null;
            this.status         = null;
       
            this.$refs.form.reset();

            this.openModal          = false;
            this.$parent.edit_index = null;
        },

        editBooking() {
            this.booking_cancel     = this.booking.booking_cancel;
            this.status             = this.booking.status;
            this.is_paid            = this.booking.is_paid;
           
        },
        
        
        // validate data on form submit
        validateForm(event) {
            this.$validator.validateAll().then((result) => {
                if (result) {
                    this.formSubmit(event);            
                }
            });
        },

        // show server validation errors
        serverValidate(serrors) {
            this.$validator.validateAll().then((result) => {
                this.$validator.errors.add(serrors);
            });
        },

        // submit form
        formSubmit(event) {

            // prepare form data for post request
            let post_url = route('eventmie.obookings_organiser_bookings_edit');
            let post_data = new FormData(this.$refs.form);
            
            // axios post request
            axios.post(post_url, post_data)
            .then(res => {
                // on success
                // use vuex to update global sponsors array
                // close form
                this.close();
                this.updateItem();
                if(res.data.status)
                    Vue.helpers.showToast('success', trans('em.booking_updat_success'));
            
            })
            .catch(error => {
                let serrors = Vue.helpers.axiosErrors(error);
                if (serrors.length) {
                    this.serverValidate(serrors);
                }
            });
        },

        updateItem() {
            this.$emit('changeItem');
        }
    },
    mounted() {
        if(this.booking) {
            this.editBooking();
            this.openModal = true;
        }
    },
}
</script>