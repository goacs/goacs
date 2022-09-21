<template>
  <CCard>
    <CCardHeader>
      <strong>Edit provision</strong>
      <CButton class="float-right" variant="outline" color="danger" size="sm" @click="remove">
        <CIcon name="cil-trash" class="btn-icon mt-0" size="sm"></CIcon>Remove
      </CButton>
      <CButton class="float-right mr-4" variant="outline" color="primary" size="sm" @click="clone">
        <CIcon name="cil-clone" class="btn-icon mt-0" size="sm"></CIcon>Clone
      </CButton>
    </CCardHeader>
    <CCardBody>
      <ValidationObserver ref="validator" v-slot="{ passes }">
        <ConfigurationForm ref="form"></ConfigurationForm>
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
  name: "ConfigurationEdit",
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
      this.$emit('onUpdate', this.form);
      try {
        this.saving = true
        await this.$store.dispatch('configuration/update', this.form);
      } catch (e) {
        console.log(e.response.data.errors);
        this.$refs.validator.setErrors(e.response.data.errors);
      } finally {
        this.saving = false
      }

    },
    async remove() {
      if(confirm('Delete configuration?') === false) {
        return;
      }
      try {
        await this.$store.dispatch('configuration/destroy', this.form.id);
        await this.$router.push({ name: 'configuration-list'})
      } catch (e) {

      }
    },
    async clone() {
      if(confirm('Did you want to clone this configuration?') === false) {
        return;
      }

      try {
        const response = await this.$store.dispatch('configuration/clone', this.form.id);
        await this.$router.push({ name: 'configuration-edit', params: {id: response.data.data.id}})

      } catch (e) {

      }
    }
  },
  async beforeMount() {
    await this.$store.dispatch('configuration/fetchProvision', this.$route.params.id)
  },
  beforeDestroy() {
    this.$store.commit('configuration/resetProvision');
  }
}
</script>

<style scoped>

</style>
