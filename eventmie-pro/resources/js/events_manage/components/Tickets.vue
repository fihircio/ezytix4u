<template>
    <div>
        
        <button type="button" class="btn btn-primary m-2" @click="openModal_1 = true" v-if="edit_index == null"><i class="fas fa-ticket-alt"></i> {{ trans('em.create_ticket') }}</button>
        <ticket-component v-if="openModal_1" :taxes ="taxes" @changeItem="updateItem" :currency="currency" :openModal_1="openModal_1" ></ticket-component>
        <div class="row">
            <div class="table-responsive">
                <table class="table text-wrap">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th>{{ trans('em.title') }}</th>
                            <th>{{ trans('em.price') }}</th>
                            <th>{{ trans('em.quantity') }}</th>
                            <th>{{ trans('em.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(ticket, index) in tickets"
                            v-bind:key="index" 
                        >
                            <td :data-title="trans('em.title')">{{ ticket.title }}</td>
                            <td :data-title="trans('em.price')">{{ ticket.price+' '+ currency }}</td>
                            <td :data-title="trans('em.quantity')">{{ ticket.quantity }}</td>
                            <td :data-title="trans('em.actions')">
                                <div class="btn-group text-nowrap">
                                    <button type="button" class="btn btn-sm bg-primary text-white" @click="setSeatTicketId(ticket.id)">
                                        {{ trans('em.seating_chart') }}
                                        <span v-if="ticket.seatchart != null" class="badge badge-light">{{ ticket.seatchart.seats.length }}</span>
                                    </button>
                                    <button type="button" class="btn btn-sm bg-info text-black" @click="()=>{edit_index = index; openModal_2 = true}" ><i class="fas fa-edit"></i> {{ trans('em.edit') }}</button>
                                    <button type="button" class="btn btn-sm bg-danger text-black" @click="deleteTicket(ticket.id)"><i class="fas fa-trash"></i> {{ trans('em.delete') }}</button>
                                </div>

                                <ticket-component 
                                    :taxes ="taxes"
                                    :edit_ticket="ticket" 
                                    v-if="edit_index == index" 
                                    @changeItem="updateItem"
                                    :currency="currency"
                                    :openModal_2="openModal_2"
                                ></ticket-component>

                                <file-upload v-if="ticket.id == seat_ticket_id" :ticket="ticket"></file-upload>
                                            
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
                    
    </div>
</template>

<script>

import { mapState, mapMutations} from 'vuex';

import TicketComponent from './Ticket.vue';
import mixinsFilters from '../../mixins.js';
import FileUpload from './FileUpload.vue';

export default {

    data() {
        return {
            edit_index : null,
            taxes      : null,
            currency   : null,
            openModal_1  : false,
            openModal_2  : false,
            seat_ticket_id : 0,
        }
    },

    mixins:[
        mixinsFilters
    ],
    components: {
        TicketComponent,
        FileUpload,
    },
    computed: {
        // get global variables
        ...mapState( ['tickets', 'event_id', 'organiser_id']),
    },
    methods: {
        // update global variables
        ...mapMutations(['add', 'update']),

        // delete tickets
        deleteTicket(ticket_id){
            this.showConfirm(trans('em.ticket_delete_ask')).then((res) => {
                if(res) {
                    axios.post(route('eventmie.tickets_delete'), {
                        ticket_id       : ticket_id,
                        event_id        : this.event_id,
                        organiser_id    : this.organiser_id
                    })
                    .then(res => {
                    
                        if(res.data.status)
                        {
                            this.showNotification('success', trans('em.ticket_delete_success'));
                            this.getTickets();
                        }
                    })
                    .catch(error => {
                        Vue.helpers.axiosErrors(error);
                    });
                }
            })

           
        },

        // get all tickets       
        getTickets() {
            axios.post(route('eventmie.tickets'),{
                'event_id'      : this.event_id,
                'organiser_id'  : this.organiser_id,
            })
            .then(res => {
                // fill data to global sponsors array
                if(res.data.status)
                {
                    this.add({  
                        tickets        : res.data.tickets,
                    });

                }
                else
                {
                    // if status false and have not tickets 
                    this.add({  
                        tickets        : [],
                    });
                }
                
                if(res.data.currency)
                {
                    this.currency  = res.data.currency;
                }
                
            })
            .catch(error => {
                Vue.helpers.axiosErrors(error);
            });
        },  

        getTaxes(){
            axios.get(route('eventmie.tickets_taxes'),{
                'event_id'      : this.event_id,
                'organiser_id'  : this.organiser_id,
            })
            .then(res => {
                if(res.data.status)
                {
                    this.taxes = res.data.taxes;
                }
            
            })
            .catch(error => {
                Vue.helpers.axiosErrors(error);
            });    
        },

        updateItem() {
            this.getTickets();
            // in edit case reload page 
            this.edit_index = null;
            
            
        },

        isDirtyReset() {
            this.add({is_dirty: false});
        },

        setSeatTicketId(ticketId) {
        this.seat_ticket_id = ticketId;
        console.log('seat_ticket_id:', this.seat_ticket_id);  // Log to confirm the id is set
        },
    },
    
    mounted() {
        this.isDirtyReset();
        // if user have no event_id then redirect to details page
        let event_step  = this.eventStep();
        
        if(event_step)
        {
            this.getTaxes();

            if(this.event_id)
                this.getTickets();
        }    
    }
}
</script>