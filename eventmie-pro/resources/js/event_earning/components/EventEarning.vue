<template>
    <div>
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between flex-wrap p-4 bg-white border-bottom-0">
                <div class="d-flex flex-column">
                    <div>
                        <h1 class="fw-bold h2">{{ trans('em.myearning') }}</h1>
                    </div>

                    <!-- Filters -->
                    <div class="d-flex">
                        <div class="me-2">
                            <select class="form-select" name="event_id" v-model="event_id" >
                                <option  value=0>{{ trans('em.all_events') }} </option>
                                <option v-for="(event, index) in events" :key ="index" :value="event.id">{{event.title}} </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mx-3">

                    <div class="col-lg-4 col-md-12 col-12">
                        <div class="card bg-info mb-4 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div>
                                    <h5 class="mb-0 text-white">{{ trans('em.total_bookings') }}</h5>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-4">
                                    <h2 class="fw-bold mb-0 fs-1 text-white">{{ total_earning.customer_paid_total }} {{ currency }}</h2>
                                    <i class="fas fa-cart-arrow-down text-white fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-12">
                        <div class="card bg-dark mb-4 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div>
                                    <h5 class="mb-0 text-white">{{ trans('em.total_admin_commission') }}</h5>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-4">
                                    <h2 class="fw-bold mb-0 fs-1 text-white">{{ total_earning.admin_commission_total }} {{ currency }}</h2>
                                    <i class="fas fa-user-shield text-white fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-12">
                        <div class="card bg-success mb-4 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div>
                                    <h5 class="mb-0 text-white">{{ trans('em.total_profit') }}</h5>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-4">
                                    <h2 class="fw-bold mb-0 fs-1 text-white">{{ total_earning.organiser_earning_total }} {{ currency }}</h2>
                                    <i class="fas fa-sack-dollar text-white fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>

            </div>

            <div class="table-responsive">
                <table class="table text-wrap">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.event') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.bookings') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.commission') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.profit') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.month') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.transferred') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(earning, index) in event_earning" :key="index" >
                            <td :data-title="trans('em.event')">
                                <div class="d-flex align-items-center">
                                    <a :href="eventSlug(earning.event_slug)"> 
                                        <img :src="'/storage/'+earning.event_thumbnail" :alt="earning.event_name" class="rounded img-4by3-md ">
                                    </a>
                                    <div class="ms-3 lh-1">
                                        <h5 class="mb-1"> 
                                            <a :href="eventSlug(earning.event_slug)" class="text-inherit text-wrap">{{ earning.event_name }}</a>
                                        </h5>
                                        <small class="text-success strong"><i class="fas fa-bolt"></i> {{ earning.count_bookings }} {{ trans('em.bookings') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle" :data-title="trans('em.bookings')">
                                {{ earning.customer_paid_total }} {{ currency }}
                            </td>
                            <td class="align-middle" :data-title="trans('em.commission')">
                                {{ earning.admin_commission_total }} {{ currency }}
                            </td>
                            <td class="align-middle text-success" :data-title="trans('em.profit')">
                                <i class="fas fa-sack-dollar"></i> {{ earning.organiser_earning_total }} {{ currency }}
                            </td>
                            <td class="align-middle" :data-title="trans('em.month')">
                                {{ moment(earning.month_year, 'MM YYYY').format('MMM ,YYYY') }}
                            </td>
                            <td class="align-middle" :data-title="trans('em.transferred')">
                                <span class="badge bg-success" v-if="earning.transferred > 0">{{ trans('em.paid') }}</span>
                                <span class="badge bg-primary" v-else>{{ trans('em.pending') }}</span>
                            </td>
                        </tr>
                        <tr v-if="event_earning.length <= 0">
                            <td colspan="6" class="text-center align-middle">{{ trans('em.no_bookings') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-4 pb-4"  v-if="event_earning.length > 0">
                <pagination-component v-if="pagination.last_page > 1" :pagination="pagination" :offset="pagination.total" @paginate="eventEarning()">
                </pagination-component>
            </div>
        </div>
    </div>
</template>


<script>

import PaginationComponent from '../../common_components/Pagination'
import mixinsFilters from '../../mixins.js';



export default {
    
    mixins:[
        mixinsFilters
    ],

    props: [
        // pagination query string
        'page',
        
    ],
    
    components: {
        PaginationComponent,
    },

    
    data() {
        return {
            event_earning : [],
            total_earning : [],
            
            moment     : moment,
            edit_index : null,
             pagination: {
                'current_page': 1
            },
            currency   : null,

            date_range : [],
            start_date : '',
            end_date   : '',
            
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

        // return route with event slug
        eventSlug(slug){
            
            if(slug)
            {
                return route('eventmie.events_show',[slug]);
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

                this.eventEarning();
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

        // get event earning
        eventEarning() {
            
            if(typeof this.start_date === "undefined") {
                this.start_date     = '';
            }
            if(typeof this.end_date === "undefined") {
                this.end_date     = '';
            }

            axios.get(route('eventmie.organiser_event_earning')+'?page='+this.current_page+'&event_id='+this.event_id+'&start_date='
                         +this.start_date+'&end_date='+this.end_date)
            .then(res => {
                
                this.event_earning   = res.data.event_earning.data;
                this.pagination = {
                    'total' : res.data.event_earning.total,
                    'per_page' : res.data.event_earning.per_page,
                    'current_page' : res.data.event_earning.current_page,
                    'last_page' : res.data.event_earning.last_page,
                    'from' : res.data.event_earning.from,
                    'to' : res.data.event_earning.to,
                    'links' : res.data.event_earning.links
                };
            })
            .catch(error => {
                
            });
        },

        //net total
        totalEarning() {
             axios.get(route('eventmie.organiser_total_earning'))
            .then(res => {
                
                this.total_earning   = res.data.total_earning;
                this.currency   = res.data.currency;
                
            })
            .catch(error => {
                
            });
        }
    },
   
    mounted() {
        
        this.getMyEvents();
        this.eventEarning();
        this.totalEarning();
    },

    watch : {
        date_range: function () {
            this.dateRange();
        },

        event_id: function () {
            this.eventEarning();
        },

    }
}
</script>


