<template>
  <v-select
    :value="selectedRequests"
    @input="onRequestSelected"
    :options="requests"
    label="name"
    multiple
    v-bind="{...$attrs, ...$props}"
  ></v-select>
</template>

<script>
  export default {
    name: "RequestSelect",
    props: {
      value: {
        type: String,
        default: () => '',
      },
    },
    data() {
      return {
        selectedRequests: [],
        requests: [
          {
            value: 'Empty',
            name: 'Empty'
          },
          {
            value: 'GetParameterValuesResponse',
            name: 'GetParameterValuesResponse'
          },
          {
            value: 'Inform',
            name: 'Inform'
          },
          {
            value: 'TransferComplete',
            name: 'TransferComplete'
          },
        ]
      }
    },
    mounted() {
      this.initializeRequests();
    },
    methods: {
      initializeRequests() {

        this.selectedRequests = [];
        const splittedValue = this.value.split(',')
        console.log(splittedValue);
        this.requests.forEach(event => {
          if(splittedValue.includes(event.value) === true) {
            this.selectedRequests.push(event)
          }
        })
      },
      onRequestSelected(val) {
        const selectedRequests = []
        val.forEach(item => {
          selectedRequests.push(item.value);
        })
        selectedRequests.sort();
        this.$emit('input', selectedRequests.join(','))
      }
    },
    watch: {

      value: {
        // deep: true,
        handler() {
          console.log('value changed');
          this.initializeRequests();
        }
      }
    },

  }
</script>

<style scoped>
  .dropdown-menu {
    z-index: 101;
  }
</style>
