<template>
    <div class="row">
        <div class="col-md-12">
            <div class="modal show" v-if="add_attendee > 0">
                <div class="modal-dialog modal-lg w-55 modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5">{{ trans('em.create') + ' ' + trans('em.user') }}</h1>
                            <button type="button" class="btn btn-sm bg-danger close" @click="close()"><span aria-hidden="true">&times;</span></button>
                            
                        </div>
                        <form ref="form" @submit.prevent="validateForm" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name">{{ trans('em.name') }}<sup>*</sup></label>
                                    <input type="text" class="form-control lgxname" v-model="name" name="name" v-validate="'required'">
                                    <span v-show="errors.has('name')" class="help text-danger">{{ errors.first('name') }}</span>
                                </div>
                                <div class="mb-3">
                                    <label for="email">{{ trans('em.email') }}<sup>*</sup></label>
                                    <input type="text" class="form-control lgxname" v-model="email" name="email" v-validate="'required'|email">
                                    <span v-show="errors.has('email')" class="help text-danger">{{ errors.first('email') }}</span>
                                </div>
                                <div class="mb-3">
                                    <label>{{ trans('em.phone') }}</label>
                                    <input type="text" class="form-control" name="phone" v-model="phone" v-validate="'required'">
                                    <span v-show="errors.has('phone')" class="help text-danger">{{ errors.first('phone') }}</span>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" :class="{ 'disabled': disable }" :disabled="disable" class="btn btn-primary">
                                    <i class="fas fa-sd-card"></i> {{ trans('em.save') }}
                                </button>
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
    props: ["add_attendee"],
    mixins: [mixinsFilters],
    data() {
        return {
            name: '',
            email: '',
            phone: '',
            disable: false
        };
    },
    methods: {
        // reset form and close modal
        close() {
            this.$refs.form.reset();
            this.$parent.add_attendee = 0;
        },
        // validate data on form submit
        validateForm() {
            this.$validator.validateAll().then((result) => {
                if (result) {
                    this.formSubmit();
                }
            });
        },
        // show server validation errors
        serverValidate(serrors) {
            this.$validator.errors.add(serrors);
        },
        // submit form
        formSubmit() {
            // show loader
            this.showLoaderNotification(trans('em.processing'));

            // prepare form data for post request
            this.disable = true;

            let post_url = route('eventmie.add_attendee');
            let post_data = new FormData();
            post_data.append('name', this.name);
            post_data.append('email', this.email);
            post_data.append('phone', this.phone);

            // axios post request
            axios.post(post_url, post_data)
                .then(res => {
                    this.close();
                    this.showNotification('success', trans('em.user') + ' ' + trans('em.saved') + ' ' + trans('em.successfully'));

                    if (res.data.status) {
                        Swal.hideLoading();
                        this.disable = false;
                        this.$parent.customer = res.data.attendee;

                        if (this.$parent.options.length > 0)
                            this.$parent.options.push(res.data.attendee);
                        else
                            this.$parent.options = res.data.customer_options;
                    } else {
                        this.showNotification('error', res.data.message);
                        Swal.hideLoading();
                        this.disable = false;
                    }
                })
                .catch(error => {
                    let serrors = Vue.helpers.axiosErrors(error);
                    if (serrors.length) {
                        this.serverValidate(serrors);
                    }
                });
        }
    }
}
</script>
