
/**
 * This is a page specific seperate vue instance initializer
 */

// include vue common libraries, plugins and components
require('../vue_common');


/**
 * Local Components 
 */
Vue.component('o-line-chart', require('./components/OLineChart.vue').default);






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
    
    
    
    data() {
        return {
          
        }
    },

  


   
});





