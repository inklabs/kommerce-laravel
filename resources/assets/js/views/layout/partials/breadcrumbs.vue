<template>
  <nav aria-label="You are here:" role="navigation">
    <ul class="breadcrumbs">
      <li v-for="item in list"><span class="active" v-if="isLast($index)">{{item.label}}</span>
        <a v-link="{ name: item.link }" v-else>{{ item.label }}</a>
      </li>
    </ul>
  </nav>
</template>

<script>
  export default {
    props: {
      list: {
        type: Array,
        required: true,
        default: () => []
      },
      separator: {
        type: String
      }
    },
    ready () {
      if (this.separator) {
        this.$el.style.setProperty('--separator', `"${this.separator}"`)
      }
    },
    methods: {
      isLast (index) {
        return index === this.list.length - 1
      }
    }
  }
</script>
