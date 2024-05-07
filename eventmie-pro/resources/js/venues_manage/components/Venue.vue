<template>

    <div class="custom_model">
    
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-secondary" @click="openModal = true" v-if="!edit_venue"><i class="fa fa-map-location"></i> {{ trans('em.add_venue') }}</button>

        <div class="modal show" v-if="openModal">
            <div class="modal-dialog modal-lg w-100">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-3" id="exampleModalLabel">{{ !edit_venue ? trans('em.new') : trans('em.edit') }} {{ trans('em.venue') }}</h1> 
                        <button type="button" class="btn btn-sm bg-danger text-white close" @click="close()"><span aria-hidden="true">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <form ref="form" @submit.prevent="validateForm" method="POST" enctype="multipart/form-data">
                            <input v-if="edit_venue" type="hidden" class="form-control lgxname"  name="venue_id" v-model="edit_venue.id">
                            <input type="hidden" class="form-control lgxname"  name="organiser_id" v-model="organiser_id">

                            <div class="modal-body">

                                <div class="mb-3">
                                    <label class="form-label">{{ trans('em.venue_image_label') }}*</label>
                                    <input multiple="multiple" type="file" class="form-control" ref="images" name="images[]" >
                                    <span class="help-block">{{ trans('em.venue_image') }}</span>
                                    <span v-show="errors.has('images')" class="help text-danger">{{ errors.first('images') }}</span>
                                    
                                    <div class="col-md-12">
                                        <div class="row" v-if="multiple_images.length > 0">
                                            <div class="col-3"
                                                v-for="(image,index) in multiple_images" 
                                                :key="index">
                                                
                                                <button type="button" class="btn-remove-image" @click="deleteImages(image)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <img :src="'/storage/'+image" class="img-fluid img-rounded m-2">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CUSTOM -->
                                <map-autocomplete></map-autocomplete>
                                <!-- CUSTOM -->
                                
                                <div class="mb-3">
                                    <label class="form-label" for="glat">Google {{ trans('em.map_lat') }}</label>
                                    <input type="text" class="form-control" name="glat" placeholder="e.g 27.1751448" v-model="glat" @change="isDirty()">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="glong">Google {{ trans('em.map_long') }}</label>
                                    <input type="text" class="form-control" name="glong" v-model="glong" placeholder="78.0399535" @change="isDirty()">
                                    
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label" for="name">{{ trans('em.venue_title') }}<sup>*</sup></label>
                                    <input type="text" class="form-control lgxname" v-model="title" name="title" v-validate="'required'">
                                    <span v-show="errors.has('title')" class="help text-danger">{{ errors.first('title') }}</span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="name">{{ trans('em.slug') }} ({{ trans('em.venue_url') }})<sup>*</sup></label>
                                    <input type="text" class="form-control lgxname" name="slug" v-model="slug" v-validate="'required'" >
                                    <p class="help text-wrap">{{ slugUrl() }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="description">{{ trans('em.description') }}</label>
                                    <textarea class="form-control" rows="3" name="description" :value="description" style="display:none;"></textarea>
                                    <ckeditor  v-model="description"></ckeditor>
                                    <span v-show="errors.has('description')" class="help text-danger">{{ errors.first('description') }}</span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="venue_type">{{ trans('em.venue_type') }}<sup>*</sup></label>
                                    <input type="text" class="form-control lgxname" v-model="venue_type" name="venue_type" :placeholder="trans('em.venue_type_ie')">
                                    <span v-show="errors.has('venue_type')" class="help text-danger">{{ errors.first('venue_type') }}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label" for="amenities">{{ trans('em.amenities') }}</label>
                                    <textarea class="form-control" rows="3" name="amenities" :value="amenities" style="display:none;"></textarea>
                                    <ckeditor  v-model="amenities"></ckeditor>
                                    <span v-show="errors.has('amenities')" class="help text-danger">{{ errors.first('amenities') }}</span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="seated_guestnumber">{{ trans('em.seated_guestnumber') }}<sup>*</sup></label>
                                    <input type="number" class="form-control lgxname" v-model="seated_guestnumber" name="seated_guestnumber" >
                                    <span v-show="errors.has('seated_guestnumber')" class="help text-danger">{{ errors.first('seated_guestnumber') }}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label" for="standing_guestnumber">{{ trans('em.standing_guestnumber') }}<sup>*</sup></label>
                                    <input type="number" class="form-control lgxname" v-model="standing_guestnumber" name="standing_guestnumber" >
                                    <span v-show="errors.has('standing_guestnumber')" class="help text-danger">{{ errors.first('standing_guestnumber') }}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label" for="neighborhoods">{{ trans('em.neighborhoods') }}<sup>*</sup></label>
                                    <input type="text" class="form-control lgxname" v-model="neighborhoods" name="neighborhoods" >
                                    <span v-show="errors.has('neighborhoods')" class="help text-danger">{{ errors.first('neighborhoods') }}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label" for="pricing">{{ trans('em.pricing') }}</label>
                                    <textarea class="form-control" rows="3" name="pricing" :value="pricing" style="display:none;"></textarea>
                                    <ckeditor  v-model="pricing"></ckeditor>
                                    <span v-show="errors.has('pricing')" class="help text-danger">{{ errors.first('pricing') }}</span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="availability">{{ trans('em.availability') }}</label>
                                    <textarea class="form-control" rows="3" name="availability" :value="availability" style="display:none;"></textarea>
                                    <ckeditor  v-model="availability"></ckeditor>
                                    <span v-show="errors.has('availability')" class="help text-danger">{{ errors.first('availability') }}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label" for="food">{{ trans('em.food') }}</label>
                                    <textarea class="form-control" rows="3" name="food" :value="food" style="display:none;"></textarea>
                                    <ckeditor  v-model="food"></ckeditor>
                                    <span v-show="errors.has('food')" class="help text-danger">{{ errors.first('food') }}</span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="exampleFormControlSelect1">{{ trans('em.show_quoteform') }}</label>
                                    <select class="form-control" name="show_quoteform" v-model="show_quoteform" >
                                        <option value="0" >{{ trans('em.no') }} </option>
                                        <option value="1" >{{ trans('em.yes') }}</option>
                                    </select>
                                </div>

                                <div v-if="show_quoteform > 0">
                                    <div class="mb-3">
                                        <label class="form-label" for="contact_email">{{ trans('em.contact_email') }}<sup>*</sup></label>
                                        <input type="text" class="form-control lgxname" v-model="contact_email" name="contact_email" >
                                        <span v-show="errors.has('contact_email')" class="help text-danger">{{ errors.first('contact_email') }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="address">{{ trans('em.address') }}<sup>*</sup></label>
                                    <input type="text" class="form-control lgxname" v-model="address" name="address" >
                                    <span v-show="errors.has('address')" class="help text-danger">{{ errors.first('address') }}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label" for="city">{{ trans('em.city') }}<sup>*</sup></label>
                                    <input type="text" class="form-control lgxname" v-model="city" name="city" >
                                    <span v-show="errors.has('city')" class="help text-danger">{{ errors.first('city') }}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label" for="state">{{ trans('em.state') }}<sup>*</sup></label>
                                    <input type="text" class="form-control lgxname" v-model="state" name="state" >
                                    <span v-show="errors.has('state')" class="help text-danger">{{ errors.first('state') }}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label" for="zipcode">{{ trans('em.zipcode') }}<sup>*</sup></label>
                                    <input type="text" class="form-control lgxname" v-model="zipcode" name="zipcode" >
                                    <span v-show="errors.has('zipcode')" class="help text-danger">{{ errors.first('zipcode') }}</span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="country_id">{{ trans('em.select_country') }}</label>
                                    <select name="country_id" class="form-control" v-model="country_id">
                                        <option value="0">-- {{ trans('em.country') }} --</option>
                                        <option  v-for="(country, index) in countries" :key="index" :value="country.id">{{country.country_name}}</option>
                                    </select>
                                    <span v-show="errors.has('country_id')" class="help text-danger">{{ errors.first('country_id') }}</span>
                                </div>    
                                
                            </div>
                                


                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-sd-card"></i> {{ trans('em.save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</template>

<script>

import { mapMutations} from 'vuex';
import mixinsFilters from '../../mixins.js';

// CUSTOM
import MapAutocomplete from './MapAutocomplete.vue';
// CUSTOM

export default {

    props: ["edit_venue", "organiser_id"],

    mixins:[
        mixinsFilters
    ],

    components: {
       
        MapAutocomplete
        
    },

    data() {
        return {
            openModal: false,
                
            // important!!! declare all form fields
            title                   : null,
            description             : null,
            venue_type              : null,
            amenities               : null,
            seated_guestnumber      : null,
            standing_guestnumber    : null,
            neighborhoods           : null,
            pricing                 : null,
            availability            : null,
            food                    : null,
            is_page                 : 0,
            address                 : null,
            state                   : null,
            zipcode                 : null,
            city                    : null,
            glat                    : null,
            glong                   : null,
            show_quoteform          : 0,

            images              : [],
            multiple_images     : [],

            slug                : '',
            country_id  : 0,
            countries   : [],
            
    
        }
    },

    methods: {
        // update global variables
        ...mapMutations(['add', 'update']),

        // reset form and close modal
        close: function () {
            this.$refs.form.reset();
            this.$parent.edit_index = null;
            this.openModal = false;
            
            
        },

        editVenue(){
            console.log(this.edit_venue);

            
            this.title                  = this.edit_venue.title;
            this.description            = this.edit_venue.description;
            this.venue_type             = this.edit_venue.venue_type;
            this.amenities              = this.edit_venue.amenities;
            this.show_quoteform              = this.edit_venue.show_quoteform;
            this.contact_email              = this.edit_venue.contact_email;
            this.seated_guestnumber     = this.edit_venue.seated_guestnumber;
            this.standing_guestnumber   = this.edit_venue.standing_guestnumber;
            this.neighborhoods          = this.edit_venue.neighborhoods;
            this.pricing                = this.edit_venue.pricing;
            this.availability           = this.edit_venue.availability;
            this.food                   = this.edit_venue.food;
            this.address                = this.edit_venue.address;
            this.state                  = this.edit_venue.state;
            this.zipcode                = this.edit_venue.zipcode;
            this.glat                   = this.edit_venue.glat;
            this.glong                  = this.edit_venue.glong;
            this.city                   = this.edit_venue.city;
            this.multiple_images        = this.edit_venue.images ? JSON.parse(this.edit_venue.images) : [];
            this.slug                   = this.edit_venue.slug;
            this.country_id             = this.edit_venue.country_id ? this.edit_venue.country_id : 0;
        },

        // validate data on form submit
        validateForm(event) {
            this.$validator.validateAll().then((result) => {
                if (result) {
                    this.formSubmit(event);            
                }
            });
        },

        // show server validation errors
        serverValidate(serrors) {
            this.$validator.validateAll().then((result) => {
                this.$validator.errors.add(serrors);
            });
        },

        // submit form
        formSubmit(event) {

            // prepare form data for post request
            let post_url = route('eventmie.myvenues.store');
            let post_data = new FormData(this.$refs.form);
            
            // axios post request
            axios.post(post_url, post_data)
            .then(res => {
                
                this.close();
                this.showNotification('success',  trans('em.venue_saved_successfully'));
                // reload page   
                setTimeout(function() {
                    location.reload(true);
                }, 1000);
            
            })
            .catch(error => {
                let serrors = Vue.helpers.axiosErrors(error);
                if (serrors.length) {
                    this.serverValidate(serrors);
                }
            });
        },

        updateItem() {
            this.$emit('changeItem');
        },

        deleteImages(image = null){
               this.showConfirm(trans('em.delete')).then((res) => {
                if(res) {
                    // prepare form data for post request
                    console.log('hello')
                    let post_url = route('eventmie.delete_venueimage', [this.edit_venue.id]);
                    let post_data = {
                        'image'    : image,
                    };
                    
                    // axios post request
                    axios.post(post_url, post_data)
                    .then(res => {
                        // on success
                        // use vuex to update global sponsors array
                        if(res.data.status)
                        {
                            this.images           = res.data.venue;
                            
                            this.multiple_images  = this.images.images ? JSON.parse(this.images.images) : [];

                            this.showNotification('success', trans('em.venue_image_deleted'));
                        }    
                    })
                    .catch(error => {
                        let serrors = Vue.helpers.axiosErrors(error);
                        if (serrors.length) {
                            this.serverValidate(serrors);
                        }
                    });
                }
            })

        },

        // slug route
        slugUrl(){
            if(this.slug != null)
                return route('eventmie.venues.show',[this.slug]);

            return '';
        },

        get_countries(){
            axios.get(route('eventmie.myevents_countries'))
            .then(res => {
                if(res.data.countries)
                {
                   this.countries = res.data.countries
                }
            })
            .catch(error => {
                let serrors = Vue.helpers.axiosErrors(error);
                if (serrors.length) {
                    this.serverValidate(serrors);
                }
            });
        },

    },

    mounted(){
        this.get_countries();

       if(typeof this.edit_venue !== 'undefined') {
            this.editVenue();
            this.openModal = true;
        }
    },

    watch: {
        // active when organizer search 
        title: function () {
            if(this.edit_venue == undefined)
                this.slug =  (this.title != null) ? this.sanitizeTitle(this.title) : this.sanitizeTitle(this.slug);
        },

    }
}
</script>

<style >
.pac-container {
    background-color: #fff;
    position: absolute!important;
    z-index: 20000 !important;
    border-radius: 2px;
    border-top: 1px solid #d9d9d9;
    font-family: Arial,sans-serif;
    box-shadow: 0 2px 6pxrgba(0,0,0,.3);
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    overflow: hidden;
}
</style>