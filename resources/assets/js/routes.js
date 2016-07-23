import Vue from 'vue';
import VueRouter from 'vue-router';

import DashboardPage from './views/dashboard/index';
import Product from './views/product/Product';
import ProductList from './views/product/list';
import ProductShow from './views/product/show';
import ProductEdit from './views/product/edit';

Vue.use(VueRouter);

var router = new VueRouter({
  history: false
});

router.map({
  '/dashboard': {
    name: 'dashboard',
    component: DashboardPage,
  },
  '/product': {
    name: 'product',
    component: Product,
    subRoutes: {
      '/': {
        name: 'product.list',
        component: ProductList,
      },
      '/:id': {
        name: 'product.show',
        component: ProductShow,
      },
      '/:id/edit': {
        name: 'product.edit',
        component: ProductEdit,
      }
    }
  },
});

router.alias({
  '': '/dashboard'
});

export default router