<template>
  <CCard>
    <CCardHeader>Settings</CCardHeader>
    <CCardBody>
      <h5>Base</h5>
      <ValidationObserver ref="form" v-slot="{ passes }">
        <CForm novalidate @submit.prevent="passes(login)">
          <ValidationProvider vid="pii" name="Periodic Inform Interval spread"
                              v-slot="scope">
          <CInput
            type="text"
            label="Periodic Inform Interval spread"
            v-model="config.pii"
            :invalid-feedback="scope.errors[0]"
            :is-valid="isvalid(scope)"
          ></CInput>
          </ValidationProvider>
          <ValidationProvider vid="connection_request_username" name="Connection Request Username"
                              v-slot="scope">
            <CInput
              type="text"
              label="Connection Request Username"
              v-model="config.connection_request_username"
              :invalid-feedback="scope.errors[0]"
              :is-valid="isvalid(scope)"
            ></CInput>
          </ValidationProvider>
          <ValidationProvider vid="connection_request_password" name="Connection Request Password"
                              v-slot="scope">
            <CInput
              type="text"
              label="Connection Request Password"
              v-model="config.connection_request_password"
              :invalid-feedback="scope.errors[0]"
              :is-valid="isvalid(scope)"
            ></CInput>
          </ValidationProvider>
          <h5>Mappings</h5>

          <ParametersMapping v-model="config.mapping"></ParametersMapping>
        </CForm>
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
  import ParametersMapping from "../../components/ParametersMapping";
  export default {
    name: "BaseSettings",
    components: {ParametersMapping},
    data() {
      return {
        saving: false,
        saved: false,
        errors: [],
      };
    },
    computed: {
      config: {
        get() {
          return this.$store.getters['config/getConfig'];
        },
        set(config) {
          this.$store.commit('config/setConfig', config);
        }
      },
    },
    methods: {
      async save() {
        try {
          this.saving = true
          await this.$store.dispatch('config/saveConfig', this.config);
          this.saved = true
          setTimeout(() => this.saved = false, 5000);
        } catch (e) {
          console.log(e.response.data.errors);
          this.$refs.form.setErrors(e.response.data.errors);
        } finally {
          this.saving = false
        }
      }
    },
    async beforeMount() {
      try {
        const response = await this.$store.dispatch('config/getConfig')
        this.$store.commit('config/setConfig', response.data.data)
      } catch (e) {
      }
    }

  }
</script>

<style scoped>

</style>
