<template>
  <CCard>
    <CCardHeader>
      <strong>Create new provision</strong>
    </CCardHeader>
    <CCardBody>
      <CInput label="Name" v-model="form.name"></CInput>
      <CInput label="Events" v-model="form.events"></CInput>
      <CInput label="Rule" v-model="form.rule"></CInput>
      <label>Script</label>
      <CodeEditor v-model="form.script"></CodeEditor>
    </CCardBody>
  </CCard>
</template>

<script>
  import CodeEditor from "../../components/CodeEditor";
  export default {
    name: "ConfigurationCreate",
    components: {CodeEditor},
    data() {
      return {
        form: {
          name: '',
          events: '',
          rules: {},
          script: '',
          templates: [],
        }
      };
    },
    methods: {

    }
  }
</script>

<style scoped>

</style>
