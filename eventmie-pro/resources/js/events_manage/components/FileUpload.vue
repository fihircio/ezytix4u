<template>
    <div>
        <div class="modal modal-mask modal-big" v-if="$parent.seat_ticket_id > 0">
            <div class="modal-dialog modal-xl model-extra-large">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-3">{{ ticket.title }} {{ trans('em.ticket') }} {{ trans('em.seating_chart') }}</h1>
                        <button type="button" class="btn btn-sm bg-danger text-white close" @click="close()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form ref="form" @submit.prevent="validateForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" class="form-control lgxname"  name="ticket_id" v-model="local_ticket.id">
                            <input type="hidden" class="form-control lgxname"  name="event_id" v-model="local_ticket.event_id">
                            
                            <div class="form-group col-md-4 text-wrap">
                                <h4 class="mb-2 form-label">{{ trans('em.upload_seatchart') }}</h4>
                                <input type="file" class="form-control" id="file" ref="file" @change="onChangeFileUpload()">
                            </div>   
                        </form>
                    </div>
                    <seat-component v-if="local_ticket.seatchart != null" :ticket="local_ticket"></seat-component>
                </div>
            </div>
        </div>
    </div>

</template>
  
<script>
import route from '../../ziggy.js';
import mixinsFilters from '../../mixins.js';
import SeatComponent from './Seat.vue';

export default {

    props:['ticket'],
    mixins:[
        mixinsFilters
    ],

    components: {
        SeatComponent,
    },

    data(){
        return {
            local_ticket : this.ticket,   
        }
    },
  
    methods: {
        
        // on change file upload
        onChangeFileUpload(){
            let formData = new FormData(this.$refs.form);

            formData.append('file', this.$refs.file.files[0]);

            axios.post(route('eventmie.upload_seatchart'),
                formData
            ).then(res => {
                this.local_ticket = res.data.ticket; 

                this.showNotification('success', trans('em.seatchart_uploaded'));
            })
            .catch(error => {
                let serrors = Vue.helpers.axiosErrors(error);
                if (serrors.length) {
                    this.serverValidate(serrors);
                }
            });
        },

        // validate data on form submit
        validateForm(event) {
            this.$validator.validateAll().then((result) => {
                if (result) {
                    this.saveSeat(event);            
                }
            });
        },

        // show server validation errors
        serverValidate(serrors) {
            this.$validator.validateAll().then((result) => {
                this.$validator.errors.add(serrors);
            });
        },

        close(){
            this.$parent.getTickets();
            this.$parent.seat_ticket_id = 0;
        }
    
    }
  }
</script>