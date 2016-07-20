<template>
  <div>
    <div class="lk-sidebar-branding">
      <img class="lk-sidebar-logo" src="//placehold.it/150x150" alt="">
    </div>

    <ul class="lk-menu vertical menu" data-accordion-menu>
      <li>
        <a v-link="'product'">Product</a>
        <ul class="vertical menu">
          <li><a v-link="'product'">List All Products</a></li>
          <li><a v-link="'dashboard'">Create Dummy Product</a></li>
        </ul>
      </li>
      <li><a v-link="'dashboard'">Dashboard</a></li>
    </ul>
  </div>
</template>
