<template>

  <article>
    <h1>
      <a v-link="{ name: 'product' }">&laquo;</a>
      {{ product.name }}
      <a v-if="product.id" v-link="{ name: 'product.edit',  params: { id: product.id }}">Edit</a>
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
      this.fetch();
    },

    data() {
      return {
        product: {
          id: this.$route.params.id
        },
      }
    },


    methods: {

      fetch() {
        this.$resource('/api/products/' + this.product.id).get().then(function (res) {
          this.product = res.data[0];
        });
      },

    }
  }
</script>
