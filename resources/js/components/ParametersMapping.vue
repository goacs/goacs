<template>
  <div>
    <div v-for="(mapping, idx) in mappings">
      <CInput v-model="mappings[idx].parameter" label="Parameter"></CInput>
      <CInput v-model="mappings[idx].name" label="Name"></CInput>
    </div>
  </div>
</template>

<script>
export default {
  name: "ParametersMapping",
  data() {
    return {
      mappings: [],
    };
  },
  props: {
    value: {
      type: Array,
      required: true,
    },
  },
  mounted() {
    this.mappings = this.value;
  },
  watch: {
    mappings: {
      deep: true,
      handler(val) {
        this.$emit('input', val);
      }
    }
  }
}
</script>

<style scoped>

</style>
