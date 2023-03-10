require('./bootstrap');

import router from './router.js';
import store from './store/index.js';
import moment from 'moment';

window.Vue = require('vue');
window.moment = moment;
Vue.prototype.moment = moment;


import Vuetify from 'vuetify';
import Breadcrumbs from 'vue-2-breadcrumbs';
import VueApexCharts from 'vue-apexcharts';
import Vue from 'vue';

Vue.use(Vuetify);
Vue.use(Breadcrumbs);
Vue.component('main-body', require('./views/MainBody.vue').default);
Vue.component('vuetify-autocomplete', require('./components/VuetifyAutocomplete.vue').default);
Vue.component('apexcharts', VueApexCharts);

Vue.filter('uppercase', str => str.toUpperCase());

Vue.filter("peso", function (value) {
    return 'P' + parseFloat(value || 0).toLocaleString('en-US', {maximumFractionDigits:2});
});

Vue.filter("simpleDate", function(value) {
    let _date = moment(value);
    return _date.isValid() ? _date.format('MMM D, YYYY') : value;
})

Vue.filter("simpleDateTime", function(value) {
    let _date = moment(value);
    return _date.isValid() ? _date.format('MMM D, YY h:m a') : value;
})


const app = new Vue({
    el: '#app',
    router,
    store
});
