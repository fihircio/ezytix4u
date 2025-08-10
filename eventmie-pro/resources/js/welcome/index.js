
/**
 * This is a page specific seperate vue instance initializer
 */

// include vue common libraries, plugins and components
require('../vue_common');

/**
 * Local Imports
*/
Vue.component('VueMatchHeights', require('vue-match-heights').default);

/**
 * Local Components 
 */
Vue.component('event-listing', require('../common_components/EventListing').default);
Vue.component('banner-slider', require('./components/BannerSlider').default);


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
    mounted() {
        this.initHowItWorksTabs();
    },
    methods: {
        initHowItWorksTabs() {
            console.log('Initializing How It Works tabs...');
            
            // Enable tab click functionality
            $('#howItWorksTabs a').click(function (e) {
                e.preventDefault();
                console.log('Tab clicked:', $(this).text());
                $(this).tab('show');
            });

            // Auto-switch tabs every 5 seconds
            let tabs = $('#howItWorksTabs a');
            console.log('Found tabs:', tabs.length);
            let index = 0;
            setInterval(() => {
                index = (index + 1) % tabs.length;
                console.log('Auto-switching to tab:', index);
                tabs.eq(index).tab('show');
            }, 5000);
        }
    }
});