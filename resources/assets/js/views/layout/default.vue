<template>

  <div class="lk-wrapper">
    <side-bar></side-bar>
    <section class="lk-content">
      <router-view></router-view>
    </section>
  </div>

</template>

<script>
  import SideBar from './partials/sidebar.vue';

  export default{

    components: { SideBar }
  }
</script>