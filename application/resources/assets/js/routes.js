import Vue from 'vue';
import VueRouter from 'vue-router';

import DashboardPage from './views/dashboard/index.vue';
import Product from './views/product/Product.vue';
import ProductList from './views/product/list.vue';
import ProductShow from './views/product/show.vue';
import ProductEdit from './views/product/edit.vue';

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