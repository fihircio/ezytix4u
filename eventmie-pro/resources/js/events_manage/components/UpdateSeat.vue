<template>
    <div class="modal modal-mask modal-big">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-3">{{trans('em.update_seat')}}</h1>
                    <button type="button" class="btn btn-sm bg-danger text-white close" data-dismiss="modal" @click="close()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="name ">{{trans('em.name')}}</label>
                        <input type="text" class="form-control" id="name"  placeholder="change name" v-model="seat_name" >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" @click="deleteSeat()">{{trans('em.delete')}}</button>
                    
                    <button type="button" class="btn btn-dark" v-if="data.id != null && data.status > 0" @click="disableSeat()">{{trans('em.disable')}}</button>
                    <button type="button" class="btn btn-primary" v-if="data.id != null && data.status <= 0" @click="enableSeat()">{{trans('em.enable')}}</button>

                    <button type="button" class="btn btn-success"  @click="updateSeatName()">{{trans('em.save')}}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import mixinsFilters from '../../mixins.js';

export default {
    
    props:['data'],
    mixins:[
        mixinsFilters
    ],

    data()  {
         
        return {
            seat_name : this.data.name,
        }
        
    },

    methods:{
        close(){
            this.$parent.update = 0;
        },

        // change seat name
        updateSeatName(e){
           
            let _this = this;

            this.$parent.seats.forEach((seat, index) => {
                console.log(_this.data);
                
                //update seat name
                if(seat.x == _this.data.x && seat.y == _this.data.y && seat.name == _this.data.name ){
                    seat.name = _this.seat_name;
                    _this.$parent.seats.splice(index, 1, seat);
                }
        
            });    

            setTimeout(function(){ 
                // call parent function
                _this.$parent.saveSeat();

                _this.close();   
                
            }, 1000);
                      
        },

        // delete seat
        deleteSeat(){

            if(this.data.id != null){
                    
                // detele form database    
                axios.post(route('delete_seat'),{
                
                    'seat_id'   : this.data.id,
                    'ticket_id' : this.data.ticket_id

                }).then(res => {
                    
                    //update ticket
                    this.$parent.local_ticket = res.data.ticket

                    //update seats
                    this.$parent.showSelectedSeats();
                    
                    this.showNotification('success', trans('em.seat_deleted'));
                    this.close(); 
                })
                .catch(error => {
                    let serrors = Vue.helpers.axiosErrors(error);
                    if (serrors.length) {
                        this.$parent.serverValidate(serrors);
                    }
                });
            } else{

                // delete form local
                let _this = this;

                this.$parent.seats.forEach((seat, index) => {
                    console.log(_this.data);
                    
                    //delete seat 
                    if(seat.x == _this.data.x && seat.y == _this.data.y && seat.name == _this.data.name ){
                        seat.name = _this.seat_name;
                        _this.$parent.seats.splice(index, 1);
                    }
            
                });  

                this.showNotification('success', trans('em.seat_deleted'));
                this.close();
            }

            
        },

        // disable seat
        disableSeat(){
            // detele form database    
            axios.post(route('eventmie.disable_seat'),{
                
                'seat_id'   : this.data.id,
                'ticket_id' : this.data.ticket_id

            }).then(res => {
                
                //update ticket
                this.$parent.local_ticket = res.data.ticket

                //update seats
                this.$parent.showSelectedSeats();
                
                this.showNotification('success', trans('em.seat_disabled'));
                this.close(); 

            })
            .catch(error => {
                let serrors = Vue.helpers.axiosErrors(error);
                if (serrors.length) {
                    this.$parent.serverValidate(serrors);
                }
            });
        },

        // enable seat
        enableSeat(){
            // detele form database    
            axios.post(route('eventmie.enable_seat'),{
                
                'seat_id'   : this.data.id,
                'ticket_id' : this.data.ticket_id

            }).then(res => {
                
                //update ticket
                this.$parent.local_ticket = res.data.ticket

                //update seats
                this.$parent.showSelectedSeats();
                
                this.showNotification('success', trans('em.seat_enabled'));
                this.close(); 

            })
            .catch(error => {
                let serrors = Vue.helpers.axiosErrors(error);
                if (serrors.length) {
                    this.$parent.serverValidate(serrors);
                }
            });
        },
    }
}
</script>