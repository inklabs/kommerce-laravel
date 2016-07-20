import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

var router = new VueRouter({
  history: false
});

import DashboardPage from './views/dashboard/index';
import ProductPage from './views/product/index';

router.map({
  '/dashboard': {
    component: Vue.component('dashboard', DashboardPage),
  },
  '/product': {
    component: Vue.component('product', ProductPage),
  }
});

router.alias({
  '': '/dashboard'
});

export default router