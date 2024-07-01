<template>
    <div class="row">
        <div class="col-md-12">
            <div class="modal modal-mask" v-if="event_id > 0">
                <div class="modal-dialog modal-container">
                    <div class="modal-content lgx-modal-box">
                        <div class="modal-header">
                            <button type="button" class="close" @click="close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="title"> {{ trans('em.private_event') }}</h3>
                        </div>
                        <form ref="form" @submit.prevent="validateForm" method="POST" enctype="multipart/form-data">
                            <input type="hidden" class="form-control lgxname" name="event_id" :value="event_id">
                            <div class="modal-body">
                                <div class="form-group">
                                    <input type="checkbox" class="custom-control-input" :value=1 name="is_private" v-model="is_private">
                                    <label class="custom-control-label"> &nbsp;&nbsp;{{ trans('em.is_private') }}</label>
                                </div>
                                <div class="form-group">
                                    <label for="event_password">{{ trans('em.event') + ' ' + trans('em.password') }}</label>
                                    <input type="text" class="form-control lgxname" v-model="event_password" name="event_password">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn lgx-btn"><i class="fas fa-sd-card"></i> {{ trans('em.save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import mixinsFilters from '../../mixins.js';

export default {
    props: ["event_id", "event"],
    mixins: [
        mixinsFilters
    ],
    data() {
        return {
            event_password: null,
            is_private: false
        }
    },
    methods: {
        close() {
            this.$emit('close'); // Emit the close event to the parent component
        },
        edit() {
            this.event_password = this.event.event_password;
            this.is_private = this.event.is_private;
        },
        validateForm(event) {
            this.$validator.validateAll().then((result) => {
                if (result) {
                    this.formSubmit(event);
                }
            });
        },
        serverValidate(serrors) {
            this.$validator.validateAll().then((result) => {
                this.$validator.errors.add(serrors);
            });
        },
        formSubmit(event) {
            let post_url = route('eventmie.private_event_save'); // Ensure this matches the named route
            let post_data = new FormData(this.$refs.form);
            axios.post(post_url, post_data)
                .then(res => {
                    this.close();
                    this.showNotification('success', trans('em.event') + ' ' + trans('em.password') + ' ' + trans('em.saved') + ' ' + trans('em.successfully'));
                    setTimeout(function () {
                        location.reload(true);
                    }, 1000);
                })
                .catch(error => {
                    let serrors = Vue.helpers.axiosErrors(error);
                    if (serrors.length) {
                        this.serverValidate(serrors);
                    }
                });
        }
    },
    mounted() {
        console.log('EventPassword component mounted with event_id:', this.event_id);
        if (this.event_id > 0) {
            this.edit();
        }
    }
}
</script>
<style scoped>
.modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: table;
    transition: opacity 0.3s ease;
}

.modal-container {
    display: table-cell;
    vertical-align: middle;
}

.modal-content {
    width: 600px;
    margin: 0px auto;
    padding: 20px 30px;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
    transition: all 0.3s ease;
    font-family: Helvetica, Arial, sans-serif;
}
</style>
