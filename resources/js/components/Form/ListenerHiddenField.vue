<template>
  <input :id="field.attribute" type="hidden" v-model="value" />
</template>

<script>
import { FormField } from 'laravel-nova'
export default {
  mixins: [FormField],
  props: ['resourceName', 'resourceId', 'field'],

  created() {
    Nova.$on(this.field.listensTo, this.messageReceived)
  },

  data: () => ({
    calculating: false,
    field_values: {}
  }),

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = this.field.value || ''
    },

    messageReceived(message) {
      this.field_values[message.field_name] = message.value;
      this.calculateValue()
    },

    calculateValue: _.debounce(function () {
      this.calculating = true;

      Nova.request().post(
          `/nova-vendor/inventarios/calculate/${this.resourceName}/${this.field.attribute}`,
          this.field_values
      ).then((response) => {
        if (
            !(response.data.disabled && this.field.isUpdating)
        ) {
          this.value = response.data.value
        }
        this.calculating = false;
      }).catch(() => {
        this.calculating = false;
      });
    }, 500),
  },
}
</script>