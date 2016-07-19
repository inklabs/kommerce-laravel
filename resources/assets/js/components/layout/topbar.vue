<template>
  <header class="lk-topbar">
    <div class="top-bar">
      <div class="top-bar-title">
        <span data-responsive-toggle="responsive-menu" data-hide-for="medium">
          <button class="menu-icon dark" type="button" data-toggle></button>
        </span>
        <strong>Larakommerce</strong>
      </div>
      <div id="responsive-menu">
        <div class="top-bar-left">
          <ul class="menu">
            <li><a v-link="{ path: '/foo' }">Go to Foo</a></li>
            <li><a v-link="{ path: '/bar' }">Go to Bar</a></li>
          </ul>
        </div>
        <div class="top-bar-right">
          <ul class="menu">
            <li><input type="search" placeholder="Search"></li>
            <li>
              <button type="button" class="button">Search</button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>
</template>
