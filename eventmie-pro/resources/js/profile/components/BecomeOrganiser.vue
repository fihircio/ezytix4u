<template>
    <div>
        <div id="BecomeOrganiser" class="tab-pane">
            <div class="panel-group">
                <div class="panel panel-default">
                    <div v-if="user.organisation">
                        <div class="alert alert-info" role="alert">
                            <strong>{{ trans("em.become_organiser_notification") }}</strong>
                        </div>
                    </div>

                    <h3 class="mb-0">{{ trans('em.become_organiser') }}</h3>
                    <p class="mb-0">{{ trans('em.become_organizer_to_host') }}</p>

                    <div class="card border-0 shadow-sm bg-light my-3">
                        <div class="card-body p-4 fs-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0 text-primary">{{ trans('em.how_it_works') }}</h4>
                            </div>
                            <div class="d-flex">
                                <div>
                                    <span><i class="fas fa-arrow-right text-primary"></i></span>
                                </div>
                                <div class="ms-3 lh-1">
                                    <h5 class="mb-1">{{ trans('em.organiser_note_1') }}</h5>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div>
                                    <span><i class="fas fa-arrow-right text-primary"></i></span>
                                </div>
                                <div class="ms-3 lh-1">
                                    <h5 class="mb-1">{{ trans('em.organiser_note_2') }}</h5>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div>
                                    <span><i class="fas fa-arrow-right text-primary"></i></span>
                                </div>
                                <div class="ms-3 lh-1">
                                    <h5 class="mb-1">{{ trans('em.organiser_note_3') }}</h5>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div>
                                    <span><i class="fas fa-arrow-right text-primary"></i></span>
                                </div>
                                <div class="ms-3 lh-1">
                                    <h5 class="mb-1">{{ trans('em.organiser_note_4') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <form class="form-horizontal" ref="form" :action="submitUrl()" @submit.prevent="validateForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="_token" id="csrf-token" :value="csrf_token" />
                        <input type="hidden" name="role_id" value="3">

                        <div class="form-group row mt-3">
                            <label class="col-md-3 form-label">{{ trans("em.organization") }}</label>
                            <div class="col-md-9">
                                <input class="form-control" name="organisation" v-model="organisation" type="text" :placeholder="trans('em.brand_identity')" required>
                                <span v-show="errors.has('organisation')" class="help text-danger">{{ errors.first("organisation") }}</span>
                            </div>
                        </div>

                        <div class="form-group row mt-3">
                            <div class="col-md-9 offset-md-3">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-sd-card"></i>
                                    {{ trans("em.submit") }}
                                </button>
                            </div>
                        </div>

                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import mixinsFilters from "../../mixins.js";
export default {
    props: ["user", "csrf_token"],
    mixins: [mixinsFilters],

    data() {
        return {
            organisation: user.organisation,
        };
    },
    methods: {
        // validate data on form submit
        validateForm(event) {
            this.$validator.validateAll().then(result => {
                if (result) {
                    this.formSubmit(event);
                }
            });
        },

        // show server validation errors
        serverValidate(serrors) {
            this.$validator.validateAll().then(result => {
                this.$validator.errors.add(serrors);
            });
        },

        // submit form
        async formSubmit(event) {
            this.$refs.form.submit();
        },

        submitUrl() {
            return route("eventmie.updateAuthUserRole");
        },
    },
    mounted() {}
};
</script>
