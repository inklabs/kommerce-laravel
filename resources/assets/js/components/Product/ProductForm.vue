<template>

    <section class="thread-add-message">

      <article v-if="!showForm" class="add-message-toggle" @click="toggleMessageForm()" >
        <input type="text" placeholder="New Message..." style="border: none;background-color: rgba(0,0,0,0);">
      </article>

      <article v-if="showForm" class="backdrop">

        <div class="toggle-message-form" @click="toggleMessageForm()"></div>

        <div v-if="showForm" class="add-message-form">

          <form method="POST" action="{{ action }}">
            <input type="hidden" name="_token" value="{{ csrfToken }}">
            <div class="textbox-container">
              <textarea v-model="note" name="message" rows="5" placeholder="New Message..." style="max-height:250px;">{{ note }}</textarea>
              <small class="text-input-count">{{ note.length }} / 1000</small>
            </div>
            <button type="submit" class="button expanded">
              <slot name="send">Send</slot>
            </button>
          </form>

        </div>

      </article>


    </section>

</template>

<style scoped>
  .add-message-form {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
  }
  .toggle-message-form {
    width: 100%;
    height: 100%;
  }
  .textbox-container {
    position: relative;
    margin-bottom: 25px;
  }
  .text-input-count {
    position: absolute;
    z-index: 2;
    color: lightgray;
    bottom: 6px;
    right: 12px;
  }
</style>

<script>
    export default {

        props: {
            'show': {
                type: Boolean,
                required: true
            },
            'action': {
                type: String,
                required: true
            },
            'users': {
                type: Object,
                required: true
            },
            'note': {
                type: String,
                required: true
            }
        },

        data() {

          return {
            showForm: false,
            showToggle: true
          };
        },

        methods: {
          toggleMessageForm() {
            var vm = this;
            vm.showForm = (!vm.showForm);
            vm.showToggle = (!vm.showToggle);

            if (vm.showForm) {
              $('#js-select-users').selectize({
                delimiter: ',',
                maxItems: 10
              });
            }

          }
        },

        computed: {
            csrfToken() {
                return document.querySelector('meta[name=csrf-token]').getAttribute('content');
            },
        }
    };
</script>
