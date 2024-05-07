<template>
    <div class="custom_model">
        <div class="modal show" v-if="openModal">
            <div class="modal-dialog modal-xl model-extra-large">
                
                <div class="modal-content">
                    <div class="modal-header text-center border-0">
                        <div class="modal-title w-100">
                            <div>
                                <h5 class="mb-0 h3">{{ event.title }}</h5>

                                <ul class="list-group list-group-horizontal list-group-flush justify-content-center">
                                    <li class="list-group-item text-sm border-0 px-0 pe-sm-3">
                                        <small v-if="event.online_location"><i class="fas fa-signal text-primary"></i> {{ trans('em.online') }}</small>
                                        <small v-else><i class="fas fa-location-dot text-primary"></i> {{ event.venue }}</small>
                                        &nbsp;
                                    </li>
                                    <li class="list-group-item text-sm border-0 px-0 pe-sm-3">
                                        <small><i class="fas fa-calendar-day text-primary"></i> {{ serverTimezone(moment(booking_date).format('dddd LL')+' '+start_time, 'dddd LL HH:mm a').locale('en').format('Y-MM-DD') }}</small>
                                        &nbsp;
                                    </li>
                                    <li class="list-group-item text-sm border-0 px-0 pe-sm-3">
                                        <small v-if="event.repetitive > 0"><i class="fas fa-clock text-primary"></i> {{ start_time }} {{ showTimezone() }}</small>
                                        <small v-else><i class="fas fa-clock text-primary"></i> {{ changeTimeFormat(start_time) }} {{ showTimezone() }}</small>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <button type="button" class="btn-close bg-danger" data-bs-dismiss="modal" aria-label="Close" @click="close()"></button>
                    </div>
                    
                    <div class="modal-body">
                        <form ref="form" @submit.prevent="validateForm" method="POST" >
                            
                            <input type="hidden" class="form-control" name="event_id" :value="tickets[0].event_id" >
                            <input type="hidden" class="form-control" name="booking_date" :value="serverTimezone(booking_date+' '+start_time, 'dddd LL HH:mm a').format('YYYY-MM-DD')" >

                            <input type="hidden" class="form-control" name="booking_end_date" 
                                :value="(booking_end_date != null && typeof(booking_end_date) != 'undefined' && booking_end_date != false) ? 
                                serverTimezone(moment(booking_date).format('dddd LL')+' '+end_time, 'dddd LL HH:mm a').locale('en').format('YYYY-MM-DD') : null" 
                                v-if="!event.merge_schedule"
                            >
                            <input type="hidden" class="form-control" name="booking_end_date" 
                                :value="(booking_end_date != null && typeof(booking_end_date) != 'undefined' && booking_end_date != false) ? 
                                serverTimezone(moment(booking_end_date).format('dddd LL')+' '+end_time, 'dddd LL HH:mm a').locale('en').format('YYYY-MM-DD') : null" 
                                v-else
                            >

                            <input type="hidden" class="form-control" name="start_time" :value="serverTimezone(booking_date+' '+start_time, 'dddd LL HH:mm a').format('HH:mm:ss')" >
                            <input type="hidden" class="form-control" name="end_time" :value="serverTimezone((booking_end_date == null ? booking_date : booking_end_date) +' '+end_time, 'dddd LL HH:mm a').format('HH:mm:ss')" >
                            <input type="hidden" class="form-control" name="merge_schedule" :value="event.merge_schedule" >
                            <input type="hidden" name="customer_id" v-model="customer_id" v-validate="'required'" >

                            <div class="px-lg-3">
                                <!-- only for admin & organizer -->
                                <div class="row" v-if="is_customer <= 0">
                                    <div class="col-md-4 mb-3">
                                        <div v-if="is_customer <= 0">
                                            <label class="form-label h6" for="customer_id">{{ trans('em.select_customer') }}</label>
                                            <v-select 
                                                label="name" 
                                                class="form-control px-0 py-0 border-0 mb-2" 
                                                :placeholder="trans('em.search_customer_email')"
                                                v-model="customer" 
                                                :required="!customer" 
                                                :filterable="false" 
                                                :options="options" 
                                                @search="onSearch" 
                                            ><div slot="no-options">{{ trans('em.customer_not_found') }}</div></v-select>
                                            <div class="invalid-feedback danger" v-show="errors.has('customer_id')">{{ errors.first('customer_id') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tickets -->
                                <div class="row">
                                    <div class="col-12">
                                        <p class="mb-2 h6">{{ trans('em.select_tickets') }}</p>
                                        <ul class="list-group">
                                            <li class="list-group-item mb-3 rounded border-2"
                                                v-for="(item, index) in tickets" 
                                                :key = "index"
                                            >
                                                <input type="hidden" class="formbh g-control" name="ticket_id[]" :value="item.id" >
                                                <input type="hidden" class="form-control" name="ticket_title[]" :value="item.title" >
                                                
                                                <div class="d-flex justify-content-between lh-condensed d-flex-wrap">
                                                    <div class="w-40">
                                                        <h6 class="my-0"><strong>{{ item.title }}</strong></h6>
                                                        <p class="my-0 h6">{{ item.price > 0 ? item.price : '0.00' }} <small>{{currency}}</small></p>
                                                    </div>
                                                    
                                                    <div class="w-20">
                                                        <!-- Live stock alert  -->
                                                        <!-- if any booked tickets -->
                                                        <div v-if='typeof(booked_tickets[item.id+"-"+booked_date_server]) != "undefined"
                                                        '>
                                                            
                                                            <select class="form-select border-2 form-select-lg" 
                                                                name="quantity[]" 
                                                                v-model="quantity[index]"
                                                                v-if='(item.customer_limit != null ? item.customer_limit :  max_ticket_qty) <= 100'
                                                            >
                                                                <option value="0" selected>0</option>
                                                                <option :key="ind"
                                                                    v-if="booked_tickets[item.id+'-'+booked_date_server].total_vacant <= (item.customer_limit != null ? item.customer_limit :  max_ticket_qty)" 
                                                                    :value="itm" v-for=" (itm, ind) in booked_tickets[item.id+'-'+booked_date_server].total_vacant"  
                                                                >{{itm }}</option>
                                                                <option v-else :value="itm" v-for=" (itm, ind) in (item.quantity > (item.customer_limit != null ? item.customer_limit :  max_ticket_qty) ? parseInt(item.customer_limit != null ? item.customer_limit :  max_ticket_qty) : parseInt(item.quantity))"  :key="ind">{{itm }}</option>
                                                            </select>
                                                            <input v-else type="number" name="quantity[]" 
                                                                v-model="quantity[index]" value="0" class="form-control form-input-sm" 
                                                                min="0" :max="booked_tickets[item.id+'-'+booked_date_server].total_vacant < (item.customer_limit != null ? item.customer_limit :  max_ticket_qty) ? booked_tickets[item.id+'-'+booked_date_server].total_vacant : (item.customer_limit != null ? item.customer_limit :  max_ticket_qty)"
                                                            >
                                                            <!-- Show if vacant less than max_ticket_qty -->
                                                            <p class="text-muted" 
                                                                v-if="booked_tickets[item.id+'-'+booked_date_server].total_vacant < (item.customer_limit != null ? item.customer_limit :  max_ticket_qty) && booked_tickets[item.id+'-'+booked_date_server].total_vacant > 0">
                                                                <small><i class="fas fa-exclamation"></i> {{ trans('em.vacant') }} 
                                                                {{ booked_tickets[item.id+'-'+booked_date_server].total_vacant }}</small>
                                                            </p>
                                                            <p class="text-danger" 
                                                                v-if="booked_tickets[item.id+'-'+booked_date_server].total_vacant < (item.customer_limit != null ? item.customer_limit :  max_ticket_qty) && booked_tickets[item.id+'-'+booked_date_server].total_vacant <= 0">
                                                                <small><i class="fas fa-times-circle"></i>  {{ trans('em.vacant') }} 0</small>
                                                            </p>
                                                        </div>
                                                        <div v-else>
                                                            <select class="form-select border-2 form-select-lg" 
                                                                name="quantity[]" 
                                                                v-model="quantity[index]"
                                                                v-if="(item.customer_limit != null ? item.customer_limit :  max_ticket_qty) <= 100"
                                                            >
                                                                <option value="0" selected>0</option>
                                                                <option :value="itm" v-for=" (itm, ind) in item.quantity > (item.customer_limit != null ? item.customer_limit :  max_ticket_qty) ? parseInt((item.customer_limit != null ? item.customer_limit :  max_ticket_qty)) : parseInt(item.quantity)"  :key="ind">{{itm }}</option>
                                                            </select>
                                                            <input v-else type="number" name="quantity[]" v-model="quantity[index]" value="0" class="form-control form-input-sm" min="0" :max="item.quantity > (item.customer_limit != null ? item.customer_limit :  max_ticket_qty) ? parseInt((item.customer_limit != null ? item.customer_limit :  max_ticket_qty)) : parseInt(item.quantity)">
                                                            <!-- Show if vacant less than max_ticket_qty -->
                                                            <p class="text-primary h6" 
                                                                v-if="item.quantity < (item.customer_limit != null ? item.customer_limit :  max_ticket_qty) && item.quantity > 0">
                                                                <small><i class="fas fa-exclamation"></i> {{ trans('em.vacant') }} 
                                                                {{ item.quantity }}</small>
                                                            </p>
                                                            <p class="text-danger" 
                                                                v-if="item.quantity <= 0">
                                                                <small><i class="fas fa-times-circle"></i>  {{ trans('em.vacant') }} 0</small>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <strong>
                                                            {{ total_price[index] ? total_price[index] : '0.00' }}
                                                            <small>{{currency}}</small>
                                                        </strong>
                                                        <span v-if="quantity[index] > 0"><i class="fas fa-check-circle text-success"></i></span>
                                                    </div>
                                                </div>

                                                <!-- show tax only if quantity is set -->
                                                <div class="break-flex w-30 w-m-100">
                                                    <ul class="list-group list-group-flush my-2" v-if="quantity[index] > 0 && item.price > 0 && item.taxes.length > 0">
                                                        <li class="list-group-item small px-2 p-1 text-muted" v-for="(tax, index1) in item.taxes" :key ="index1">
                                                            <span>{{ tax.title }} 
                                                                <small>{{ total_price[index] > 0 ? countTax(item.price, tax.rate, tax.rate_type, tax.net_price, quantity[index]) : 0 }}</small>
                                                            </span>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <div class="break-flex">
                                                    <!-- hide/show below ticket description -->
                                                    <a class="pointer ticket-info-toggle small" @click="ticket_info = !ticket_info">
                                                        <small v-if="ticket_info">{{ trans('em.hide_info') }}</small>
                                                        <small v-else>{{ trans('em.show_info') }}</small>
                                                    </a>
                                                    <p class="ticket-info small text-muted" v-if="ticket_info">{{ item.description }}</p>
                                                </div>
                                                
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <p class="mb-2 h6">{{ trans('em.cart') }}</p>
                                        <ul class="list-group">
                                            <li class="list-group-item mb-3 rounded border-2">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="my-0"><strong>{{ trans('em.total_tickets') }}</strong></h6>
                                                    <strong :class="{'ticket-selected-text': bookedTicketsTotal() > 0 }">{{ bookedTicketsTotal() }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="my-0"><strong>{{ trans('em.total_order') }}</strong></h6>
                                                    <strong :class="{'ticket-selected-text': bookedTicketsTotal() > 0 }">{{ total }} <small>{{currency}}</small></strong>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- If not logged in -->
                                <div class="row" v-if="!login_user_id">
                                    <div class="col-12">
                                        <div class="w-100 mb-3" >
                                            <div class="alert alert-danger">
                                                {{ trans('em.please_login_signup') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment -->
                                <div class="row" v-if="bookedTicketsTotal() > 0 && login_user_id">
                                    <div class="col-12">
                                        <p class="mb-2 h6">{{ trans('em.payment') }}</p>
                                        <div class="border-1 list-group-flush px-2">
                                            <!-- Free -->
                                            <div class="d-block my-3 pl-3" v-if="total <= 0">
                                                <div class="radio-inline">
                                                    <input id="free_order" name="free_order" type="radio" class="custom-control-input" checked>
                                                    <label class="custom-control-label" for="free_order"> &nbsp;<i class="fas fa-glass-cheers"></i> {{ trans('em.free') }} <small>({{ trans('em.rsvp') }} )</small></label>
                                                </div>
                                            </div>

                                            <!-- Paid -->
                                            <div class="d-block my-3 pl-3" v-else>
                                                
                                                <!-- For Organizer & Customer -->
                                                <div class="radio-inline" v-if="is_admin <= 0 && is_paypal > 0">
                                                    <input type="radio" class="custom-control-input" id="payment_method_paypal" name="payment_method" v-model="payment_method" value="1" >
                                                    <label class="custom-control-label" for="payment_method_paypal"> &nbsp;<i class="fab fa-paypal"></i> PayPal</label>
                                                </div>

                                                <div class="radio-inline" v-if="is_admin <= 0 && is_usaepay > 0">
                                                    <input type="radio" class="custom-control-input" id="payment_method_usaepay" name="payment_method" v-model="payment_method" value="9" >
                                                    <label class="custom-control-label" for="payment_method_usaepay"> &nbsp;<i class="fa-regular fa-credit-card"></i> USAePay</label>
                                                </div>
                                            
        

                                                <!-- For Admin & Organizer & Customer -->
                                                <div class="radio-inline" 
                                                    v-if="(is_organiser > 0 && is_offline_payment_organizer > 0) || (is_customer > 0 && is_offline_payment_customer > 0) || (is_admin > 0)">
                                                    
                                                    <input type="radio" class="custom-control-input" id="payment_method_offline" name="payment_method" v-model="payment_method" value="offline">

                                                    <label class="custom-control-label" for="payment_method_offline"> 
                                                        &nbsp;<i class="fas fa-suitcase-rolling"></i> {{ trans('em.offline') }} 
                                                        <small>({{ trans('em.cash_on_arrival') }})</small>
                                                    </label>

                                                </div>

                                                <p v-if="payment_method == 'offline'" class="text-dark h6"><strong>{{ trans('em.offline_payment_info') }}: </strong><small v-html="event.offline_payment_info"></small></p>

                                                <p class="text-mute h6 mt-5" v-html="trans('em.order_terms')"></p>
                                                
                                            </div>
                                        </div>    
                                    </div>
                                    <USAePay v-if="payment_method == 9"/>
                                    <div class="col-12 mt-2 pb-4">
                                        <div class="d-grid">
                                            <div class="btn-group btn-group-md btn-block  btn-group-justified">
                                                <button :class="{ 'disabled' : disable }" :disabled="disable" type="button" class="btn btn-success btn-lg btn-block" @click="bookTickets()"><i class="fas fa-cash-register"></i> {{ (total <= 0) ? trans('em.rsvp') : trans('em.checkout') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action -->
                                <div class="row">
                                    <div class="col-12">
                                        <div v-if="!login_user_id">
                                            <div class="d-grid pb-4">
                                                <div class="btn-group btn-group-md btn-block  btn-group-justified">
                                                    <button type="button" class="btn btn-primary btn-lg" @click="loginFirst()"><i class="fas fa-fingerprint"></i> {{ trans('em.login') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                        </form>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>

import { mapState, mapMutations} from 'vuex';
import mixinsFilters from '../../mixins.js';
import _ from 'lodash';
import USAePay from './USAePay.vue';

export default {
    
    mixins:[
        mixinsFilters
    ],

    components: {
        USAePay,
    },
    props : [
        'tickets', 
        'max_ticket_qty', 
        'event', 
        'currency', 
        'login_user_id',
        'is_admin',
        'is_organiser',
        'is_customer',
        'is_paypal',
        'is_offline_payment_organizer',
        'is_offline_payment_customer',
        'booked_tickets',
        'is_usaepay'
    ],

    data() {
        return {
            openModal           : false,
            ticket_info         : false,    
            moment              : moment,
            quantity            : [1],
            price               : null,
            total_price         : [],
            customer_id         : 0,
            total               : 0,   
            disable             : false,
            payment_method      : 'offline',   
            
            // customers options
            options             : [],
            //selected customer
            customer            : null,

            cardName    : "",
            cardNumber  : "",
            cardMonth   : "",
            cardYear    : "",
            cardCvv     : "",
        }
    },

    computed: {
        // get global variables
        ...mapState( ['booking_date', 'start_time', 'end_time', 'booking_end_date', 'booked_date_server']),
    },
   
    methods: {
        // update global variables
        ...mapMutations(['add', 'update']),

        // reset form and close modal
        close: function () {    
            this.price          = null;
            this.quantity       = [];
            this.total_price    = [];
            
            this.add({
                booking_date        : null,
                booked_date_server  : null,
                booking_end_date    : null,
                start_time          : null,
                end_time            : null,
            })
            
            
            this.openModal      = false;
        },

        bookTickets(){
            // show loader
            this.showLoaderNotification(trans('em.processing'));

            // prepare form data for post request
            this.disable = true;

            let post_url = route('eventmie.bookings_book_tickets');
            let post_data = new FormData(this.$refs.form);
            
            // axios post request
            axios.post(post_url, post_data)
            .then(res => {
                
                if(res.data.status && res.data.message != ''  && typeof(res.data.message) != "undefined") {
                   
                    // hide loader
                    Swal.hideLoading();

                    // close popup
                    this.close();
                    this.showNotification('success', res.data.message);
                    
                }
                else if(!res.data.status && res.data.message != '' && res.data.url != ''  && typeof(res.data.url) != "undefined"){
                    
                    // hide loader
                    Swal.hideLoading();
                    
                    // close popup
                    this.close();
                    this.showNotification('error', res.data.message);
                    
                    setTimeout(() => {
                        window.location.href = res.data.url;    
                    }, 1000);
                }

                if(res.data.url != '' && res.data.status  && typeof(res.data.url) != "undefined") {
                    
                    // hide loader
                    Swal.hideLoading();

                    setTimeout(() => {
                        window.location.href = res.data.url;    
                    }, 1000);
                }

                if(!res.data.status && res.data.message != ''  && typeof(res.data.message) != "undefined") {
                   
                    // hide loader
                    Swal.hideLoading();

                    // close popup
                    this.close();
                    this.showNotification('error', res.data.message);
                    
                }

            })
            .catch(error => {
                this.disable = false;
                let serrors = Vue.helpers.axiosErrors(error);
                if (serrors.length) {
                    
                    this.serverValidate(serrors);
                    
                }
            });
        },
        

        // validate data on form submit
        validateForm(e) {
            this.$validator.validateAll().then((result) => {
                if (result) {
                    this.disable = true;
                    this.formSubmit(e);
                }
                else{
                    this.disable = false;
                }
            });
        },

        // show server validation errors
        serverValidate(serrors) {
            this.disable = false;
            this.$validator.validateAll().then((result) => {
                this.$validator.errors.add(serrors);
            });
        },

        
        // count total tax
        countTax(price, tax, rate_type, net_price, quantity) {
            
            price           = parseFloat(price).toFixed(2);
            tax             = parseFloat(tax).toFixed(2);
            var total_tax   = parseFloat(quantity * tax).toFixed(2);
            
            
                // in case of percentage
                if(rate_type == 'percent')
                {
                    if(isNaN((price * total_tax)/100))
                        return 0;
                    
                    total_tax = (parseFloat((price*total_tax)/100)).toFixed(2); 

                    if(net_price == 'excluding')
                        return total_tax+' '+this.currency+' ('+tax+'%'+' '+trans('em.exclusive')+')';
                    else
                        return total_tax+' '+this.currency+' ('+tax+'%'+' '+trans('em.inclusive')+')';
                }    

                // for fixed tax
                if(rate_type == 'fixed')
                {
                    if(net_price == 'excluding')
                        return total_tax+' '+this.currency+' ('+tax+' '+this.currency+' '+trans('em.exclusive')+')';
                    else
                        return total_tax+' '+this.currency+' ('+tax+' '+this.currency+' '+trans('em.inclusive')+')';
                }        
                
            return 0;        
        },

        // count total price
        totalPrice(){
            if(this.quantity != null || this.quantity.length > 0)
            {
                let amount;
                let tax;
                let total_tax ;
                this.quantity.forEach(function(value, key) {
                    total_tax               = 0;
                    this.total_price[key]   = [];
                    
                    amount                  = (parseFloat(value * this.tickets[key].price)).toFixed(2);

                    // when have no taxes set set total_price with actual ammount without taxes
                    if(Object.keys(this.total_price).length > 0)
                    {
                        this.total_price.forEach(function(v, k){

                            if(Object.keys(v).length <= 0);
                                this.total_price[key] = amount;
                                
                        }.bind(this))
                    }
                    if(this.tickets[key].taxes.length > 0 && amount > 0) {

                        this.tickets[key].taxes.forEach(function(tax_v, tax_k) {
                                    // in case of percentage
                            if(tax_v.rate_type == 'percent')
                            {
                                // in case of excluding
                                if(tax_v.net_price == 'excluding')
                                {
                                    tax = isNaN((amount * tax_v.rate)/100) ? 0 : (parseFloat((amount*tax_v.rate)/100)).toFixed(2);
                                
                                    total_tax   =  parseFloat(total_tax) + parseFloat(tax);
                                }
                            }

                            // // in case of percentage
                            if(tax_v.rate_type == 'fixed')
                            {
                                tax   = parseFloat(value *tax_v.rate);
                                
                                // // in case of excluding
                                if(tax_v.net_price == 'excluding')
                                    total_tax   = parseFloat(total_tax) + parseFloat(tax);

                            }
                            
                        }.bind(this))    
                    }
                    
                    this.total_price[key] = (parseFloat(amount) + parseFloat(total_tax)).toFixed(2);
                    
                }.bind(this));
            }
        },

        updateItem() {
            this.$emit('changeItem');
        },

        setDefaultQuantity() {
            // only set default value once
            var _this   = this;
            var promise = new Promise(function(resolve, reject) { 
                // only set default value once
                if(_this.quantity.length == 1) {
                    _this.tickets.forEach(function(value, key) {
                        if(key == 0)
                            _this.quantity[key] = 0;
                        else
                            _this.quantity[key] = 0;
                            
                    }.bind());
                }
                resolve(true);
            }); 

            promise.then(function(successMessage) {
                _this.totalPrice();
                _this.orderTotal();
            }, function(errorMessage) { 
                
            }); 
        },

        // count prise all booked tickets
        orderTotal() {
            this.total = 0
            if(Object.keys(this.total_price).length > 0)
            {
                this.total_price.forEach(function(value, key){
                    
                    this.total = (parseFloat(this.total) + parseFloat(value)).toFixed(2);
                    
                }.bind(this))

                return this.total;
            }
            return 0;    
        },

        // total booked tickets
        bookedTicketsTotal() {
            let  total = 0
            if(this.quantity.length > 0)
            {
                this.quantity.forEach(function(value, key){
                    total = parseInt(total) + parseInt(value);

                }.bind(this))

                return total;
            }
            return 0;    
        },

        defaultPaymentMethod() {
            // if not admin
            // total > 0
            if(this.is_admin <= 0 && this.bookedTicketsTotal() > 0)
                this.payment_method = 1;
        },

        loginFirst() {
            window.location.href = route('eventmie.login_first');
        },
        signupFirst() {
            window.location.href = route('eventmie.signup_first');
        },

        // get customers

        getCustomers(loading, search = null){
            var postUrl     = route('eventmie.get_customers');
            var _this       = this;
            axios.post(postUrl,{
                'search' :search,
            }).then(res => {
                
                var promise = new Promise(function(resolve, reject) { 
                    
                    _this.options = res.data.customers;
                    
                    resolve(true);
                }) 
                
                promise 
                    .then(function(successMessage) { 
                        loading(false);
                    }, function(errorMessage) { 
                    //error handler function is invoked 
                        console.log(errorMessage); 
                    }) 
            })
            .catch(error => {
                let serrors = Vue.helpers.axiosErrors(error);
                if (serrors.length) {
                    this.serverValidate(serrors);
                }
            });
        },
        
        // v-select methods
        onSearch(search, loading) {
            loading(true);
            this.search(loading, search, this);
        },

        // v-select methods
        search: _.debounce((loading, search, vm) => {
            
            if(vm.validateEmail(search))
                vm.getCustomers(loading, search);
            else
                loading(false);    
            
        }, 350),

        validateEmail(email) {
            const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        scrollToBottom() {
            this.$refs["bottom-down"].scrollIntoView({ block: "end", inline: "nearest" })
        },

    },

    watch: {
        quantity: function () {
            this.totalPrice();
            this.orderTotal();
            this.defaultPaymentMethod();
        },
        tickets: function() {
            this.setDefaultQuantity();
            this.totalPrice();
            this.orderTotal();
        },

        // active when customer search 
        customer: function () {
            this.customer_id = this.customer != null ?  this.customer.id : null;
        },
    
    },

    mounted() {
        this.openModal = true;
        this.setDefaultQuantity();
        this.defaultPaymentMethod();
    },
}
</script>