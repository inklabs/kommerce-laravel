<template>
  <!--<breadcrumbs :list="list"></breadcrumbs>-->
  <router-view></router-view>
</template>

<script>
  import Breadcrumbs from '../layout/partials/breadcrumbs.vue';

  export default{

    data() {
      return {
        list: [{
          'link':'dashboard',
          'label':'Dashboard'
        },{
          'link':'product',
          'label': 'Products'
        },{
          'link':'product',
          'label': 'Products234'
        }]
      }
    },

    components: {Breadcrumbs}
  }
</script>
