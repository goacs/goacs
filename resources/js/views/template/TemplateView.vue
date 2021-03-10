<template>
  <div v-if="template.id">
    <div class="columns">
      <div class="column is-half">
        <TemplateInfo></TemplateInfo>
      </div>
    </div>
    <div class="columns">
      <div class="column">
        <TemplateParameterList></TemplateParameterList>
      </div>
    </div>
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
