<template>

  <li class="message" :class="incomingMessage">

      <article class="message-body">
        <p>{{ message.body }}</p>
        <small class="datetime">{{ message.date.date }}</small>
      </article>

      <aside class="message-info">
        <img class="avatar" :src="'/images/cache/micro/' + message.user.data.avatar" alt="{{ message.user.data.name }}'s Avatar">
      </aside>
</li>
</template>

<script>

  export default {

    props: {
      'message': {
        type: Object,
        required: true
      }
    },


    computed: {

      incomingMessage() {
        return this.message.self_message ? '' :'message-right';
      },

    },

  };

</script>