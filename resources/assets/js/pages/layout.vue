<template>
  <article>

      <lk-topbar></lk-topbar>

    <div class="lk-wrapper">
      <lk-sidebar></lk-sidebar>

      <section class="lk-content">
        <router-view></router-view>
      </section>
    </div>

  </article>
</template>

<script>
  import LkTopbar from '../components/layout/topbar';
  import LkSidebar from '../components/layout/sidebar';
  export default {
    components: {
      LkTopbar: LkTopbar,
      LkSidebar: LkSidebar
    }
  }
</script>
