<template>
  <div>
    <CAlert color="info">Use $root variable, to replace it with correct device root</CAlert>
    <div v-for="(mapping, idx) in mappings">
      <CRow>
        <CCol md="4">
          <CInput v-model="mappings[idx].parameter" label="Parameter"></CInput>
        </CCol>
        <CCol md="4">
          <CInput v-model="mappings[idx].name" label="Name"></CInput>
        </CCol>
      </CRow>
    </div>
  </div>
</template>

<script>
export default {
  name: "ParametersMapping",
  computed: {
    mappings: {
      get() {
        return this.$store.getters['config/getConfig']['mappings'];
      },
      set(mappings) {
        const config = this.$store.getters['config/getConfig']
        config.mappings = mappings;
        this.$store.commit('config/setConfig', config);
      }
    },
  }
}
</script>

<style scoped>

</style>
