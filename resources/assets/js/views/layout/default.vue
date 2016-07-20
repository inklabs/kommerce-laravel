<template>

  <div class="lk-wrapper">
    <nav class="lk-sidebar">
      <side-bar></side-bar>
    </nav>
    <section class="lk-content">
      <router-view keep-alive></router-view>
    </section>
  </div>

</template>

<script>
  import SideBar from './partials/sidebar.vue';

  export default{

    components: {
      SideBar: SideBar,
    }
  }
</script>