<template>
  <Multiselect
    :options="flags"
    label="name"
    track-by="value"
    multiple
    v-model="selectedFlags"
    variant="dark"
    placeholder="Select flags"
  >
  </Multiselect>
</template>

<script>
  import Multiselect from "./Multiselect";
  export default {
    name: "FlagInput",
    components: {Multiselect},
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
      this.initializeFlag()
    },
    methods: {
      initializeFlag() {
        if(!this.value) {
          return
        }

        const initialFlags = this.value
        this.flags.forEach(flag => {
          if(initialFlags[flag.value] === true) {
            this.selectedFlags.push(flag)
          }
        })
      }
    },
    watch: {
      selectedFlags(val) {
        const flagsObject = {}
        val.forEach(item => {
          flagsObject[item.value] = true
        })
        this.$emit('input', flagsObject)
      },
    }
  }
</script>

<style scoped>
  .dropdown-menu {
    z-index: 101;
  }
</style>
