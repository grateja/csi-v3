require('./bootstrap');

import router from './router.js';
import store from './store/index.js';

window.Vue = require('vue');

import Vuetify from 'vuetify';
import Breadcrumbs from 'vue-2-breadcrumbs';
import VueApexCharts from 'vue-apexcharts';

Vue.use(Vuetify);
Vue.use(Breadcrumbs);
Vue.component('main-body', require('./views/MainBody.vue').default);
Vue.component('vuetify-autocomplete', require('./components/VuetifyAutocomplete.vue').default);
Vue.component('apexcharts', VueApexCharts);

Vue.filter('uppercase', str => str.toUpperCase());

const app = new Vue({
    el: '#app',
    router,
    store
});
