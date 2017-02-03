<template>
  <nav class="lk-sidebar">
    <div class="lk-sidebar-branding">
      <a v-link="{ name: 'dashboard' }">
        <img class="lk-sidebar-logo" src="//placehold.it/150x150" alt="">
      </a>
    </div>

    <ul class="lk-menu vertical menu" data-accordion-menu>
      <li><a v-link="{ name: 'product' }">Product</a></li>
      <li><a v-link="{ name: 'dashboard' }">Dashboard</a></li>
    </ul>
  </nav>
</template>
