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
      <CodeEditor class="mb-3" v-model="form.script"></CodeEditor>
      <CRow class="mb-2">
        <CCol sm="12">
          <label>Denied parameters</label>
          <CAlert color="info">
            <ul class="mb-0">
              <li>Following parameters will not be saved in the device parameters</li>
              <li>Use $root variable, to replace it with correct device root</li>
              <li>You can also use wildcard syntax with asterisk, ex. InternetGatewayDevice.WANDevice.*.WANConnectionDevice.*.ConnectionStatus</li>
            </ul>
          </CAlert>
          <CInput label="Parameter" v-for="(denied, idx) in form.denied"  :key="`denied-${denied.uniq}`" v-model="form.denied[idx].value"></CInput>
        </CCol>
      </CRow>
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
          denied: [],
        }
      };
    },
    methods: {
      addRule() {
        this.form.rules.push({
          uniq: this.ruleUniq++,
        })
      },
      addDenied() {
        this.form.denied.push({
          uniq: this.ruleUniq++,
        })
      },
      remove(rule) {
          this.$delete(this.form.rules, rule)
      }
    },
    mounted() {
      this.addRule();
      this.addDenied();
    }
  }
</script>

<style scoped>

</style>
