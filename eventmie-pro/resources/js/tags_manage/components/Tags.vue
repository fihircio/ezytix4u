<template>
    <div>
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between flex-wrap p-4 bg-white border-bottom-0">
                <div>
                    <h1 class="mb-0 fw-bold h2">{{ trans('em.mytags') }}</h1>
                </div>
                <div>
                    <Tag-component  :organiser_id="organiser_id"> </Tag-component>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table text-wrap">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.name') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.type') }}</th>
                            <th class="border-top-0 border-bottom-0">{{ trans('em.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in tags" v-bind:item="item" v-bind:index="index" v-bind:key="item.id">
                            <td :data-title="trans('em.name')">
                                <div class="d-flex align-items-center">
                                    <img :src="'/storage/'+item.image" :alt="item.title" class="rounded img-4by3-md ">
                                    
                                    <div class="ms-3 lh-1">
                                        <h5 class="mb-1">{{ item.title }}</h5>
                                        <small class="text-muted strong">{{ item.sub_title }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle" :data-title="trans('em.type')">{{ item.type }}</td>
                            <td class="align-middle text-nowrap" :data-title="trans('em.actions')"> 
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary" @click="edit_index = index"><i class="fas fa-edit"></i> {{ trans('em.edit') }}</button>
                                    <button type="button" class="btn btn-sm btn-danger" @click="deleteTag(item.id)"><i class="fas fa-trash"></i> {{ trans('em.delete') }}</button>
                                </div>
                                <Tag-component  v-if="edit_index == index" :edit_tag="item" ></Tag-component>
                            </td>
                            
                        </tr>
                        <tr v-if="tags.length <= 0">
                            <td class="text-center align-middle">{{ trans('em.no_tags') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-4 pb-4" v-if="tags.length > 0">
                <pagination-component v-if="pagination.last_page > 1" :pagination="pagination" :offset="pagination.total" @paginate="getTags()">
                </pagination-component>
            </div>
        </div>
    </div>
    
</template>

<script>

import TagComponent from './Tag.vue';
import PaginationComponent from '../../common_components/Pagination';
import mixinsFilters from '../../mixins.js';

export default {
    props: [
        'organiser_id', 'page',
    ],

    components: {
        TagComponent,
        PaginationComponent,
    },

    mixins:[
        mixinsFilters
    ],

    data() {
        return {
            tags       : [],
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
        getTags() {
            axios.post(route('eventmie.tags')+'?page='+this.current_page,{
               organiser_id : this.organiser_id, 
            })
            .then(res => {
                // fill data tags array
                this.tags   = res.data.tags.data;
                this.pagination = {
                    'total' : res.data.tags.total,
                    'per_page' : res.data.tags.per_page,
                    'current_page' : res.data.tags.current_page,
                    'last_page' : res.data.tags.last_page,
                    'from' : res.data.tags.from,
                    'to' : res.data.tags.to,
                    'links' : res.data.tags.links
                };
            })
            .catch(error => {
                Vue.helpers.axiosErrors(error);
            });
        },
        deleteTag(tag_id){

            this.showConfirm(trans('em.delete_tag_ask')).then((res) => {
                if(res) {
         
                    axios.post(route('eventmie.tags_delete'), {
                        tag_id : tag_id
                    })
                    .then(res => {
                    
                        if(res.data.status)
                        {
                            this.getTags();
                            this.showNotification('success', trans('em.delete_tag_succcess'));
                        }
                    })
                    .catch(error => {
                        Vue.helpers.axiosErrors(error);
                    });
                }
            })
        }
    },


    mounted() {
        this.getTags();
    }
}
</script>