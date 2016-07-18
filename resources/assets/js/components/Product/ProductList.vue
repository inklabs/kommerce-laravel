<template>

  <article id="js-threadbox" class="thread-messages">

    <ul class="messages">
      <li>CARD</li>
    </ul>

  </article>

</template>

<script>

  import $ from 'jquery';

  export default {



    props: {
      'threadId': {
        type: String,
        required: true
      }
    },

    data() {
      return {
        messages: {},
        messagesCount: null,
        messagesResource: null
      };
    },


    ready() {
      var vm = this,
          url = '/api/messages/' + vm.threadId + '?include=messages'
          ;

      vm.messagesResource = vm.$resource(url);

//      vm.fetch();
      vm.setUpdateCycle();

    },

    methods: {

      /**
       * Sets a polling cycle to look for new messages
       */
      setUpdateCycle() {
        var vm = this;

        (function Forever() {
          vm.fetch();
          setTimeout(Forever, 10000);
        })();
      },

      /**
       * Fetch
       */
      fetch() {
        var vm = this;

        console.log('fetching...');
        vm.messagesResource.get().then(function (res) {
          vm.messages = res.data.data.messages.data;
          vm.focusView();
        });
      },

      /**
       * Focus the scrolling to the bottom of the box
       */
      focusView() {
        var vm = this;

        if (vm.messagesCount === null || vm.messagesCount < vm.messages.length) {
          console.log('focusing scrolling');
          $('#js-threadbox').animate({ scrollTop: 10000000 }, 250);
          vm.messagesCount = vm.messages.length;
        }
      }
    }
  };

</script>