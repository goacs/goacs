<template>
  <CCard>
    <CCardHeader>
      <strong>Create new provision</strong>
    </CCardHeader>
    <CCardBody>
      <ValidationObserver ref="validator" v-slot="{ passes }">
        <ConfigurationForm ref="form" v-model="form"></ConfigurationForm>
      </ValidationObserver>
    </CCardBody>
    <CCardFooter>
      <CButton
        @click="save"
        color="dark"
      >
        Save
      </CButton>
    </CCardFooter>
    <CElementCover v-if="saving" :opacity="0.8"/>

  </CCard>
</template>

<script>
import ConfigurationForm from "./ConfigurationForm";
import {mapGetters} from "vuex";
  export default {
    name: "ConfigurationCreate",
    components: {ConfigurationForm},
    computed: {
      ...mapGetters({
        form: "configuration/getProvision",
      })
    },
    data() {
      return {
        saving: false,
      };
    },
    methods: {
      async save() {
        this.$emit('onSave', this.form);
        try {
          this.saving = true
          const response = await this.$store.dispatch('configuration/store', this.form);
          await this.$router.push({ name: 'configuration-edit', params: {id: response.data.data.id}})
        } catch (e) {
          console.log(e.response.data.errors);
          this.$refs.validator.setErrors(e.response.data.errors);
        } finally {
          this.saving = false
        }
      }
    },
    mounted() {
      this.form.addRule();
      // this.addDenied();
    }
  }
</script>

<style scoped>

</style>
