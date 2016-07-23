<template>

  <article>
    <h1>
      <a v-link="{ name: 'product' }">&laquo;</a>
      {{ product.name }}
    </h1>

    <ul>
      <li v-for="(key, value) in product">
        <strong>{{ key }}:</strong>
        {{ value }}
      </li>
    </ul>

  </article>

</template>

<script>

  export default {

    ready() {
      this.id = this.$route.params.id;
      this.fetch();
    },

    data() {
      return {
        product: null,
        id: this.$route.params.id
      }
    },


    methods: {

      fetch() {
        this.$resource('/api/products/' + this.id).get().then(function (res) {
          this.product = res.data[0];
        });
      },

    }
  }
</script>
