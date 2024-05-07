<template>
    <div>
        <div id="Bdetail" class="tab-pane">
            <div class="panel-group">
                <div class="panel panel-default lgx-panel">
                    <div class="panel-heading px-5">
                        <form id="form" class="form-horizontal" ref="form" :action="submitUrl()"
                            @submit.prevent="validateForm" method="POST">
                            <input type="hidden" name="_token" id="csrf-token" :value="csrf_token" />
                            <div class="form-group row mt-3">
                                <label class="col-md-3 form-label">{{
                                trans("em.bank_name")
                                }}</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="bank_name" type="text" v-model="bank_name" />
                                    <span v-show="errors.has('bank_name')" class="help text-danger">{{
                                    errors.first("bank_name") }}</span>
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <label class="col-md-3 form-label">{{
                                trans("em.bank_code")
                                }}</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="bank_code" type="text" v-model="bank_code" />
                                    <span v-show="errors.has('bank_code')" class="help text-danger">{{
                                    errors.first("bank_code") }}</span>
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <label class="col-md-3 form-label">{{
                                trans("em.bank_branch_name")
                                }}</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="bank_branch_name" type="text"
                                        v-model="bank_branch_name" />
                                    <span v-show="errors.has('bank_branch_name')" class="help text-danger">{{
                                    errors.first("bank_branch_name")
                                    }}</span>
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <label class="col-md-3 form-label">{{
                                trans("em.bank_branch_code")
                                }}</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="bank_branch_code" type="text"
                                        v-model="bank_branch_code" />
                                    <span v-show="errors.has('bank_branch_code')" class="help text-danger">{{
                                    errors.first("bank_branch_code")
                                    }}</span>
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <label class="col-md-3 form-label">{{
                                trans("em.bank_account_number")
                                }}</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="bank_account_number" type="text"
                                        v-model="bank_account_number" />
                                    <span v-show="
                                        errors.has('bank_account_number')
                                    " class="help text-danger">{{
                                    errors.first("bank_account_number")
                                    }}</span>
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <label class="col-md-3 form-label">{{
                                trans("em.bank_account_name")
                                }}</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="bank_account_name" type="text"
                                        v-model="bank_account_name" />
                                    <span v-show="errors.has('bank_account_name')" class="help text-danger">{{
                                    errors.first("bank_account_name")
                                    }}</span>
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <label class="col-md-3 form-label">{{
                                trans("em.bank_account_phone")
                                }}</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="bank_account_phone" type="text"
                                        v-model="bank_account_phone" />
                                    <span v-show="
                                        errors.has('bank_account_phone')
                                    " class="help text-danger">{{
                                    errors.first("bank_account_phone")
                                    }}</span>
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <div class="col-md-9 offset-md-3">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-sd-card"></i>
                                        {{ trans("em.save") }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
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
            bank_name: null,
            bank_code: null,
            bank_branch_name: null,
            bank_branch_code: null,
            bank_account_number: null,
            bank_account_name: null,
            bank_account_phone: null
        };
    },
    methods: {

        editProfile() {
            (this.bank_name = this.user.bank_name),
                (this.bank_code = this.user.bank_code),
                (this.bank_branch_name = this.user.bank_branch_name),
                (this.bank_branch_code = this.user.bank_branch_code),
                (this.bank_account_number = this.user.bank_account_number),
                (this.bank_account_name = this.user.bank_account_name),
                (this.bank_account_phone = this.user.bank_account_phone);
        },

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
            return route("eventmie.updateBankUser");
        },
    },
    mounted() {
        this.editProfile();
    }
};
</script>
