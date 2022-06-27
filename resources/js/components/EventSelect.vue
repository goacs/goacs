<template>
  <v-select
    :value="selectedEvents"
    @input="onEventSelected"
    :options="events"
    label="name"
    multiple
    v-bind="{...$attrs, ...$props}"
  ></v-select>
</template>

<script>
  export default {
    name: "EventSelect",
    props: {
      value: {
        type: String,
        required: true,
      },
    },
    data() {
      return {
        selectedEvents: [],
        events: [
          {
            value: '0 BOOTSTRAP',
            name: '0 BOOTSTRAP'
          },
          {
            value: '1 BOOT',
            name: '1 BOOT'
          },
          {
            value: '2 PERIODIC',
            name: '2 PERIODIC'
          },
          {
            value: '4 VALUE CHANGE',
            name: '4 VALUE CHANGE',
          },
          {
            value: '6 CONNECTION REQUEST',
            name: '6 CONNECTION REQUEST'
          },
          {
            value: '7 TRANSFER COMPLETE',
            name: '7 TRANSFER COMPLETE'
          },
        ]
      }
    },
    mounted() {
      this.initializeEvents();
    },
    methods: {
      initializeEvents() {
        if(!this.value) {
          return
        }
        this.selectedEvents = [];
        const splittedValue = this.value.split(',')
        console.log(splittedValue);
        this.events.forEach(event => {
          if(splittedValue.includes(event.value) === true) {
            this.selectedEvents.push(event)
          }
        })
      },
      onEventSelected(val) {
        const selectedEvents = []
        val.forEach(item => {
          selectedEvents.push(item.value);
        })
        selectedEvents.sort();
        this.$emit('input', selectedEvents.join(','))
      }
    },
    watch: {

      value: {
        // deep: true,
        handler() {
          console.log('value changed');
          this.initializeEvents();
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
