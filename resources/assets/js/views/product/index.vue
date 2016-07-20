<template>

  <article>
    <div class="row lk-list-product-header hide-for-small-only">
      <div class="medium-3 columns" v-for="(name, showTitle) in show" v-if="showTitle">
        <span class="lk-list-product-title">{{ name }}</span>
      </div>
    </div>
    <a href="#" class="lk-list-product-link" v-for="product in products">
      <div class="row">
        <div class="medium-3 columns" v-for="(name, showTitle) in show" v-if="showTitle">
          <span class="lk-list-product-title show-for-small-only">{{ name }}</span>
          <span>{{ product.name }}</span>
        </div>
      </div>
    </a>
  </article>

</template>

<script>
  import Vue from 'vue';

  export default {

    data() {
      return {
        products: [],
        show: {
          'slug': true,
          'sku': true,
          'name': true,
          'quantity': true,
          'unitPrice': false,
          'isInventoryRequired': false,
          'isPriceVisible': false,
          'isActive': false,
          'isVisible': false,
          'isTaxable': false,
          'isShippable': false,
          'areAttachmentsEnabled': false,
        }
      };
    },

    methods: {

      fetch() {
        this.$resource('/api/products').get().then(function (res) {
          this.products = res.data;
          this.$set('products', this.products);
        });
      },

    },

    route: {
      data() {
        this.fetch();
      }
    }
  }
</script>
