import Vue from 'vue';
import VueResource from 'vue-resource';
import VueRouter from 'vue-router';

Vue.use(VueResource);
Vue.use(VueRouter);
// Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name=csrf-token]').getAttribute('content');

import ProductForm from './components/Product/ProductForm.vue';
import ProductList from './components/Product/ProductList.vue';

var App = Vue.extend({});

// new Vue({
//   el: 'app',
//   components: {
//     ProductForm: ProductForm,
//     ProductList: ProductList,
//   }
// });


var router = new VueRouter();

// Define some routes.
// Each route should map to a component. The "component" can
// either be an actual component constructor created via
// Vue.extend(), or just a component options object.
// We'll talk about nested routes later.
import ProductShow from './pages/product/show.vue';

router.map({
  '/products': {
    component: './pages/product/index.vue',
    subRoutes: {
      '/show': {
        component: {
          ProductShow: ProductShow
        },
        guest: true
      }
    }
  }
});

// Now we can start the app!
// The router will create an instance of App and mount to
// the element matching the selector #app.
router.start(App, '#lk-app');