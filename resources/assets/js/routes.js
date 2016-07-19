import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

var router = new VueRouter({
  history: true
});

import DashboardPage from './pages/dashboard';
import ProductPage from './pages/product';

router.map({
  '/': {
    name: 'dashboard',
    component: DashboardPage
  },
  '/product': {
    name: 'product',
    component: ProductPage
  }
});

export default router