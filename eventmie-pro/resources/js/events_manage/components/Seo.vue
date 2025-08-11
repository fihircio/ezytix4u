<template>
    <div>
   
        <form ref="form" @submit.prevent="validateForm" method="POST" enctype="multipart/form-data" class="lgx-contactform">
            <!-- AI Generation Button -->
            <div class="mb-3">
                <button type="button" class="btn btn-success" @click="openAi = true">
                    <i class="fas fa-magic"></i> Generate SEO with AI
                </button>
            </div>
            
            <input type="hidden" name="event_id" v-model="event_id">
            <input type="hidden" name="organiser_id" v-model="organiser_id">
            
            <div class="mb-2">
                <label class="form-label">{{ trans('em.meta_title') }}</label>
                <input type="text" class="form-control"  name="meta_title" v-model="local_meta_title" @change="isDirty()">
                <span v-show="errors.has('meta_title')" class="help text-danger">{{ errors.first('meta_title') }}</span>
            </div>

            <div class="mb-2">
                <label class="form-label">{{ trans('em.meta_tags') }}</label>
                <input type="text" class="form-control" name="meta_keywords" v-model="local_meta_keywords" @change="isDirty()">
                <span v-show="errors.has('meta_keywords')" class="help text-danger">{{ errors.first('meta_keywords') }}</span>
            </div>

            <div class="mb-2">
                <label class="form-label">{{ trans('em.meta_description') }}</label>
                <input type="text" class="form-control" name="meta_description" v-model="local_meta_description" @change="isDirty()">
                <span v-show="errors.has('meta_description')" class="help text-danger">{{ errors.first('meta_description') }}</span>
            </div>

            <button type="submit" class="btn btn-primary btn-lg mt-2"><i class="fas fa-sd-card"></i> {{ trans('em.save') }}</button>
        </form>
        
        <!-- AI Modal for SEO Generation -->
        <ai-modal v-if="openAi" :seo-only="true" @close="openAi = false" @apply="applyAiSeo"></ai-modal>
                    
    </div>
</template>

<script>
import { mapState, mapMutations} from 'vuex';
import mixinsFilters from '../../mixins.js';
import AiModal from './AiModal.vue';

export default {
    props: [
        'event_prop',
    ],

    components: {
        AiModal
    },

    mixins:[
        mixinsFilters
    ],

    data() {
        return {
            local_meta_title       : '',
            local_meta_description : '',
            local_meta_keywords   : '',
            openAi                 : false, // New state variable for AI modal
        }
    },

    computed: {
        // get global variables
        ...mapState( ['event_id', 'organiser_id', 'event', 'meta_title', 'meta_description', 'meta_keywords']),
    },

    watch: {
        // Watch for changes from AI modal
        meta_title: function(newVal) {
            console.log('SEO: meta_title changed to:', newVal);
            if (newVal && newVal !== this.local_meta_title) {
                this.local_meta_title = newVal;
            }
        },
        meta_description: function(newVal) {
            console.log('SEO: meta_description changed to:', newVal);
            if (newVal && newVal !== this.local_meta_description) {
                this.local_meta_description = newVal;
            }
        },
        meta_keywords: function(newVal) {
            console.log('SEO: meta_keywords changed to:', newVal);
            if (newVal && newVal !== this.local_meta_keywords) {
                this.local_meta_keywords = newVal;
            }
        }
    },

    methods: {
        // update global variables
        ...mapMutations(['add', 'update']),

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
            // Sync local data with Vuex store before saving
            this.syncWithStore();
            
            // prepare form data for post request
            let post_url = route('eventmie.myevents_store_seo');
            let post_data = new FormData(this.$refs.form);

            // axios post request
            axios.post(post_url, post_data)
            .then(res => {
                // on success
                // use vuex to update global sponsors array
                if(res.data.status)
                {
                    this.showNotification('success', trans('em.seo_saved_successfully'));
                    // reload page
                    setTimeout(function() {
                        location.reload(true);
                    }, 1000);
                }

            })
            .catch(error => {
                // only in case or serverValidate
                if (error.length) {
                    this.serverValidate(error);
                }
            });

        },

        //edit seo
        edit_seo(){
            if(Object.keys(this.event).length > 0)
            {
                this.local_meta_title         =  this.event.meta_title || '';
                this.local_meta_keywords      =  this.event.meta_keywords || '';
                this.local_meta_description   =  this.event.meta_description || '';
                
                // Also update Vuex store with existing data
                this.update({
                    meta_title: this.local_meta_title,
                    meta_description: this.local_meta_description,
                    meta_keywords: this.local_meta_keywords
                });
            }
        },

        isDirty() {
            this.add({is_dirty: true});
        },
        isDirtyReset() {
            this.add({is_dirty: false});
        },

        // Sync local data with Vuex store
        syncWithStore() {
            // Sync from Vuex store to local data before saving
            if (this.$store.state.meta_title && this.$store.state.meta_title !== this.local_meta_title) {
                this.local_meta_title = this.$store.state.meta_title;
            }
            if (this.$store.state.meta_description && this.$store.state.meta_description !== this.local_meta_description) {
                this.local_meta_description = this.$store.state.meta_description;
            }
            if (this.$store.state.meta_keywords && this.$store.state.meta_keywords !== this.local_meta_keywords) {
                this.local_meta_keywords = this.$store.state.meta_keywords;
            }
        },

        // New method to apply AI generated SEO
        applyAiSeo(generatedSeo) {
            console.log('SEO: Applying AI generated SEO:', generatedSeo);
            
            // Update local data
            this.local_meta_title = generatedSeo.meta_title;
            this.local_meta_keywords = generatedSeo.meta_keywords;
            this.local_meta_description = generatedSeo.meta_description;
            
            // Update Vuex store
            this.update({
                meta_title: generatedSeo.meta_title,
                meta_description: generatedSeo.meta_description,
                meta_keywords: generatedSeo.meta_keywords
            });
            
            this.openAi = false; // Close modal after applying
            this.isDirty(); // Mark as dirty
        }
    },
    
    mounted(){
        this.isDirtyReset();
        
        // Sync with Vuex store immediately
        this.syncWithStore();
        
        // if user have no event_id then redirect to details page
        let event_step     = this.eventStep();
        
        if(event_step)
        {
            var $this = this;
            this.getMyEvent().then(function (response){
                $this.edit_seo();
                // Sync again after loading event data
                $this.syncWithStore();
            });
        }
    }


}
</script>