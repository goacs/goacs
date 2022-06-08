<template>
  <CCard>
    <CCardHeader>
      <strong>Create new provision</strong>
    </CCardHeader>
    <CCardBody>
      <CInput label="Name" v-model="form.name"></CInput>
      <CInput label="Events" v-model="form.events"></CInput>
      <CRow class="mb-2">
        <CCol sm="12">
      <label>Rules</label>
      <RuleItem v-for="(rule, idx) in form.rules" v-model="form.rules[idx]" :key="`rule-${rule.uniq}`" @remove="remove(idx)"></RuleItem>
      <CButton color="dark" size="sm" variant="outline" @click="addRule">
        <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon>Add rule
      </CButton>
        </CCol>
      </CRow>
      <label>Script</label>
      <CodeEditor v-model="form.script"></CodeEditor>
    </CCardBody>
  </CCard>
</template>

<script>
  import CodeEditor from "../../components/CodeEditor";
  import RuleItem from "../../components/rule/RuleItem";
  export default {
    name: "ConfigurationCreate",
    components: {RuleItem, CodeEditor},
    data() {
      return {
        ruleUniq: 0,
        form: {
          name: '',
          events: '',
          rules: [],
          script: '',
          templates: [],
        }
      };
    },
    methods: {
      addRule() {
        this.form.rules.push({
          uniq: this.ruleUniq++,
        })
      },
      remove(rule) {
          this.$delete(this.form.rules, rule)
      }
    },
    mounted() {
      this.addRule();
    }
  }
</script>

<style scoped>

</style>
