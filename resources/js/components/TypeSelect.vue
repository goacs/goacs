<template>
  <v-select
    :value="type"
    @input="onTypeSelect"
    :options="types"
    label="name"
    v-bind="{...$attrs, ...$props}"
  ></v-select>
</template>

<script>
  export default {
    name: "TypeSelect",
    props: {
      value: {
        type: String,
        default: () => 'xsd:string',
      },
    },
    data() {
      return {
        type: 'xsd:string',
        types: [
          {
            value: 'xsd:string',
            name: 'xsd:string'
          },
          {
            value: 'xsd:int',
            name: 'xsd:int'
          },
          {
            value: 'xsd:unsignedInt',
            name: 'xsd:unsignedInt'
          },
          {
            value: 'xsd:boolean',
            name: 'xsd:boolean'
          },
          {
            value: 'xsd:dateTime',
            name: 'xsd:dateTime'
          },
          {
            value: 'xsd:base64',
            name: 'xsd:base64'
          },
          {
            value: 'object',
            name: 'object'
          },
        ]
      }
    },
    mounted() {
      this.init();
    },
    methods: {
      init() {
        this.type = this.types.filter(item => item.value === this.value)[0];
        this.$emit('input', this.type.value);
      },
      onTypeSelect(val) {
        this.$emit('input', val.value)
      }
    },
    watch: {
      value: {
        // deep: true,
        handler() {
          this.init();
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
