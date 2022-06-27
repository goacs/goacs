<template>
  <div>
  <ValidationProvider vid="name" name="Name"
                      v-slot="scope">
    <CInput
      label="Name"
      v-model="form.name"
      :invalid-feedback="scope.errors[0]"
      :is-valid="isvalid(scope)"
    ></CInput>
  </ValidationProvider>
  <ValidationProvider vid="events" name="Events"
                      v-slot="scope">
    <label>Events</label>
    <EventSelect
      class="mb-4"
      v-model="form.events"
                 :invalid-feedback="scope.errors[0]"
                 :is-valid="isvalid(scope)"></EventSelect>


  </ValidationProvider>
  <CRow class="mb-2">
    <CCol sm="12">
      <label>Rules</label>
      <RuleItem v-for="(rule, idx) in form.rules" v-model="form.rules[idx]" :idx="idx" :key="`rule-${rule.uniq}`" @remove="removeRule(idx)"></RuleItem>
      <CButton color="dark" size="sm" variant="outline" @click="addRule">
        <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon>Add rule
      </CButton>
    </CCol>
  </CRow>
  <label>Script</label>
  <ValidationProvider vid="script" name="Script"
                      v-slot="scope">
    <CodeEditor class="mb-3" v-model="form.script" ></CodeEditor>
  </ValidationProvider>
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
      <CRow alignVertical="center" v-for="(denied, idx) in form.denied"  :key="`denied-${denied.uniq}`">
        <CCol sm="11">
          <ValidationProvider :vid="`denied.${idx}.value`" name="Script"
                              v-slot="scope">
            <CInput label="Parameter"  v-model="form.denied[idx].parameter"
                    :invalid-feedback="scope.errors[0]"
                    :is-valid="isvalid(scope)"></CInput>
          </ValidationProvider>
        </CCol>
        <CCol sm="1">
          <CButton color="dark" size="sm" variant="outline" @click="removeDenied(idx)">
            <CIcon name="cil-minus" class="btn-icon mt-0" size="sm"></CIcon>Remove
          </CButton>
        </CCol>
      </CRow>
      <CButton color="dark" size="sm" variant="outline" @click="addDenied">
        <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon>Add denied
      </CButton>
    </CCol>
  </CRow>
  </div>
</template>

<script>
import RuleItem from "../../components/rule/RuleItem";
import CodeEditor from "../../components/CodeEditor";
import EventSelect from "../../components/EventSelect";

export default {
  name: "ConfigurationForm",
  components: {EventSelect, RuleItem, CodeEditor},
  computed: {
    form: {
      set(val) {
        this.$store.commit('configuration/setProvision', val);
      },
      get() {
        return this.$store.getters['configuration/getProvision'];
      }
    }
  },
  data() {
    return {
      ruleUniq: 0,
    };
  },
  methods: {
    addRule() {
      this.form.rules.push({
        uniq: this.ruleUniq++,
        operator: '>',
      })
    },
    addDenied() {
      this.form.denied.push({
        uniq: this.ruleUniq++,
      })
    },
    removeRule(rule) {
      this.$delete(this.form.rules, rule)
    },
    removeDenied(denied) {
      console.log('rem den', denied);
      this.$delete(this.form.denied, denied);
    },
  },
}
</script>

<style scoped>

</style>
