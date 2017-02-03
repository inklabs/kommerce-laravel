<template>
  <div>
    <!-- the input field -->
    <input type="text"
           placeholder="Search For Product"
           autocomplete="off"
           v-model="query"
           @keydown.down="down"
           @keydown.up="up"
           @keydown.enter="hit"
           @keydown.esc="reset"
           @input="update"/>

    <!-- the list -->
    <ul v-show="hasItems">
      <li v-for="item in items" :class="activeClass($index)">
        <a v-link="{ name: 'product.show', params: {id: item.id} }">{{ item.name }}</a>
        </li>
    </ul>
  </div>
</template>

<script>

  import VueTypeahead from 'vue-typeahead';

  export default{

    extends: VueTypeahead,

    data() {
      return {
        query: '',
        src: '/api/products',
        limit: 5,
        minChars: 3,
      }
    },

    methods: {

      goToPage(item) {
        this.$router.go({ name: 'product.show', params: {id: item.id} });
      },

      onHit (item) {
        alert(item)
      }

    }
  }
</script>
