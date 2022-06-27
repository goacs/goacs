<template>
  <v-select
    :value="selectedFlags"
    @input="onFlagSelected"
    :options="flags"
    label="name"
    multiple
    v-bind="{...$attrs, ...$props}"
  ></v-select>
</template>

<script>
  export default {
    name: "FlagInput",
    props: {
      value: {
        type: Object,
        required: true,
      },
    },
    data() {
      return {
        selectedFlags: [],
        flags: [
          {
            value: 'read',
            name: 'Read'
          },
          {
            value: 'write',
            name: 'Write'
          },
          {
            value: 'send',
            name: 'Send'
          },
          {
            value: 'object',
            name: 'Object'
          },
          {
            value: 'system',
            name: 'System'
          },
        ]
      }
    },
    mounted() {
      this.initializeFlag();
    },
    methods: {
      initializeFlag() {
        if(!this.value) {
          return
        }
        this.selectedFlags = [];
        this.flags.forEach(flag => {
          if(this.value[flag.value] === true) {
            this.selectedFlags.push(flag)
          }
        })
      },
      onFlagSelected(val) {
        const flagsObject = {}
        val.forEach(item => {
          flagsObject[item.value] = true
        })
        this.$emit('input', flagsObject)
      }
    },
    watch: {

      value: {
        // deep: true,
        handler() {
          console.log('value changed');
          this.initializeFlag();
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
