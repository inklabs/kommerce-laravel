import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

var router = new VueRouter({
  history: false
});

import DashboardPage from './pages/dashboard';
import ProductPage from './pages/product';

router.map({
  '/home': {
    component: Vue.component('home', DashboardPage),
  },
  '/product': {
    component: Vue.component('product', ProductPage),
  }
});

router.alias({
  '': '/home'
});

export default router