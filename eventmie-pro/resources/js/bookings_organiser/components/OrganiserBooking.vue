<template>
    <div>    
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between flex-wrap p-4 bg-white border-bottom-0">
                <div class="d-flex flex-column">
                    <div>
                        <h1 class="fw-bold h2">{{ trans('em.mybookings') }}</h1>
                    </div>

                    <!-- Filters -->
                    <div class="d-flex">
                        <div class="me-2">
                            <select class="form-select" name="event_id" v-model="event_id" >
                                <option  value=0>{{ trans('em.all_events') }} </option>
                                <option v-for="(event, index) in events" :key ="index" :value="event.id">{{event.title}} </option>
                            </select>
                        </div>
                        <div class="me-2">
                            <date-picker class="form-control" :shortcuts="shortcuts" v-model="date_range" range :lang="$vue2_datepicker_lang" :placeholder="trans('em.booking_date')" format="YYYY-MM-DD "></date-picker>
                        </div>        
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table text-wrap">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.event') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.customer_email') }} </th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.ticket') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.order_total') }} </th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.booked_on') }} </th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.payment') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.checked_in') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.status') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.cancellation') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(booking, index) in bookings" :key="index" >
                            <td :data-title="trans('em.event')">
                                <div class="d-flex align-items-center">
                                    <a :href="eventSlug(booking.event_slug)"> 
                                        <img :src="'/storage/'+booking.event_thumbnail" :alt="booking.event_title" class="rounded img-4by3-md ">
                                    </a>
                                    <div class="ms-3 lh-1">
                                        <h5 class="mb-1"> 
                                            <a :href="eventSlug(booking.event_slug)" class="text-inherit text-wrap">{{ booking.event_title }}</a>
                                        </h5>
                                        <p class="text-mute">
                                            <small class="text-muted" v-if="booking.event_start_date != booking.event_end_date">
                                                {{ moment(userTimezone(booking.event_start_date+' '+booking.event_start_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD')).format(date_format.vue_date_format) }}
                                            </small>
                                            <small class="text-muted" v-else>
                                                {{ moment(userTimezone(booking.event_start_date+' '+booking.event_start_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD')).format(date_format.vue_date_format) }}
                                            </small>
                                            
                                            <small class="text-muted">
                                                {{ userTimezone(booking.event_start_date+' '+booking.event_start_time, 'YYYY-MM-DD HH:mm:ss').format(date_format.vue_time_format) }}
                                            </small>
                                            <small class="text-muted"> 
                                                {{ showTimezone() }}
                                            </small>
                                        </p>

                                        <p>
                                            <small class="text-success fw-bold">{{ trans('em.order_id') }}: #{{ booking.order_number }}</small>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="align-middle" :data-title="trans('em.customer_email')">{{ booking.customer_email}}</td>
                            <td class="align-middle" :data-title="trans('em.ticket')"><i class="fas fa-ticket"></i> {{ booking.ticket_title }} <strong>{{ ' x '+booking.quantity }}</strong></td>
                            <td class="align-middle" :data-title="trans('em.order_total')">{{ booking.net_price+' '+ currency }} </td>
                            <td class="align-middle" :data-title="trans('em.booked_on')">{{ moment(userTimezone(booking.created_at, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD')).format(date_format.vue_date_format) }} {{ showTimezone() }}</td>
                            <td class="align-middle text-capitalize" :data-title="trans('em.payment')">
                                <span class="badge bg-secondary text-white" v-if="booking.payment_type == 'offline'">
                                    {{ booking.payment_type }} 
                                    <hr class="small p-0 m-0">
                                    <small class="text-white" v-if="booking.is_paid">{{ trans('em.paid') }}</small>
                                    <small class="text-white" v-else>{{ trans('em.unpaid') }}</small>
                                </span>
                                <span class="badge bg-success text-white" v-else>{{ booking.payment_type }} <hr class="small"><small class="text-small">{{ booking.is_paid ? trans('em.paid') : trans('em.unpaid') }}</small></span>
                            </td>
                            <td class="align-middle" :data-title="trans('em.checked_in')">
                                <span class="badge bg-success text-white" v-if="booking.checked_in > 0">
                                    {{ trans('em.yes') }}
                                    <hr class="small p-0 m-0">
                                    <small class="text-small text-white">{{ booking.checked_in +'/'+ booking.quantity }}</small>
                                </span>
                                <span class="badge bg-secondary text-white" v-else>{{ trans('em.no') }}</span>
                            </td>
                            <td class="align-middle" :data-title="trans('em.status')">
                                <span class="badge bg-success text-white" v-if="booking.status == 1 && booking.expired == 0">{{ trans('em.active') }}</span>
                                <span class="badge bg-danger text-white" v-else>{{ trans('em.inactive') }}</span>
                            </td>
                            <td class="align-middle" :data-title="trans('em.cancellation')">
                                <span class="badge bg-success text-white" v-if="booking.booking_cancel == 0 && booking.status == 1">{{ trans('em.no') }}</span>
                                <span class="badge bg-secondary text-white" v-if="booking.booking_cancel == 0 && booking.status == 0">{{ trans('em.disabled') }}</span>
                                <span class="badge bg-warning text-white" v-if="booking.booking_cancel == 1">{{ trans('em.pending') }}</span>
                                <span class="badge bg-info text-white" v-if="booking.booking_cancel == 2">{{ trans('em.approved') }}</span>
                                <span class="badge bg-secondary text-white" v-if="booking.booking_cancel == 3">{{ trans('em.refunded') }}</span>
                            </td>

                            <!-- check booking expired or not -->
                            <td class="align-middle" :data-title="trans('em.expired')" v-if="booking.expired == 1">
                                <span class="badge bg-danger text-white"> {{trans('em.expired')}} </span>
                            </td>

                            <td class="align-middle text-nowrap" :data-title="trans('em.actions')" v-else>
                                <div class="mb-2 btn-group">
                                    <a class="btn btn-secondary btn-sm" :href="goto_route(booking.id)">
                                        <i class="fas fa-info"></i>&nbsp;<span>{{ trans('em.view') }}</span>
                                    </a>
                                    <button type="button" class="btn btn-info btn-sm" @click="edit_index = index">
                                        <i class="fas fa-edit"> </i><span >{{ trans('em.edit') }}</span>
                                    </button>
                                    <edit-booking 
                                        :booking    = "booking"
                                        v-if="edit_index == index" 
                                        @changeItem = "getOrganiserBookings"

                                    ></edit-booking>
                                </div>

                                <div v-if="hide_ticket_download == null" class="mb-2">
                                    <a v-if="booking.is_paid == 1 && booking.status == 1" class="btn btn-sm bg-danger text-white" :href="downloadURL(booking.id, booking.order_number)"><i class="fas fa-download"></i> {{trans('em.ticket')}}</a>
                                    <span class="badge bg-danger text-white" v-else>
                                        <small v-if="booking.is_paid == 0 && booking.status == 1" class="text-white">{{ trans('em.unpaid') }}</small>
                                        <small v-else class="">{{ trans('em.disabled') }}</small>
                                    </span>
                                </div>

                                <div v-if="hide_google_calendar == null" class="mb-2">
                                    <create-google-event :booking="booking" :date_format="date_format"></create-google-event>
                                </div>

                                <div v-if="booking.online_location != null && booking.is_paid == 1 && booking.status == 1"> 
                                    <button type="button" class="btn btn-sm bg-parimary text-parimary" @click="booking_id = booking.id"><i class="fas fa-tv"></i> {{ trans('em.online') +' '+ trans('em.event') }}</button>
                                    <online-event  v-if="booking_id == booking.id" :online_location="booking.online_location" :booking_id="booking.id" ></online-event>
                                </div>
                            </td>
                            
                        </tr>

                        <tr v-if="bookings.length <= 0">
                            <td colspan="10" class="text-center align-middle">{{ trans('em.no_bookings') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-4 pb-4" v-if="bookings.length > 0">
                <pagination-component v-if="pagination.last_page > 1" :pagination="pagination" :offset="pagination.total"  @paginate="getOrganiserBookings()">
                </pagination-component>
            </div>
        </div>
    </div>
</template>

<script>

import PaginationComponent from '../../common_components/Pagination'
import mixinsFilters from '../../mixins.js';
import EditBooking from './EditBooking.vue';
import DatePicker from 'vue2-datepicker';
import OnlineEvent from '../../bookings_customer/components/OnlineEvent';
import CreateGoogleEvent from '../../bookings_customer/components/CreateGoogleEvent.vue';

export default {
    
    mixins:[
        mixinsFilters
    ],

    props: [
        // pagination query string
        'page',
        'is_success',
        'date_format',
        'hide_ticket_download',
        'hide_google_calendar'
        
    ],
    
    components: {
        PaginationComponent,
        EditBooking,
        DatePicker,
        OnlineEvent,
        CreateGoogleEvent
    },

    
    data() {
        return {
            bookings   : [],
            moment     : moment,
            edit_index : null,
             pagination: {
                'current_page': 1
            },
            currency   : null,

            date_range : [],
            start_date : '',
            end_date   : '',

            booking_id : 0,
            
            // date shortucts like today, tommorrow
            shortcuts: [
                {
                    text: trans('em.today'),
                    onClick: () => {
                        this.date_range = [moment().toDate(), moment().toDate() ]
                    }
                },
                {
                    text: trans('em.tomorrow'),
                    onClick: () => {
                        this.date_range = [moment().add(1,'day').toDate(), moment().add(1,'day').toDate()]
                    }
                },
                {
                    text: trans('em.this')+' '+trans('em.weekend'),
                    onClick: () => {
                        this.date_range = [moment().endOf("week").toDate(), moment().endOf("week").toDate()]
                    }
                },
                {
                    text: trans('em.this')+' '+trans('em.week'),
                    onClick: () => {
                        this.date_range = [moment().startOf("week").toDate(), moment().endOf("week").toDate()]
                    }
                },
                {
                    text: trans('em.next')+' '+trans('em.week'),
                    onClick: () => {
                        this.date_range = [moment().add(1, 'weeks').startOf("week").toDate(), moment().add(1, 'weeks').endOf("week").toDate()]
                    }
                },
                {
                    text: trans('em.this')+' '+trans('em.month'),
                    onClick: () => {
                        this.date_range = [moment().startOf("month").toDate(), moment().endOf("month").toDate()]
                    }
                },
                {
                    text: trans('em.next')+' '+trans('em.month'),
                    onClick: () => {
                        this.date_range = [moment().add(1, 'months').startOf("month").toDate(), moment().add(1, 'months').endOf("month").toDate()]
                    }
                },
            ],

            events    : [],
            event_id  : 0,
        }
    },

     computed: {
        current_page() {
            // get page from route
            if(typeof this.page === "undefined")
                return 1;
            return this.page;
        },
    },

    methods:{
          // get all events
        getOrganiserBookings() {

            if(typeof this.start_date === "undefined") {
                this.start_date     = '';
            }
            if(typeof this.end_date === "undefined") {
                this.end_date     = '';
            }

            axios.get(route('eventmie.obookings_organiser_bookings')+'?page='+this.current_page+'&event_id='+this.event_id+'&start_date='
                         +this.start_date+'&end_date='+this.end_date)
            .then(res => {
                this.currency   = res.data.currency;
                this.bookings   = res.data.bookings.data;
                this.pagination = {
                    'total' : res.data.bookings.total,
                    'per_page' : res.data.bookings.per_page,
                    'current_page' : res.data.bookings.current_page,
                    'last_page' : res.data.bookings.last_page,
                    'from' : res.data.bookings.from,
                    'to' : res.data.bookings.to,
                    'links' : res.data.bookings.links
                };
            })
            .catch(error => {
                
            });
        },

        // view booking by organiser 
        organiserViewBooking(booking_id) {
            axios.get(route('eventmie.obookings_organiser_bookings_show',[booking_id]))
            .then(res => {
                if(res.data.status)
                {
                    this.getOrganiserBookings();
                }    
            })
            .catch(error => {
                
            });
        },

        // view booking
        goto_route(id) {
            return route('eventmie.obookings_organiser_bookings_show', {id:id});
        },

        // return route with event slug
        eventSlug(slug){
            
            if(slug)
            {
                return route('eventmie.events_show',[slug]);
            }    
        },

        // return route with download URL
        downloadURL(id, order_number) {
            if(id && order_number) {
                return route('eventmie.downloads_index',[id, order_number]);
            }
        },

        // searching by date 
        dateRange: function () {
            var is_date_null = 0;
            if(Object.keys(this.date_range).length > 0 )
            {
                // convert date range to server side date
                this.date_range.forEach(function(value, key) {
                    if(value != null) {
                        is_date_null = 1;

                        if(key == 0)
                            this.start_date   =  this.convert_date(value); // convert local start_date to server date then searching by date
                        
                        if(key == 1)
                            this.end_date     =  this.convert_date(value); // convert local end_date to server date then searching by date
                    }
                }.bind(this));
                
                // date reset
                if(is_date_null <= 0){
                    this.start_date  = '';
                    this.end_date    = '';
                }

                this.getOrganiserBookings()
            }
            
        },

        // get all events
        getMyEvents() {
            axios.get(route('eventmie.all_myevents'))
            .then(res => {
                this.events  = res.data.myevents;
            })
            .catch(error => {
                
            });
        },

        
    },
   
    mounted() {
        this.getOrganiserBookings();
        this.getMyEvents();
        
        // send email after successful bookings
        this.sendEmail();
    },

    watch : {
        date_range: function () {
            this.dateRange();
        },

        event_id: function () {
            this.getOrganiserBookings();
        },

    }
}
</script>


