<template>
  <div v-if="template.id">
    <CRow>
      <CCol sm="12" lg="6">
        <TemplateInfo></TemplateInfo>
      </CCol>
    </CRow>
    <CRow>
      <CCol lg="12">
        <TemplateParameterList></TemplateParameterList>
      </CCol>
    </CRow>
  </div>
</template>

<script>
  import TemplateInfo from "./TemplateInfo";
  import TemplateParameterList from "./TemplateParameterList";
  import {mapGetters} from "vuex";

  export default {
    name: "TemplateView",
    components: {TemplateParameterList, TemplateInfo},
    computed: {
      ...mapGetters({
        template: 'template/getTemplate',
      }),
    },
    async created() {
      await this.$store.dispatch('template/fetchTemplate', this.$route.params.id)
    },
    beforeDestroy() {
      this.$store.commit('template/setTemplate', {})
    }
  }
</script>

<style scoped>

</style>
