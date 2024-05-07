<!-- Event schedules-->
<template>
    <div class="col-xs-12">
        <div  v-if="event.repetitive > 0">
            <div class="bg-white rounded-3 px-3 mb-3">
                <ul class="nav nav-lb-tab h-scroll">
                    <li v-for="(item, index) in calculate_months" class="nav-item" :key="index" 
                        @click="tab_active_index = index"
                        v-if="schedules[index].from_time != null && schedules[index].to_time != null" 
                    >
                        <a class="nav-link pointer lh-1 text-center" :class="{'active' : index == tab_active_index}">
                            {{ moment(local_from_date[index], "YYYY-MM-DD").format('MMMM  YYYY') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- non-merge schedule | daily/weekly/monthly -->
            <div class="tab-content v-scroll mh-30 p-3" v-if="event.merge_schedule <= 0">
                <div v-for="(item, index) in calculate_months" 
                    :key="index"   class="tab-pane" :class="{'active' : index == tab_active_index}" 
                    
                    v-if="schedules[index].from_time != null
                    && schedules[index].to_time != null 
                    ">
                    
                    <div class="row" role="tablist" aria-multiselectable="true" v-if="parseInt(schedules[0].repetitive_type) != parseInt(2)">

                        <a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne">
                            <div class="card border-0 shadow-sm img-hover mb-2 lgx-single-schedule" 
                                :class="{
                                    'expired-event': userTimezone1(item+'-'+selected_date+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss'),
                                    'outofstock-event': !available_dates[index][index1]
                                }"
                                v-for="(selected_date, index1) in selected_dates[index]" 
                                :key="index1" 
                            >
                                <div class="d-flex justify-content-between flex-wrap card-body schedule-info"
                                    @click="
                                        (!(userTimezone1(item+'-'+selected_date+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && available_dates[index][index1])
                                        ? selectDates(userTimezone1(dateToFullDate(selected_date+' '+schedules[index].from_time, schedules[index].from_date),'dddd LL').format('dddd LL'), false,
                                        changeTimeFormat(userTimezone1(moment(schedules[index].from_date).format('YYYY-MM')+'-'+selected_date+' '+schedules[index].from_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss'))
                                        , changeTimeFormat(userTimezone1(moment(schedules[index].to_date).format('YYYY-MM')+'-'+selected_date+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')) ) 
                                        : null">
                                    <h6 class="mb-0 h6 fw-bold">{{ userTimezone1(dateToShortDate(selected_date+' '+schedules[index].from_time, schedules[index].from_date), date_format.vue_date_format).format(date_format.vue_date_format) }}</h6>
                                    <span class="badge bg-light text-primary">{{ changeTimeFormat(userTimezone1(moment(schedules[index].from_date).format('YYYY-MM')+'-'+selected_date+' '+schedules[index].from_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')) }} -
                                        
                                        {{ changeTimeFormat(userTimezone1(moment(schedules[index].to_date).format('YYYY-MM')+'-'+selected_date+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')) }}

                                        {{ showTimezone()  }}
                                    </span>
                                    
                                    <!-- expired -->
                                    <h4 class="mb-0 h6 badge bg-danger event-ended" v-if="userTimezone1(item+'-'+selected_date+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')"
                                    >
                                        <span>{{ trans('em.ended') }}</span>
                                    </h4>

                                    <!-- out-of-stock -->
                                    <h4 class="mb-0 h6 badge bg-danger event-outofstock" v-if="
                                        !(userTimezone1(item+'-'+selected_date+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && !available_dates[index][index1]"
                                    >
                                        <span>{{ trans('em.out_of_stock') }}</span>
                                    </h4>

                                    <!-- filling fast -->
                                    <h4 class="mb-0 h6 badge bg-danger event-outofstock mr-3" v-if="
                                        !(userTimezone1(item+'-'+selected_date+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && positiveInteger(available_dates[index][index1])"
                                    >
                                        <span>{{ trans('em.filling_fast') }}</span>
                                    </h4>

                                </div>
                            </div>    
                        </a>
                               
                    </div>

                    <!-- for weekly -->
                    <div v-else class="row" role="tablist" aria-multiselectable="true">

                        <a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne">

                            <div class="card border-0 shadow-sm img-hover mb-2 lgx-single-schedule" 
                                :class="{
                                    'expired-event': userTimezone(item+'-'+selected_date+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss'),
                                        'outofstock-event': !available_dates[index][index1]
                                }"
                                v-for="(selected_date, index1) in selected_dates[index]" 
                                :key="index1" 
                            >
                                <div class="d-flex justify-content-between flex-wrap card-body schedule-info" 
                                    @click="
                                        (!(userTimezone(item+'-'+selected_date+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && available_dates[index][index1])
                                        ? selectDates(userTimezone(dateToFullDate(selected_date+' '+schedules[index].from_time, schedules[index].from_date),'dddd LL').format('dddd LL'), false,
                                        changeTimeFormat(userTimezone(moment(schedules[index].from_date).format('YYYY-MM')+'-'+selected_date+' '+schedules[index].from_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss'))
                                        , changeTimeFormat(userTimezone(moment(schedules[index].to_date).format('YYYY-MM')+'-'+selected_date+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')) ) 
                                        : null">
                                    <h6 class="mb-0 h6 fw-bold">{{ userTimezone(dateToShortDate(selected_date+' '+schedules[index].from_time, schedules[index].from_date), date_format.vue_date_format).format(date_format.vue_date_format) }}</h6>
                                    <span class="badge bg-light text-primary">{{ changeTimeFormat(userTimezone(moment(schedules[index].from_date).format('YYYY-MM')+'-'+selected_date+' '+schedules[index].from_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')) }} -
                                        
                                        {{ changeTimeFormat(userTimezone(moment(schedules[index].to_date).format('YYYY-MM')+'-'+selected_date+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')) }}

                                        {{ showTimezone()  }}
                                    </span>
                                    
                                    <!-- expired -->
                                    <h4 class="mb-0 h6 badge bg-danger" v-if="userTimezone(item+'-'+selected_date+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')"
                                    >
                                        <span>{{ trans('em.ended') }}</span>
                                    </h4>

                                    <!-- out-of-stock -->
                                    <h4 class="mb-0 h6 badge bg-danger" v-if="
                                        !(userTimezone(item+'-'+selected_date+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && !available_dates[index][index1]"
                                    >
                                        <span>{{ trans('em.out_of_stock') }}  </span>
                                    </h4>

                                    <!-- filling fast -->
                                    <h4 class="mb-0 h6 mr-3 badge bg-warning" v-if="
                                        !(userTimezone(item+'-'+selected_date+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && positiveInteger(available_dates[index][index1])"
                                    >
                                        <span>{{ trans('em.filling_fast') }}</span>
                                    </h4>

                                </div>
                            </div>
                        </a>

                    </div>

                </div>
            </div>

            <!-- merge schedule | monthly repetitive -->
            <div class="tab-content v-scroll mh-30 p-3" v-if="event.merge_schedule > 0 && parseInt(schedules[0].repetitive_type) == parseInt(3)">
                <div v-for="(item, index) in calculate_months" :key="index" class="tab-pane" :class="{'active' : index == tab_active_index}" >
                    <div class="row" role="tablist" aria-multiselectable="true">
                        <a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne">
                            <div class="card border-0 shadow-sm img-hover mb-2 lgx-single-schedule"  
                                :class="{
                                    'expired-event': userTimezone1(item+'-'+selected_dates[index][selected_dates[index].length - 1]+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss'),
                                    'outofstock-event': !available_dates[index][0]
                                }"
                                @click="
                                    (!(userTimezone1(item+'-'+selected_dates[index][selected_dates[index].length - 1]+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && available_dates[index][0])
                                    ? selectDates(
                                    userTimezone1(dateToFullDate(selected_dates[index][0]+' '+schedules[index].from_time,schedules[index].from_date),'dddd LL').format('dddd LL'),
                                    userTimezone1(dateToFullDate(selected_dates[index][selected_dates[index].length - 1]+' '+schedules[index].from_time, schedules[index].from_date),'dddd LL').format('dddd LL'),
                                    changeTimeFormat(userTimezone1(moment(schedules[index].from_date).format('YYYY-MM')+'-'+selected_dates[index][0]+' '+schedules[index].from_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')), 
                                    changeTimeFormat(userTimezone1(moment(schedules[index].to_date).format('YYYY-MM')+'-'+selected_dates[index][0]+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')) 
                                ) : null" 
                            >
                                <div class="d-flex justify-content-between flex-wrap card-body schedule-info"> 
                                    <h6 class="mb-0 h6 fw-bold">
                                        {{changeTimeFormat(userTimezone1(moment(schedules[index].from_date).format('YYYY-MM')+'-'+selected_dates[index][0]+' '+schedules[index].from_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')) }} - 
                                        {{changeTimeFormat(userTimezone1(moment(schedules[index].to_date).format('YYYY-MM')+'-'+selected_dates[index][0]+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')) }}
                                        {{ showTimezone() }}
                                    </h6>
                                    
                                    <!-- expired -->
                                    <h4 class="mb-0 h6 badge bg-danger" v-if="userTimezone1(item+'-'+selected_dates[index][selected_dates[index].length - 1]+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')"
                                    >
                                        <span>{{ trans('em.ended') }}</span>
                                    </h4>
                                    
                                    <!-- out-of-stock -->
                                    <h4 class="mb-0 h6 badge bg-danger" v-if="
                                        !(userTimezone1(item+'-'+selected_dates[index][selected_dates[index].length - 1]+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && !available_dates[index][0]"
                                    >
                                        <span>{{ trans('em.out_of_stock') }}</span>
                                    </h4>

                                    <!-- filling fast -->
                                    <h4 class="mb-0 h6 badge bg-warning mr-3" v-if="
                                        !(userTimezone1(item+'-'+selected_dates[index][selected_dates[index].length - 1]+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) &&  positiveInteger(available_dates[index][0])"
                                    >
                                        <span>{{ trans('em.filling_fast') }}</span>
                                    </h4>

                                    <ul class="date-list">
                                        <li class="badge bg-light text-primary me-1"
                                            v-for="(selected_date, index2) in selected_dates[index]" :key="index2" 
                                        >
                                            {{ userTimezone1(dateToShortDate(selected_date+' '+schedules[index].from_time, schedules[index].from_date ), date_format.vue_date_format).format(date_format.vue_date_format) }}
                                        </li>
                                    </ul>

                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- merge schedule | weekly repetitive -->
            <div class="tab-content v-scroll mh-30 p-3" v-if="event.merge_schedule > 0 && parseInt(schedules[0].repetitive_type) == parseInt(2)">
                <div v-for="(item, index) in calculate_months" :key="index" class="tab-pane" :class="{'active' : index == tab_active_index}" 
                    v-if="schedules[index].from_time != null && schedules[index].to_time != null "
                >
                    <div class="row" role="tablist" aria-multiselectable="true">

                        <a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne">
                            <div class="card border-0 shadow-sm img-hover mb-2 lgx-single-schedule"  
                                :class="{
                                        'expired-event': moment(moment(getWeekLastDate(item2, selected_dates[index], schedules[index].from_date)).format('YYYY-MM-DD')+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss'),
                                        'outofstock-event': !checkSeatAvailability(moment(getWeekFirstDate(item2, selected_dates[index], schedules[index].from_date)).format('YYYY-MM-DD'))
                                    }"
                                v-for="(item2, index2) in week_numbers[index]" 
                                :key="index2"
                                @click="
                                    (!(moment(moment(getWeekLastDate(item2, selected_dates[index], schedules[index].from_date)).format('YYYY-MM-DD')+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && 
                                        checkSeatAvailability(moment(getWeekFirstDate(item2, selected_dates[index], schedules[index].from_date)).format('YYYY-MM-DD'))) 
                                    ? selectDates(
                                    getWeekFirstDate(item2, selected_dates[index], schedules[index].from_date),
                                    getWeekLastDate(item2, selected_dates[index], schedules[index].from_date),
                                    changeTimeFormat(userTimezone(moment(schedules[index].from_date).format('YYYY-MM')+'-'+selected_dates[index]+' '+schedules[index].from_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')), 
                                    changeTimeFormat(userTimezone(moment(schedules[index].to_date).format('YYYY-MM')+'-'+selected_dates[index]+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')))
                                    : null" 
                            >
                                <div class="d-flex justify-content-between flex-wrap card-body schedule-info"> 
                                    <h6 class="mb-0 h6 fw-bold">{{changeTimeFormat(userTimezone(moment(schedules[index].from_date).format('YYYY-MM')+'-'+selected_dates[index]+' '+schedules[index].from_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')) }} - {{ changeTimeFormat(userTimezone(moment(schedules[index].to_date).format('YYYY-MM')+'-'+selected_dates[index]+' '+schedules[index].to_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss'))}} {{ showTimezone() }}</h6>
                                    
                                <!-- expired -->
                                    <h4 class="mb-0 h6 badge bg-danger" v-if="moment(moment(getWeekLastDate(item2, selected_dates[index], schedules[index].from_date)).format('YYYY-MM-DD')+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')"
                                    >
                                        <span>{{ trans('em.ended') }} </span>
                                    </h4>
                                    
                                    <!-- out-of-stock -->
                                    <h4 class="mb-0 h6 badge bg-danger" v-if="
                                        !(moment(moment(getWeekLastDate(item2, selected_dates[index], schedules[index].from_date)).format('YYYY-MM-DD')+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && 
                                        !checkSeatAvailability(moment(getWeekFirstDate(item2, selected_dates[index], schedules[index].from_date)).format('YYYY-MM-DD'))"
                                    >
                                        <span>{{ trans('em.out_of_stock') }}</span>
                                    </h4>

                                    <!-- filling fast -->
                                    <h4 class="mb-0 h6 badge bg-warning mr-3" v-if="
                                        !(moment(moment(getWeekLastDate(item2, selected_dates[index], schedules[index].from_date)).format('YYYY-MM-DD')+' '+schedules[index].to_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && positiveInteger(checkSeatAvailability(moment(getWeekFirstDate(item2, selected_dates[index], schedules[index].from_date)).format('YYYY-MM-DD')))"
                                    >
                                        <span>{{ trans('em.filling_fast') }}</span>
                                    </h4>

                                    <ul class="date-list">
                                        <li class="badge bg-light text-primary me-1"
                                            v-for="(selected_date, index3) in selected_dates[index]" 
                                            :key="index3"
                                            v-if="item2 == moment(userTimezone(dateToFullDate(selected_date+' '+schedules[index].from_time, schedules[index].from_date),'dddd LL').format('dddd LL'),'dddd LL').isoWeek()"
                                        >
                                            {{ userTimezone(dateToShortDate(selected_date+' '+schedules[index].from_time, schedules[index].from_date ),date_format.vue_date_format).format(date_format.vue_date_format) }}
                                        </li>
                                    </ul>   
                                     
                                </div>
                            </div>
                        </a>

                    </div>
                </div>
            </div>

        </div>

        <!-- Single day non-repetitive event -->
        <div class="lgx-tab" v-if="event.repetitive <= 0">
            <div class="tab-content v-scroll mh-30 p-3">
                <div class="tab-pane active">   
                    <div class="row" role="tablist" aria-multiselectable="true">

                        <a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne">
                            <div class="card border-0 shadow-sm img-hover mb-2 lgx-single-schedule"
                                :class="{
                                        'expired-event': moment(event.end_date+' '+event.end_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss'),
                                        'outofstock-event': !checkSeatAvailability(moment(event.start_date,'YYYY-MM-DD' ).format('YYYY-MM-DD'))
                                }"
                            >
                                <div class="d-flex justify-content-between flex-wrap card-body schedule-info" 
                                        @click="
                                        (!(moment(event.end_date+' '+event.end_time,'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && 
                                        checkSeatAvailability(moment(event.start_date,'YYYY-MM-DD' ).format('YYYY-MM-DD')))  
                                    ? singleEvent() : null"
                                >
                                    <h6 class="mb-0 h6 fw-bold">{{ convert_date_to_local_format(userTimezone(event.start_date+' '+event.start_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD') ) }} - {{ ( userTimezone(event.start_date+' '+event.start_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD') <= userTimezone(event.end_date+' '+event.end_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD') )  ? convert_date_to_local_format(userTimezone(event.end_date+' '+event.end_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD') ) : convert_date_to_local_format(userTimezone(event.start_date+' '+event.start_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD') ) }}</h6>
                                    <span class="badge bg-light text-primary">{{ userTimezone(event.start_date+' '+event.start_time, 'YYYY-MM-DD HH:mm:ss').format(date_format.vue_time_format) }} - {{ userTimezone(event.end_date+' '+event.end_time, 'YYYY-MM-DD HH:mm:ss').format(date_format.vue_time_format) }} {{ showTimezone() }}</span>

                                    <!-- expired -->
                                    <h4 class="mb-0 h6 badge bg-danger" v-if="moment(event.end_date +' '+event.end_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')"
                                    >
                                        <span>{{ trans('em.ended') }}</span>
                                    </h4>
                                    
                                    <!-- out-of-stock -->
                                    <h4 class="mb-0 h6 badge bg-danger" v-if="
                                        !(moment(event.end_date +' '+event.end_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && 
                                        !checkSeatAvailability(moment(event.start_date,'YYYY-MM-DD' ).format('YYYY-MM-DD'))"
                                    >
                                        <span>{{ trans('em.out_of_stock') }}</span>
                                    </h4>

                                    <!-- filling fast -->
                                    <h4 class="mb-0 h6 badge bg-warning mr-3" v-if="
                                        !(moment(event.end_date +' '+event.end_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') < moment().format('YYYY-MM-DD HH:mm:ss')) && positiveInteger(checkSeatAvailability(moment(event.start_date,'YYYY-MM-DD' ).format('YYYY-MM-DD')))"
                                    >
                                        <span>{{ trans('em.filling_fast') }}</span>
                                    </h4>

                                    
                                </div>
                            </div>
                        </a>
                                
                    </div>
                </div>
            </div>
        </div>

        <ticket-component 
            v-if="booking_date && start_time && end_time" 
            :event="event" 
            :tickets="tickets" 
            :max_ticket_qty="max_ticket_qty" 
            :currency="currency"
            :login_user_id="login_user_id"
            :is_admin="is_admin"
            :is_organiser="is_organiser"
            :is_customer="is_customer"
            :is_paypal="is_paypal"
            :is_offline_payment_organizer="is_offline_payment_organizer"
            :is_offline_payment_customer="is_offline_payment_customer"
            :booked_tickets="booked_tickets"
            :is_usaepay="is_usaepay"
        >
        </ticket-component>

        
    </div>
</template>


<script>
import moment from 'moment-timezone';

import { mapState, mapMutations} from 'vuex';
import mixinsFilters from '../../mixins.js';
import TicketComponent from './TicketList.vue';

export default {

    props: [
        'event', 'max_ticket_qty', 'login_user_id',
        'is_admin',
        'is_organiser',
        'is_customer',
        'is_paypal',
        'is_offline_payment_organizer',
        'is_offline_payment_customer',
        'tickets',
        'total_capacity',
        'booked_tickets',
        'currency',
        'date_format',
        'is_usaepay'
    ],

    mixins:[
        mixinsFilters
    ],

     components: {
        'ticket-component'  : TicketComponent
    },

    data() {
        return {
            schedules           : [],
            all_dates           : [],
            selected_dates      : [],
            available_dates     : [],

            moment              : moment,
            calculate_months    : [],
            
            // local_time       
            local_from_date    : [],
            local_to_date      : [],
            local_from_time    : [],
            local_to_time      : [],
            local_start_date   : null,
            local_end_date     : null,

            // tab active
            tab_active_index   : 0,

            //weekly
            week_numbers       : [],
            
        }
    },

    computed: {
        // get global variables
        ...mapState( ['booking_date', 'start_time', 'end_time', 'booking_end_date', 'booked_date_server' ]),
        
    },

    methods: {

          // update global variables
        ...mapMutations(['add', 'update']),

        selectDates(booking_date, booking_end_date, start_time, end_time)
        {
            this.add({
                booking_date        : (moment(booking_date,'dddd LL').isValid())  ? booking_date : null,
                booked_date_server  : (moment(booking_date,'dddd LL').isValid())  ? this.serverTimezone(booking_date+' '+start_time, "dddd LL hh:mm A").format('YYYY-MM-DD') : null,
                booking_end_date    : (moment(booking_end_date, 'dddd LL').isValid()) ? booking_end_date : null,
                start_time          : (moment(start_time, 'hh:mm A').isValid()) ? start_time : null,
                end_time            : (moment(end_time, 'hh:mm A').isValid())   ? end_time : null,
            })
        
        },
        // getSchedule
        getEventSchedule()
        {
            let post_url = route('eventmie.event_schedule') ;
            let post_data = {
                'event_id'         : this.event.id,
            };

            axios.post(post_url, post_data)
            .then(res => {
                var _this = this;

                this.schedules   = res.data.schedules;
                
                //server time convert into local timezone
                this.convert_to_local_tz();
                
                // count and calculate months after get schedule
                // this.calculate_months = this.countMonth(this.schedules[0].from_date, this.schedules[this.schedules.length - 1].from_date);
                this.calculate_months = [];
                this.schedules.forEach(function(value, key) {
                    
                    _this.calculate_months[key] = moment(value['from_date'], 'YYYY-MM-DD').format('YYYY-MM');
                });

                // generate dates
                this.generateDates();

                
                
                this.schedules.every(function(schedule, index) {
                    
                    if(schedule.from_time != null && schedule.to_time != null){
                        _this.tab_active_index = index;
                        return false;   
                        
                    }else{
                        return true;
                    }
                });
               
            })
            .catch(error => {
                let serrors = Vue.helpers.axiosErrors(error);
                if (serrors.length) {
                    this.serverValidate(serrors);
                }
            });
        },
        
        // generates all dates
        generateDates() {
            
            this.calculate_months.forEach(function(value, key) {
                
                this.all_dates[key]   = [];
                // make months like 2019-3
                var  month            = moment(this.local_from_date[key], "YYYY-MM-DD").format("YYYY-MM");
                
                //count days in one month
                var  count_days       = moment(this.local_from_date[key], "YYYY-MM-DD").daysInMonth();

                var i=1;
                while( i <= count_days)
                {
                    // make dates object of moment according to months and year
                    this.all_dates[key].push(moment(month+'-'+i, "YYYY-MM-DD"));
                    i++;
                    
                }
            }.bind(this));    
            
            if(this.schedules[0].repetitive_type==1)
            {
                // generates ddates for daily event
                this.generateDaily();
            }
            else if(this.schedules[0].repetitive_type==2)
            {
                // generates selected dates for weekly event
                this.generateWeekly();
            }
            else if(this.schedules[0].repetitive_type==3)
            {
                
                this.generateMonthly();
            }
        },

        // generates selected dates for monthly event
        generateMonthly() {
            // generates selected dates
            var $this = this;
            this.all_dates.forEach(function(ad_value, ad_key) {
                
                var schedules_dates = [];

                
                if($this.schedules[ad_key].repetitive_dates == null)
                    return true;

                JSON.parse($this.schedules[ad_key].repetitive_dates.split(',')).forEach(function(v, k) {
                    schedules_dates[k] = v;
                });

                $this.selected_dates[ad_key]        = [];
                $this.available_dates[ad_key]       = [];
                ad_value.forEach(function(date, key) {
                    
                    if(schedules_dates.includes(moment(date).locale('en').format('DD'))) {
                        
                        if(ad_key == 0 && Object.keys($this.calculate_months).length != 1)
                        {
                            // selected date must be grather than start_date of event
                            if(moment($this.local_start_date, "YYYY-MM-DD").locale('en').format('YYYY-MM-DD') <= moment(date).locale('en').format('YYYY-MM-DD'))
                            {
                                $this.selected_dates[ad_key].push(moment(date).format('DD'));
                                // live-seat availability check
                                $this.addAvailableDates(ad_key, moment(date, 'YYYY-MM-DD').format('YYYY-MM-DD'), $this, true);
                            }    
                        }
                        else if(ad_key == ($this.all_dates.length -1) && Object.keys($this.calculate_months).length != 1)
                        {
                            
                            // selected date must be less than end_date of event
                            if(moment($this.local_end_date, "YYYY-MM-DD").locale('en').format('YYYY-MM-DD') >= moment(date).locale('en').format('YYYY-MM-DD'))
                            {
                                $this.selected_dates[ad_key].push(moment(date).format('DD'));
                                // live-seat availability check
                                $this.addAvailableDates(ad_key, moment(date, 'YYYY-MM-DD').format('YYYY-MM-DD'), $this, true);
                            }    
                        }
                        
                        if(Object.keys($this.calculate_months).length == 1)
                        {
                            
                              // selected date must be less than end date and grather than start date
                            if(moment($this.local_start_date, "YYYY-MM-DD").locale('en').format('YYYY-MM-DD') <= moment(date).locale('en').format('YYYY-MM-DD') 
                                && moment($this.local_end_date, "YYYY-MM-DD").locale('en').format('YYYY-MM-DD') >= moment(date).locale('en').format('YYYY-MM-DD'))
                            {
                                $this.selected_dates[ad_key].push(moment(date).format('DD'));
                                // live-seat availability check
                                $this.addAvailableDates(ad_key, moment(date, 'YYYY-MM-DD').format('YYYY-MM-DD'), $this, true);
                            }    
                        }
                        else if(Object.keys($this.calculate_months).length != 1 && ad_key != 0 && ad_key != ($this.all_dates.length -1)) 
                        {
                            
                            $this.selected_dates[ad_key].push(moment(date).format('DD'));
                            // live-seat availability check
                            $this.addAvailableDates(ad_key, moment(date, 'YYYY-MM-DD').format('YYYY-MM-DD'), $this, true);
                        }
                    }
                });

            }); 
            
            
        },

        // generates selected dates for daily event
        generateDaily() {
            
            var $this = this;
            this.all_dates.forEach(function(ad_value, ad_key) {

                var schedules_dates = [];
                var tmp             = null;
                var month           = null;

                
                if($this.schedules[ad_key].repetitive_dates == null)
                    return true;
                
                JSON.parse($this.schedules[ad_key].repetitive_dates.split(',')).forEach(function(v, k) {
                    
                    // make month like 2019-6 and it server side months
                    month               = moment($this.schedules[ad_key].from_date).format("YYYY-MM");
                    
                    //meke date like 2019-12-3
                    
                    tmp                 = moment(month +'-'+v).format("YY-MM-DD");
                    
                    // convert tmp to number
                    schedules_dates[k]  = moment(tmp, "YY-MM-DD").format('DD'); 
                     
                });
             
                //store all number of dates into all_dates_number variable
                var all_dates_number    = [];
                ad_value.forEach(function(v, k) {

                    if(ad_key == 0  && Object.keys($this.calculate_months).length != 1)
                    {
                        // selected date must be grather than start_date of event
                        if(moment($this.local_start_date,"YYYY-MM-DD").locale('en').format('YYYY-MM-DD') <= moment(v).locale('en').format('YYYY-MM-DD'))
                            all_dates_number[k] = moment(v).format('DD');
                    }
                    else if(ad_key == ($this.all_dates.length -1) &&  Object.keys($this.calculate_months).length != 1)
                    {
                        // selected date must be less than end_date of event
                        if(moment($this.local_end_date, "YYYY-MM-DD").locale('en').format('YYYY-MM-DD') >= moment(v).locale('en').format('YYYY-MM-DD'))
                            all_dates_number[k] = moment(v).format('DD');
                    }
                    else if(Object.keys($this.calculate_months).length == 1)
                    {
                        if(moment($this.local_start_date, "YYYY-MM-DD").locale('en').format('YYYY-MM-DD') <= moment(v).locale('en').format('YYYY-MM-DD') 
                                && moment($this.local_end_date, "YYYY-MM-DD").locale('en').format('YYYY-MM-DD') >= moment(v).locale('en').format('YYYY-MM-DD'))
                        {
                            all_dates_number[k] = moment(v).format('DD');
                        }    
                    }
                    else if(Object.keys($this.calculate_months).length != 1 && 
                                ad_key != 0 && 
                                ad_key != ($this.all_dates.length -1))
                    {
                        all_dates_number[k] = moment(v).format('DD');
                    }
                });

                
                // live-seat availability check
                $this.available_dates[ad_key]     = [];
                // get diffrence between all_dates_number and schedules_dates
                let difference                   = all_dates_number.filter(function(x) {
                    if(!schedules_dates.includes(x)) {

                        // live-seat availability check
                        $this.addAvailableDates(ad_key, x, $this);

                        return true;
                    }
                });

                $this.selected_dates[ad_key]     = []; 
                $this.selected_dates[ad_key]     = difference; 
            });    
        },

         // generates selected dates for weekly event
        generateWeekly() {
            
            // generates selected dates
            var $this = this;
            
            this.all_dates.forEach(function(ad_value, ad_key) {
                
                var schedules_dates = [];
                
                if($this.schedules[ad_key].repetitive_days == null)
                    return true;

                $this.schedules[ad_key].repetitive_days.split(',').forEach(function(v, k) {
                    
                    if(Number(v)==1)
                        schedules_dates[k] = "Sunday";
                    if(Number(v)==2)
                        schedules_dates[k] = "Monday";
                    if(Number(v)==3)
                        schedules_dates[k] = "Tuesday";
                    if(Number(v)==4)
                        schedules_dates[k] = "Wednesday";
                    if(Number(v)==5)
                        schedules_dates[k] = "Thursday";
                    if(Number(v)==6)
                        schedules_dates[k] = "Friday";      
                    if(Number(v)==7)
                        schedules_dates[k] = "Saturday";            
                });
            
                $this.selected_dates[ad_key]     = []; 
                ad_value.forEach(function(date, key) {
                    
                    if(schedules_dates.includes(String(date.locale('en').format('dddd')))) {
                    
                        if(ad_key == 0 && Object.keys($this.calculate_months).length != 1)
                        {
                            // selected date must be grather than start_date of event
                            if(moment($this.local_start_date,  "YYYY-MM-DD").locale('en').format('YYYY-MM-DD') <= moment(date).locale('en').format('YYYY-MM-DD'))
                                $this.selected_dates[ad_key].push((date.format('DD')));
                        }
                        else if(ad_key == ($this.all_dates.length -1) && Object.keys($this.calculate_months).length != 1)
                        {
                            // selected date must be less than end_date of event
                            if(moment($this.local_end_date, "YYYY-MM-DD").locale('en').format('YYYY-MM-DD') >= moment(date).locale('en').format('YYYY-MM-DD'))
                                $this.selected_dates[ad_key].push((date.format('DD')));
                        }
                        else if(Object.keys($this.calculate_months).length == 1)
                        {
                            if(moment($this.local_start_date, "YYYY-MM-DD").locale('en').format('YYYY-MM-DD') <= moment(date).locale('en').format('YYYY-MM-DD') 
                                    && moment($this.local_end_date, "YYYY-MM-DD").locale('en').format('YYYY-MM-DD') >= moment(date).locale('en').format('YYYY-MM-DD'))
                            {
                                $this.selected_dates[ad_key].push((date.format('DD')));
                            }    
                        }
                        else if(Object.keys($this.calculate_months).length != 1 && 
                                ad_key != 0 && 
                                ad_key != ($this.all_dates.length -1))
                        {
                            $this.selected_dates[ad_key].push((date.format('DD')));
                        }

                        // live-seat availability check
                        $this.available_dates[ad_key]     = [];
                        $this.selected_dates[ad_key].forEach(function(ad_v, ad_k) {
                            $this.available_dates[ad_key].push($this.checkSeatAvailability(moment($this.userTimezone($this.dateToFullDate(ad_v+' '+$this.schedules[ad_key].from_time, $this.schedules[ad_key].from_date), 'dddd LL').format('dddd LL'),'dddd LL').format('YYYY-MM-DD'), $this));
                        });
                        
                    }
                });
            }); 
            
            this.weekly();
        },

        // server time convert into local timezone
        convert_to_local_tz(){
            // convert all schedules to local timezone
            this.schedules.forEach(function(value, key) {
                
                // this.local_from_date    = [];
                // this.local_to_date      = [];
                // this.local_from_time[key]    = [];
                // this.local_to_time[key]      = [];
                
                this.local_from_date[key] = this.schedules[key].from_date;
                this.local_to_date[key]   = this.schedules[key].to_date;
                this.local_from_time[key] = moment(this.schedules[key].from_time, "HH:mm:ss").format(date_format.vue_time_format);
                this.local_to_time[key]   = moment(this.schedules[key].to_time, "HH:mm:ss").format(date_format.vue_time_format);


            }.bind(this));
                
            // convert all dates to local timezone
            this.local_start_date   = this.userTimezone(this.event.start_date+' '+this.event.start_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD');
            this.local_end_date     = this.userTimezone(this.event.end_date+' '+this.event.end_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD');
        },

        //single event
        singleEvent(){
            if(this.event.repetitive <= 0 )
            {
               
                this.add({
                    booking_date        : moment(this.event.start_date, "YYYY-MM-DD").format('dddd LL'),
                    booked_date_server  : this.serverTimezone(this.event.start_date+' '+this.event.start_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD'),
                    start_time          : this.userTimezone(this.event.start_date+' '+this.event.start_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss'),
                    end_time            : this.userTimezone(this.event.end_date+' '+this.event.end_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss'),
                })
            }
        },
        
        // week number group for merger_schedule weekly
        weekly(){
            var $this = this;
            
            this.selected_dates.forEach(function(value, key){
                var week_number = "week_number"
                $this.week_numbers[key] = [];
                value.forEach(function(val1, key1) {
                    
                    if(!$this.week_numbers[key].includes(moment($this.userTimezone($this.dateToFullDate(val1+' '+$this.schedules[key].from_time, $this.schedules[key].from_date), 'dddd LL').format('dddd LL'),'dddd LL').isoWeek())) {
                        $this.week_numbers[key].push(moment($this.userTimezone($this.dateToFullDate(val1+' '+$this.schedules[key].from_time, $this.schedules[key].from_date ), 'dddd LL').format('dddd LL'),'dddd LL').isoWeek());
                    }
                });
            });

        },

        //weekly  first date merger_schedule weekly
        getWeekFirstDate(item2, selected_dates, schedules){
            var $this = this;

            var tmp = [];
            selected_dates.forEach(function(selected_date, key){
                if(item2 == moment($this.dateToFullDate(selected_date, schedules),'dddd LL').isoWeek()) {
                    tmp.push($this.dateToFullDate(selected_date, schedules));
                }    
            });
            
            return tmp[0];
        },
        
        //weekly  last date merger_schedule weekly
        getWeekLastDate(item2, selected_dates, schedules){
            var $this = this;

            var tmp = [];
            selected_dates.forEach(function(selected_date, key){
                if(item2 == moment($this.dateToFullDate(selected_date, schedules),'dddd LL').isoWeek()) {
                    tmp.push($this.dateToFullDate(selected_date, schedules));
                }    
            });
            

            return tmp[tmp.length-1];
        },

        // live seat availability check
        checkSeatAvailability(schedule_date, $this) {
            if (typeof($this) == "undefined")
                $this = this;
            // don't check availability if event is expired 
            if(moment(schedule_date).format('YYYY-MM-DD') < moment().format('YYYY-MM-DD')) {
                return true;
            }
            
            // this must be checked after proper mounting
            // for that, we can check for total_capacity
            // if total_capacity > 0 then it's a sign that mount success
            var total_capacity_temp = $this.total_capacity;
            // count total_booked for each date
            // and then calculate total_capacity - total_booked
            // if it's 0
            // then show schedule out-of-stock
            var total_booked        = 0;
            var return_val          = true;
            if(total_capacity_temp > 0) {
                for(var index in $this.tickets) {
                    var ticket_id = $this.tickets[index].id;
                    if (typeof($this.booked_tickets[ticket_id+'-'+schedule_date]) != "undefined") {
                        if($this.booked_tickets[ticket_id+'-'+schedule_date].total_booked > 0) {
                            total_booked += parseInt($this.booked_tickets[ticket_id+'-'+schedule_date].total_booked);
                        } 
                    }
                }
            } 

            // only return total_available in case of any booked ticket on a schedule.
            // calculate total_capacity - total_booked for each schedule
            if(total_booked > 0)
                return parseInt(total_capacity_temp) - parseInt(total_booked);
            
            // return true as default, or else it'll show filling fast on all dates.
            return return_val;
        },

        addAvailableDates(ad_key, ad_v, $this, is_full_date) {
            if(typeof(is_full_date) == "undefined")
                $this.available_dates[ad_key].push($this.checkSeatAvailability(moment($this.dateToFullDate(ad_v+' '+$this.schedules[ad_key].from_time, $this.schedules[ad_key].from_date),'dddd LL').format('YYYY-MM-DD'), $this));
            else
                $this.available_dates[ad_key].push($this.checkSeatAvailability(ad_v, $this));
        },

    },
    mounted() {
        if(this.event.repetitive > 0)
            this.getEventSchedule();
        
    },  
}
</script>