<template>
    <div class="row">
                    
        <div class="col-md-6 col-lg-4 col-12 mb-4 px-0" 
            v-match-heights="{
                el: ['h5.sub-title'],  // Array of selectors to fix
            }"
            v-for="(venue, index) in venues" 
            :key="index"
        >
            
            <div class="px-2 py-2 w-100">
                <div>
                    <div class="position-relative ">
                        <a  :href="venueSlug(venue.slug)" class="text-inherit">
                            <div class="back-image rounded-3 img-hover" :style="{ 'background-image': 'url(/storage/' + JSON.parse(venue.images)[0] + ')' }"></div>
                        </a>
                    </div>

                    <div class="mt-3">
                        <div>
                            <h4 class="mb-0">
                                <a  :href="venueSlug(venue.slug)" class="text-inherit">
                                    {{ venue.title }}
                                </a>
                            </h4>
                            <div class="text-sm">
                                <span class="fs-6 me-1 font-weight-semi-bold"> {{ venue.city }}</span> 
                                <span class="fs-6 me-1 font-weight-semi-bold">{{ venue.state}}</span> 
                                <span class="me-2 font-weight-semi-bold" v-if="venue.country != null">{{ venue.country.country_name }}</span>
                            </div>
                        </div>

                    </div>

                </div>   

            </div>

        </div>
        

       

        <div class="col-12" v-if="not_found">
            <h4 class="heading text-center mt-30"><i class="fas fa-exclamation-triangle"></i> {{ trans('em.venues_not_found') }}</h4>
        </div>
        
    </div>
</template>

<script>

import mixinsFilters from '../../mixins.js';

export default {
    
    props: ['venues', 'currency', 'date_format'],

    mixins:[
        mixinsFilters
    ],

    data() {
        return {
            not_found: false,
        }
    },

    methods:{
        
        
        // return route with venue slug
        venueSlug: function venueSlug(slug) {
            return route('eventmie.venues.show',[slug]);
        }

  
    },

    watch: {
        venues: function () {
            this.not_found = false;
            if(this.venues.length <= 0)
                this.not_found = true;
        },
    },

    

}
</script>