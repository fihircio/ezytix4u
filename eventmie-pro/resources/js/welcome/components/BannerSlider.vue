<template>
    <VueSlickCarousel 
        :autoplay="true"
        :autoplaySpeed="5000"
        :arrows="false" 
        :dots="false"
        :infinite="false"
        :slidesToShow="1"
        :paginationEnabled="false"
        :rtl="dir"
    >
        <div 
            v-for="(item, index) in banners"
            v-bind:item="item"
            v-bind:index="index"
            v-bind:key="index"
            :class="'lgx-item-common'"
        >
            <section>
                <div class="container-fluid p-0">
                    <div class="cover-img-bg" :style=" { backgroundImage: 'url(' + (`/storage/${item.image}`) + ')'} ">
                        <img class="cover-img prevent_draggable" :src="'/storage/'+item.image" :alt="item.title" />
                        <div class="banner-slider-form">
                            <h1 class="text-white mb-0 fw-bold display-5">{{ item.title }}</h1>
                            <p class="fw-bold text-white">{{ item.subtitle }}</p>
                            <div class="d-flex justify-content-center mt-2">
                                <div v-if="demo_mode">
                                    <div class="px-2">
                                        <a class="btn btn-white" target="_blank" href="https://eventmie-pro-docs.classiebit.com"><i class="fas fa-book"></i> Docs</a>
                                    </div>
                                    <div class="px-2">
                                        <a class="btn btn-primary" target="_blank" href="https://eventmie-pro-docs.classiebit.com/docs/2.0/changelog/changes"><i class="fas fa-book"></i> See What's New v2.0</a>
                                    </div>    
                                </div>
                                <div v-else>
                                    <a v-if="item.button_url != null && item.button_title != null" class="btn btn-primary bg-gradient text-white" :href="item.button_url">{{ item.button_title }} &nbsp;&nbsp; <i class="fas fa-long-arrow-alt-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </VueSlickCarousel>
</template>

<script>
import VueSlickCarousel from 'vue-slick-carousel'
import 'vue-slick-carousel/dist/vue-slick-carousel.css'
// optional style for arrows & dots
import 'vue-slick-carousel/dist/vue-slick-carousel-theme.css'

Vue.prototype.base_url = window.base_url;

export default {

    components: {
        VueSlickCarousel
    },
    props: [
        'banners',
        'is_logged',
        'is_customer',
        'is_organiser',
        'is_admin',
        'is_multi_vendor',
        'demo_mode',
        'check_session',
        's_host'
        
    ],

    
    data() {
        return {
            check       : 0,
            categories  : [],
            cities      : [],
            f_category  : '',
            f_city      : '',
            f_price     : '',
            route       : route,
            dir         : false,

        }
    },    

    methods: {
        // return route with event slug
        getRoute(name){
            return route(name);
        },

        verifyD(){
            this.check = this.check_session ? 1 : 0;
            
            if(this.check == 0)
            {
                axios.post('https://cblicense.classiebit.com/verifyd',{
                    domain : window.location.hostname,
                    s_host : this.s_host
                })
                .then(res => {
                    if(typeof res.data.status !== 'undefined' && res.data.status != 0)
                        this.checkSession();
                    else
                        window.location.href = base_url+"/404";
                    
                })
                .catch(error => {
                    
                });
            }
        },
        
        // check Session
        checkSession(){
            axios.post(route('eventmie.check_session'))
            .then(res => {
              
            }).catch(error => {
                
            });
        },

        // get categories
        getCategories(){
            axios.get(route('eventmie.myevents_categories'))
            .then(res => {
                if(res.status)
                   this.categories  = res.data.categories;
            })
            .catch(error => {
                
            });
        },
        // get cities
        getCities(){
            axios.get(route('eventmie.myevents_cities'))
            .then(res => {
                if(res.status)
                   this.cities  = res.data.cities;
            })
            .catch(error => {
                
            });
        },
        
        getDirection(){
            document.documentElement.dir == 'rtl' ? this.dir = true : this.dir = false;
        },
        


    },

    mounted() {
       // this.verifyD();       
        this.getCategories();
        this.getCities();
        this.getDirection();
        console.log( document.documentElement.dir);
    }
}
</script>