<template>

  <article>
    EDIT THIS PRODUCT

    <h1>
      <a v-link="{ name: 'product' }">&laquo;</a>
      {{ product.name }}
    </h1>

    <ul>
      <input type="text" v-model="product.name" placeholder="Name">
      <button class="button" @click="submit">Save</button>
    </ul>

  </article>

</template>

<script>

  export default {

    computed: {
    },

    ready() {
      this.resource = this.$resource('/api/products{/id}{/edit}');
      this.id = this.$route.params.id;
      this.fetch();
    },

    data() {
      return {
        resource: null,
        product: {
          id: this.$route.params.id
        }
      }
    },

    methods: {

      fetch() {
        this.resource.get({id: this.product.id}).then((response) => {
          console.log(response.data[0]);
          this.product = response.data[0];
        });
      },

      submit() {
        var data = {
          id: this.product.id,
          name: this.product.name,
        };


        this.resource.update({id: this.product.id}, data).then((response) => {
          console.log('success', response);
        }, (response) => {
          console.log('fail', response);
        });
      }


    }
  }
</script>
