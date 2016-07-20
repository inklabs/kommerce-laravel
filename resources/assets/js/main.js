import Vue from 'vue';
import VueResource from 'vue-resource';

import App from './views/layout/default';
import router from './routes';

Vue.use(VueResource);

router.start(App, '#lk-app');