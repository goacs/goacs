<template>
  <CCard>
    <CCardHeader>Settings</CCardHeader>
    <CCardBody>
      <ValidationObserver ref="form" v-slot="{ passes }">
        <CForm novalidate @submit.prevent="passes(save)">
          <h5>Behaviour</h5>
          <CInputRadioGroup label="Read behaviour" :options="readBehaviourOptions" :checked.sync="config.read_behaviour" class="mb-3"></CInputRadioGroup>
          <h5>Variables</h5>
          <ValidationProvider vid="lookup_cache_ttl" name="Lookup parameters cache TTL"
                              v-slot="scope">
            <CInput
              type="text"
              label="Lookup parameters cache TTL (minutes)"
              v-model.number="config.lookup_cache_ttl"
              :invalid-feedback="scope.errors[0]"
              :is-valid="isvalid(scope)"
            ></CInput>
          </ValidationProvider>
          <h5>Default parameter values</h5>
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
          <ParametersMapping></ParametersMapping>

          <h5>Webhooks</h5>
          <ValidationProvider vid="webhook_timeout" name="Webhook after device provision"
                              v-slot="scope">
            <CInput
              type="text"
              label="Webhook call timeout"
              v-model="config.webhook_timeout"
              :invalid-feedback="scope.errors[0]"
              :is-valid="isvalid(scope)"
            ></CInput>
          </ValidationProvider>
          <ValidationProvider vid="webhook_after_provision" name="Webhook after device provision"
                              v-slot="scope">
            <CInput
              type="text"
              label="Webhook after device provision"
              v-model="config.webhook_after_provision"
              :invalid-feedback="scope.errors[0]"
              :is-valid="isvalid(scope)"
            ></CInput>
          </ValidationProvider>
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
      readBehaviourOptions() {
        return [
          {
            value: 'boot',
            label: 'Read and store all parameters (except with send flag) on boot event'
          },
          {
            value: 'new',
            label: 'Read and store all parameters only when device is new in ACS',
          },
          {
            value: 'none',
            label: 'Do not store any parameters read from device'
          }
        ]
      }
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
