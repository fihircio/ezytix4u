<template>
    <div>
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between flex-wrap p-4 bg-white border-bottom-0">
                <div>
                    <h1 class="mb-0 fw-bold h2">{{ trans('em.myevents') }}</h1>
                </div>
                <div>
                    <a class="btn btn-primary btn-block" :href="createEvent()"><span><i class="fas fa-calendar-plus"></i> {{ trans('em.create_event') }}</span></a>  
                </div>
            </div>
            <div class="table-responsive">
                <table class="table text-wrap">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.event') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.timings') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.repetitive') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.payment_frequency') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.publish') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.status') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(event, index) in events" :key="index" >
                            <td :data-title="trans('em.event')">
                                <div class="d-flex align-items-center">
                                    <a :href="eventSlug(event.slug)">    
                                        <img :src="'/storage/'+event.poster" :alt="event.title" class=" rounded img-4by3-md ">
                                    </a>
                                    <div class="ms-3 lh-1">
                                        <h5 class="mb-1"> 
                                            <a class="text-inherit text-wrap" :href="eventSlug(event.slug)">{{ event.title }}</a>
                                        </h5>
                                        <small class="text-success strong" v-if="event.count_bookings > 0"><i class="fas fa-bolt"></i> {{ event.count_bookings }} {{ trans('em.bookings') }}</small>
                                        <small class="text-muted strong" v-else><i class="fas fa-hourglass"></i> {{ event.count_bookings }} {{ trans('em.bookings') }}</small>
                                    </div>
                                </div>
                            </td>

                            <td class="align-middle text-nowrap" :data-title="trans('em.start_date')">
                                <small class="text-muted">
                                    {{ changeDateFormat(userTimezone(event.start_date+' '+event.start_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD'), 'YYYY-MM-DD') }}
                                    {{ userTimezone(event.start_date+' '+event.start_time, 'YYYY-MM-DD HH:mm:ss').format(date_format.vue_time_format) }} {{ showTimezone()  }}
                                </small>
                                <br>
                                <small class="text-muted" v-if="userTimezone(event.start_date+' '+event.start_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD') <= userTimezone(event.end_date+' '+event.end_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD')" :data-title="trans('em.end_date')">
                                    {{ changeDateFormat(userTimezone(event.end_date+' '+event.end_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD'), 'YYYY-MM-DD') }}
                                    {{ userTimezone(event.end_date+' '+event.end_time, 'YYYY-MM-DD HH:mm:ss').format(date_format.vue_time_format) }} {{  showTimezone()  }}
                                </small>
                                <small class="text-muted" v-else :data-title="trans('em.end_date')">
                                    {{ changeDateFormat(userTimezone(event.start_date+' '+event.start_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD'), 'YYYY-MM-DD') }}
                                    {{ userTimezone(event.end_date+' '+event.end_time, 'YYYY-MM-DD HH:mm:ss').format(date_format.vue_time_format) }} {{  showTimezone()  }}
                                </small>
                            </td>
                            <td class="align-middle" :data-title="trans('em.repetitive')">
                                <span class="badge bg-success" v-if="event.repetitive">{{ trans('em.yes')  }}</span>
                                <span class="badge bg-danger" v-else>{{ trans('em.no') }}</span>
                            </td>
                            <td class="align-middle" :data-title="trans('em.payment_frequency')">
                                <span class="badge bg-info" v-if="event.merge_schedule">{{ trans('em.monthly_weekly')  }}</span>
                                <span class="badge bg-primary" v-else>{{ trans('em.full_advance') }}</span>
                            </td>
                            <td class="align-middle" :data-title="trans('em.publish')">
                                <span class="badge bg-success" v-if="event.publish">{{ trans('em.published')  }}</span>
                                <span class="badge bg-secondary" v-else>{{ trans('em.unpublished') }}</span>
                            </td>
                            <td class="align-middle" :data-title="trans('em.status')">
                                <span class="badge bg-success" v-if="event.status">{{ trans('em.enabled')  }}</span>
                                <span class="badge bg-danger" v-else>{{ trans('em.disabled') }}</span>
                            </td>
                            <td class="align-middle" :data-title="trans('em.actions')">
                                <div class="d-grid gap-2 text-nowrap">
                                    <a class="btn btn-primary btn-sm" :href="eventEdit(event.slug)"><i class="fas fa-edit"></i> {{ trans('em.edit') }}</a>
                                    <a class="btn btn-success btn-sm" :class="{ 'disabled' : event.count_bookings < 1 }" :href="exportAttendies(event.slug, event.count_bookings)">
                                        <i class="fas fa-file-csv"></i> {{ trans('em.export_attendees') }}
                                    </a>
                                </div>
                            </td>

                        </tr>
                        <tr v-if="events.length <= 0">
                            <td class="text-center align-middle">{{ trans('em.no_events') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-4 pb-4" v-if="events.length > 0">
                <pagination-component v-if="pagination.last_page > 1" :pagination="pagination" :offset="pagination.total" @paginate="getMyEvents()"></pagination-component>
            </div>
        </div>
    </div>       
</template>

<script>

import PaginationComponent from '../../common_components/Pagination'

import mixinsFilters from '../../mixins.js';


export default {
    props: [
        // pagination query string
        'page',
        'category',
        'date_format'
    ],

    components: {
        PaginationComponent,
    },
    
    mixins:[
        mixinsFilters
    ],

    data() {
        return {
            events           : [],
            pagination: {
                'current_page': 1
            },
            moment           : moment,
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
    methods: {
        
        // get all events
        getMyEvents() {
            axios.get(route('eventmie.myevents')+'?page='+this.current_page)
            .then(res => {
                
                this.events  = res.data.myevents.data;

                this.pagination = {
                    'total' : res.data.myevents.total,
                    'per_page' : res.data.myevents.per_page,
                    'current_page' : res.data.myevents.current_page,
                    'last_page' : res.data.myevents.last_page,
                    'from' : res.data.myevents.from,
                    'to' : res.data.myevents.to,
                    'links' : res.data.myevents.links
                };
            })
            .catch(error => {
                
            });
        },

        // edit myevents
        eventEdit(event_id) {
            return route('eventmie.myevents_form', {id: event_id});
        },

        // create newevents
        createEvent() {
            return route('eventmie.myevents_form');
        },

        // return route with event slug
        eventSlug(slug){
            return route('eventmie.events_show',[slug]);
        },

        // ExportAttendies
        exportAttendies(event_slug = null, event_bookings = 0){
            if(event_slug != null && event_bookings > 0)
                return route('eventmie.export_attendees', [event_slug]);
            
        }
    },
    mounted() {
        this.getMyEvents();
    }
}
</script>
<style>

</style>