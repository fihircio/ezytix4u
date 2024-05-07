<template>

    <div class="px-2 py-2 w-100">
        <!-- listing block -->
        <div>
            <div class="position-relative">
                <a  :href="eventSlug(event.slug)" class="text-inherit">
                    <div class="back-image rounded-3 img-hover prevent_draggable" :style="{ 'background-image': 'url(/storage/' + event.thumbnail + ')' }"></div>
                </a>
                
                <!-- repetitive events who Upcoming  -->
                <span class="d-inline-flex badge bg-primary position-absolute top-0 ms-1 mt-2 start-0">
                    {{ changeDateFormat(userTimezone(event.start_date+' '+event.start_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD'), "YYYY-MM-DD") }}                        
                </span>
                    

            </div>
            <div class="rounded-bottom border-0 mb-lg-0">
                <div class="mt-3 mb-0">
                    <span v-if="event.repetitive" class="d-inline-flex btn btn-sm border-dark f-small mb-1 fw-medium">{{ trans('em.repetitive') }}</span>
                    <span class="d-inline-flex btn btn-sm border-dark f-small mb-1 fw-medium" v-if="event.online_location">{{ trans('em.online') }}</span>
                    <span class="d-inline-flex btn btn-sm border-dark f-small mb-1 fw-medium" v-if="!event.repetitive">{{ trans('em.one_t_event')  }}</span>

                    <!-- simple events means without repetitive who ended-->
                    <span class="d-inline-flex btn btn-sm border-dark f-small mb-1 fw-medium"
                        v-if="!event.repetitive && moment().format('YYYY-MM-DD') > 
                            userTimezone(event.start_date+' '+event.start_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD')"
                    >
                        {{ trans('em.ended') }}
                    </span>


                    <!-- repetitive events who Ended -->
                    <div v-if="event.repetitive && moment().format('YYYY-MM-DD') > userTimezone(event.end_date+' '+event.end_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD')">
                        <span class="d-inline-flex btn btn-sm border-dark f-small mb-1 fw-medium">{{ trans('em.ended') }}&nbsp;{{ trans('em.event') }}</span>
                    </div>


                    <h5 class="text-left p-0 m-0">
                        <a  :href="eventSlug(event.slug)" class="text-inherit">
                        {{ event.title.substring(0, 30)+
                        `${event.title.length > 30 ? '...' : '' }`
                        }} 
                        </a>
                    </h5>
                </div>
                <div class="text-sm">
                    {{ event.category_name }}
                </div>  

                <div class="text-sm d-flex justify-content-between mt-2"
                    v-for="(ticket, index1) in event.tickets" 
                    :key="index1" 
                    v-if="index1 < 1" 
                >
                    <div class="font-weight-semi-bold fw-light text-dark">
                        <span>
                            <i class="fas fa-map-marker-alt"></i>&nbsp;{{event.city}}
                        </span>
                    </div>
                    <div>
                        <span class="h6">{{`${ticket.price+' '+currency}` }}</span>
                        <span class="text-sm font-weight-semi-bold ms-1" >
                            / {{ ticket.title.substring(0, 15)+`${ticket.title.length > 15 ? '..' : '' }` }} 
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- listing block -->
    </div>
           
   
</template>

<script>

import mixinsFilters from '../mixins.js';

export default {
    
    props: ['event', 'currency', 'date_format'],


    mixins:[
        mixinsFilters
    ],

    data() {
        return {
        }
    },

    methods:{
        
        // check free tickets of events
        checkFreeTickets(event_tickets = []){
            let free = false;
            event_tickets.forEach(function(value, key) {
                if(parseFloat(value.price) <= parseFloat(0))
                {
                    free = true;
                }
            });    
            return free;
        },

        
        // return route with event slug
        eventSlug: function eventSlug(slug) {
            return route('eventmie.events_show', [slug]);
        }

  
    },

    mounted(){      
    }

}
</script>