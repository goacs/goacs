<template>
<CRow alignVertical="center">
  <CCol sm="4">
  <CInput label="Parameter" v-model="form.parameter"></CInput>
    </CCol>
  <CCol sm="1">
    <CSelect label="Operator" :value.sync="form.op" :options="options"></CSelect>
  </CCol>
  <CCol sm="3">
    <CInput label="Value" v-model="form.value"></CInput>
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
    }
  },
  data() {
    return {
      operators: ['>', '>=', '<', '<=', '==', '!=', 'in', 'not in'],
      form: {
        parameter: '',
        op: '',
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
