
/**
 * This is a page specific seperate vue instance initializer
 */

// include vue common libraries, plugins and components
require('../vue_common.js');

/**
 * Local Third-party Lib Imports
*/
/* Instances */
import Vuex from 'vuex';
window.Vuex = Vuex;
Vue.use(Vuex);


import PersonalDetails from './components/PersonalDetails';

import Security from './components/Security';
import BankDetails from './components/BankDetails';
import OrganiserInfo from './components/OrganiserInfo';

import BecomeOrganiser from './components/BecomeOrganiser';
import { mapState, mapMutations } from "vuex";

import Croppa from 'vue-croppa';
Vue.use(Croppa)

/**
 * Local Vuex Store 
 */

const store = new Vuex.Store({

    state: {
        personal_details    : [],
        update_bank_details : [],
        organiser_info      : [],
    },

    mutations: {
        add(state, { personal_details, update_bank_details, organiser_info }) {

            if (typeof personal_details !== "undefined") {
                state.personal_details = personal_details;
            }

            if (typeof update_bank_details !== "undefined") {
                state.update_bank_details = update_bank_details;
            }

            if (typeof organiser_info !== "undefined") {
                state.organiser_info = organiser_info;
            }
        }
    }

});



const routes = new VueRouter({
    linkExactActiveClass: 'active',
    routes: [
        {
            path: '/',
            name: 'personal-details',
            props: {
                user: user,
                csrf_token: csrf_token,
            },
            component: PersonalDetails,
        },
        {
            path: '/userSecurity',
            name: 'security',
            props: {
                user: user,
                csrf_token: csrf_token,
            },
            component: Security,
        },
        {
            path: '/userBankDetails',
            name: 'bank-details',
            props: {
                user: user,
                csrf_token: csrf_token,
            },
            component: BankDetails,
        },
        {
            path: '/userOrganiserInfo',
            name: 'organiser-info',
            props: {
                user: user,
                csrf_token: csrf_token,
            },
            component: OrganiserInfo,
        },
        {
            path: '/becomeOrganiser',
            props: {
                user: user,
                multi_vendor: multi_vendor,
                csrf_token: csrf_token,
            },
            name: 'become-organiser',
            component: BecomeOrganiser,
        },

    ],

});

/**
 * This is where we finally create a page specific
 * vue instance with required configs
 * element=app will remain common for all vue instances
 *
 * make sure to use window.app to make new Vue instance
 * so that we can access vue instance from anywhere
 * e.g interceptors 
 */
window.app = new Vue({
    el: '#eventmie_app',
    router: routes,
    store: store,
    data() {
        return {
            store: store
        }
    },
    computed: {

        currentRouteName() {
            return this.$route.name;
        },
        ...mapState(["personal_details",  "update_bank_details", "organiser_info"])

    },
    methods: {
        ...mapMutations(["add"]),

        checkEmptyProfile() {
            if (
                user.name == "" ||
                // user.username == null ||
                user.email == ""
            ) {
                return false;
            }
            return true;
        },

        checkEmptyBank() {
            return true;
        },

        checkEmptyOrganisation() {
            if (
                user.organisation == null 
            ) {
                return false;
            }
            return true;
        }



    },
    mounted() {
        this.add({
            personal_details: this.checkEmptyProfile(),
            update_bank_details: this.checkEmptyBank(),
            organiser_info: this.checkEmptyOrganisation(),
            
        });
    }


});