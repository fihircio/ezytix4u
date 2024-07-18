<template>
    <div>
        <table class="m-auto">
            <tr>
                <td><i class="fas fa-square seat-disabled-col"></i> {{ trans('em.disabled') }}&nbsp;&nbsp; </td>
                <td><i class="fas fa-square seat-reserved-col"></i> {{ trans('em.reserved') }}&nbsp;&nbsp; </td>
                <td> <i class="fas fa-square seat-available-col"></i> {{ trans('em.available') }}&nbsp; &nbsp;</td>
                <td> <i class="fas fa-square seat-select-col"></i> {{ trans('em.selected') }}</td>
            </tr>
        </table>

        <div class="seat-container">
            <img :src="'/storage/'+local_ticket.seatchart.chart_image" class="seat-img">

            <span class="seat-mark" 
                :class="{'seat-disabled': (seat.status <= 0 && seat.status != null), 'seat-reserved': seat.reserved}"
                :style="{ 'left': seat.x, 'top' :seat.y }" 
                :id="'seatmark_id_'+ticket.id+'_'+index"
                v-for="(seat, index) in seats"
                v-bind:key="index"
                @click="increaseTicketQuantity(index)"
            >{{seat.name}}</span>
            <input class="seat-checkbox" type="checkbox" 
                    v-for="(seat, index) in seats"
                    :style="{ 'left': seat.x, 'top' :seat.y }" 
                    :key="index+300" 
                    :name="'seat_id_'+ticket.id+'[]'"
                    :id="'seat_id_'+ticket.id+'_'+index"
                    :value="seat.id"
            >
        </div>
        
    </div>

</template>
<script>
import { mapState, mapMutations} from 'vuex';
import mixinsFilters from '../../mixins.js';

export default {
    props:['ticket', 'ticket_index','max_ticket_qty', 'event'],
    mixins:[
        mixinsFilters
    ],
    data()  {
        return {
            local_ticket : this.ticket,
            seats   : [],
            count   : 0,
        }
    },
    computed: {
        // get global variables
        ...mapState( ['booking_date', 'start_time', 'end_time', 'booking_end_date', 'booked_date_server']),
    },
    methods: {
        // Show seats on image according to coordinates
        showSelectedSeats() {
            this.seats = [];
            this.count = 0;
            let _this  = this;

            // check seats
            if(this.local_ticket.seatchart != null && this.local_ticket.seatchart.seats.length > 0) {

                this.local_ticket.seatchart.seats.forEach((value, index) => {
                    
                    //increment count variable and check is number or not
                    if(!isNaN(value.name))
                        _this.count            = parseInt(value.name);

                    // comma saprated value convert into array    
                    let coordinates        = value.coordinates.split(',');
                    
                    // show reserved seats
                    let reserved           = false;
                    _this.local_ticket.attendees.forEach(function(attendee, key){
                        // check for specific date
                        // non-repetitive event
                        if(_this.event.repetitive <= 0) {
                            if(attendee.seat_id == value.id && attendee.booking.status > 0) {
                                reserved = true;
                            }
                        } else {
                            if(attendee.seat_id == value.id && _this.userTimezone(attendee.booking.event_start_date+' '+attendee.booking.event_start_time, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD') == _this.booked_date_server && attendee.booking.status > 0) {
                                reserved = true;
                            }
                        }
                    });
                        
                    // create seat
                    let seat = {
                        'x'           : coordinates[0],
                        'y'           : coordinates[1],
                        'name'        : value.name,
                        'id'          : value.id,
                        'ticket_id'   : _this.local_ticket.id,
                        'reserved'    : reserved,
                        'status'      : value.status,
                    };

                    // created seat push into seats array
                    _this.seats.push(seat);
                    
                });

            }
        },

        //increase or decrease ticket quantity through checkbox
        increaseTicketQuantity(index = null) {

            // seatcheckbox
            let seat_id      = 'seat_id_'+this.ticket.id+'_'+index;
            let seat         = document.getElementById(seat_id);

            // seatmark
            let seatmark_id  = 'seatmark_id_'+this.ticket.id+'_'+index;
            let seatmark     = document.getElementById(seatmark_id);

            // select seat only if, it's !reserved !disabled
            if(seatmark.classList.contains('seat-disabled') || seatmark.classList.contains('seat-reserved')) {
                return false;
            }

            //increase or decrease ticket quantity
            if (seat.checked == true ) {
                // uncheck if checked & decrease quantity
                seat.checked = false;
                seatmark.classList.remove('seat-select');
                this.$parent.quantity[this.ticket_index] = parseInt(this.$parent.quantity[this.ticket_index]) - parseInt(1) ;
            } else if(this.$parent.quantity[this.ticket_index] < this.max_ticket_qty)  {
                // check if unchecked & increase quantity
                seat.checked = true;
                seatmark.classList.add('seat-select');
                this.$parent.quantity[this.ticket_index] = parseInt(this.$parent.quantity[this.ticket_index]) + parseInt(1);
            }

            this.$parent.totalPrice();
            this.$parent.orderTotal();
            this.$parent.defaultPaymentMethod();
            this.$parent.resetPromocode();
            this.$parent.promocodeReward();
            

            // select upto max_ticket_qty
            if(this.$parent.quantity[this.ticket_index] >= this.max_ticket_qty) {
                this.showNotification('error', trans('em.seat_max_error'));
                return false;
            }
        },

    },

    mounted() {
        this.showSelectedSeats();
    }   

}
</script>
