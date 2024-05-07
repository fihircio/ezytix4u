<!-- parent component-->
<template>
    <div>
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between flex-wrap p-4 bg-white border-bottom-0">
                <div>
                    <h1 class="mb-0 fw-bold h2">{{ trans('em.myvenues') }}</h1>
                </div>
                <div>
                    <Venue-component 
                        :organiser_id="organiser_id"
                    >
                    </Venue-component>
                </div>
            </div>
            <div class="mx-4">
                <div class="alert alert-info"><i class="fas fa-circle-info"></i> {{ trans('em.venue_info') }}</div>
            </div>
            <div class="table-responsive">
                <table class="table text-wrap">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.title') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.state') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.city') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in venues" v-bind:item="item" v-bind:index="index" v-bind:key="item.id" >
                            <td :data-title="trans('em.venue')">
                                <div class="d-flex align-items-center">
                                    <a :href="venueSlug(item.slug)">    
                                        <img :src="'/storage/'+JSON.parse(item.images)[0]" :alt="item.title" class="rounded img-4by3-md ">
                                    </a>
                                    <div class="ms-3 lh-1">
                                        <h5 class="mb-1"> 
                                            <a class="text-inherit text-wrap" :href="venueSlug(item.slug)">{{ item.title }}</a>
                                        </h5>
                                        <small class="text-muted strong">{{ item.venue_type }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle" :data-title="trans('em.state')">{{ item.state }}</td>
                            <td class="align-middle" :data-title="trans('em.city')">{{ item.city }}</td>
                            <td class="align-middle text-nowrap" :data-title="trans('em.actions')"> 
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary btn-sm" @click="edit_index = index"><i class="fas fa-edit"></i> {{ trans('em.edit') }}</button>
                                    <button type="button" class="btn btn-danger btn-sm" @click="deleteVenue(item.id)"><i class="fas fa-trash"></i> {{ trans('em.delete') }}</button>
                                </div>
                                <Venue-component  v-if="edit_index == index" :edit_venue="item" :organiser_id="organiser_id" ></Venue-component>
                            </td>
                        </tr>
                        <tr v-if="venues.length <= 0">
                            <td class="text-center align-middle">{{ trans('em.no_venues') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-4 pb-4" v-if="venues.length > 0">
                <pagination-component v-if="pagination.last_page > 1" :pagination="pagination" :offset="pagination.total"  @paginate="getVenues()"></pagination-component>
            </div>
        </div>

    </div>
</template>

<script>

import VenueComponent from './Venue.vue';
import PaginationComponent from '../../common_components/Pagination';
import mixinsFilters from '../../mixins.js';
import MapAutocomplete from './MapAutocomplete.vue';

export default {
    props: [
        'organiser_id', 'page',
    ],

    components: {
        VenueComponent,
        PaginationComponent,
        MapAutocomplete
    },

    mixins:[
        mixinsFilters
    ],

    data() {
        return {
            venues       : [],
            edit_index : null,
            pagination : {
                'current_page': 1
            },
        }
    },
    computed: {
         
        current_page() {
            // get page from route
            if(typeof this.page === "undefined")
                return 1;
            return this.page;
        },
    },
    methods: {
        getVenues() {
            axios.get(route('eventmie.myvenues.index')+'?page='+this.current_page+'&organiser_id='+this.organiser_id)
            .then(res => {
                console.log('hello world', res.data.venues.data);
                // fill data venues array
                this.venues   = res.data.venues.data;
                this.pagination = {
                    'total' : res.data.venues.total,
                    'per_page' : res.data.venues.per_page,
                    'current_page' : res.data.venues.current_page,
                    'last_page' : res.data.venues.last_page,
                    'from' : res.data.venues.from,
                    'to' : res.data.venues.to
                };
            })
            .catch(error => {
                Vue.helpers.axiosErrors(error);
            });
        },
        deleteVenue(venue_id){

            this.showConfirm(trans('em.delete_venue_ask')).then((res) => {
                if(res) {
         
                    axios.post(route('eventmie.myvenues.destroy',[venue_id]), {
                        
                        // headers: {
                        //     _method : 'DELETE'
                        // },

                        organiser_id : this.organiser_id,
                        _method      : 'DELETE'
                    })
                    .then(res => {
                    
                        if(res.data.status)
                        {
                            this.getVenues();
                            this.showNotification('success', trans('em.delete_venue_succcess'));
                        }
                    })
                    .catch(error => {
                        Vue.helpers.axiosErrors(error);
                    });
                }
            })
        },
        // return route with venue slug
        venueSlug(slug){
            return route('eventmie.venues.show',[slug]);
        },
    },


    mounted() {
        this.getVenues();
    }
}
</script>

