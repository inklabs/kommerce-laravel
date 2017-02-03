import Vue from 'vue';
import VueResource from 'vue-resource';

import MainLayout from './views/layout/default';
import router from './routes';

Vue.use(VueResource);
Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name=csrf-token]').getAttribute('content');

router.start(MainLayout, '#lk-app');

import '../sass/app.scss';