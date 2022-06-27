<template>
<CRow alignVertical="center">
  <CCol sm="4">
    <ValidationProvider :vid="`rules.${idx}.parameter`" name="Parameter"
                        v-slot="scope">
      <CInput label="Parameter" v-model="form.parameter"
              :invalid-feedback="scope.errors[0]"
              :is-valid="isvalid(scope)"></CInput>
    </ValidationProvider>
    </CCol>
  <CCol sm="1">
    <ValidationProvider :vid="`rules.${idx}.operator`" name="Operator"
                        v-slot="scope">
      <CSelect label="Operator" :value.sync="form.operator" :options="options"
               :invalid-feedback="scope.errors[0]"
               :is-valid="isvalid(scope)"></CSelect>
    </ValidationProvider>
  </CCol>
  <CCol sm="3">
    <ValidationProvider :vid="`rules.${idx}.value`" name="Value"
                        v-slot="scope">
      <CInput label="Value" v-model="form.value"
              :invalid-feedback="scope.errors[0]"
              :is-valid="isvalid(scope)"></CInput>
    </ValidationProvider>
  </CCol>
  <CCol sm="1">
    <CButton color="dark" size="sm" variant="outline" @click="$emit('remove')">
      <CIcon name="cil-minus" class="btn-icon mt-0" size="sm"></CIcon>Remove
    </CButton>
  </CCol>
</CRow>
</template>

<script>
export default {
  name: "RuleItem",
  props: {
    value: {
      required: true,
    },
    idx: {
      required: true,
    }
  },
  data() {
    return {
      operators: ['>', '>=', '<', '<=', '==', '!=', 'in', 'not in'],
      form: {
        parameter: '',
        operator: '>',
        value: '',
      }
    }
  },
  computed: {
    options() {
      return this.operators.map(item => {
        return {
          value: item,
          label: item,
        }
      })
    }
  },
  watch: {
    form: {
      deep: true,
      handler(val) {
        this.$emit('input', val);
      }
    }
  },
  mounted() {
    this.form = this.value;
  }
}
</script>

<style scoped>

</style>
