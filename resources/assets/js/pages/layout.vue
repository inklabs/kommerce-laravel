<template>
  <div>
    <a v-link="'product'" class="button">Product</a>
    <a v-link="'home'" class="button">Dashboard</a>
    <router-view></router-view>
  </div>
</template>

<script>
  export default {

  };
</script>