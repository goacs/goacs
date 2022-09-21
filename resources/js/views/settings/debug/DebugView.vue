<template>
  <CCard>
    <CCardHeader>Debug configuration</CCardHeader>
    <CCardBody>
      <ValidationObserver ref="form" v-slot="{ passes }">
        <CForm novalidate @submit.prevent="passes(login)">
          <CRow>
            <CCol sm="12">
              <ValidationProvider vid="conversation_log" name="Conversation log"
                                  v-slot="scope">
                <div class="form-group">
                  <label>Log ACS<->DEVICE conversation</label>
                  <div class="form-control-plaintext">
                    <CSwitch
                      :checked.sync="debug.debug"
                      type="checkbox"
                      color="dark"
                      label-on="yes"
                      label-off="no"
                    >
                    </CSwitch>
                  </div>
                </div>
              </ValidationProvider>
            </CCol>
          </CRow>
          <CRow>
            <CCol sm="12">
              <ValidationProvider vid="debug_new_devices" name="Debug new devices"
                                  v-slot="scope">
                <div class="form-group">
                  <label>Debug newly added devices in ACS</label>
                  <div class="form-control-plaintext">
                    <CSwitch
                      :checked.sync="debug.debug_new_devices"
                      type="checkbox"
                      color="dark"
                      label-on="yes"
                      label-off="no"
                    >
                    </CSwitch>
                  </div>
                </div>
              </ValidationProvider>
            </CCol>
          </CRow>
          <CRow>
            <CCol sm="12" md="6">
              <ValidationProvider vid="devices" name="Devices"
                                  v-slot="scope">
                <div class="form-group">
                  <label>Enabled Devices</label>
                  <div class="form-control-plaintext">
                    <CListGroup>
                      <CListGroupItem v-for="(device, idx) in debug.devices" :key="device.id" class="d-flex justify-content-between align-items-center">
                        <router-link :to="{name: 'devices-view', params: {id: device.id}}">{{ device.serial_number }}</router-link>
                        <CButton
                          size="sm"
                          color="dark"
                          variant="ghost"
                          @click="disableDeviceDebug(idx)"
                        >
                          <CIcon name="cil-trash"/>
                        </CButton>
                      </CListGroupItem>
                    </CListGroup>
                  </div>
                </div>
              </ValidationProvider>
            </CCol>
          </CRow>


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
export default {
  name: "DebugView",
  data() {
    return {
      saving: false,
      saved: false,
      errors: [],
    };
  },
  computed: {
    debug: {
      get() {
        return this.$store.state.config.debug;
      },
      set(val) {
        this.$store.commit('config/setDebug', val);
      }
    }
  },
  methods: {
    async save() {
      try {
        this.saving = true;
        const devices = this.debug.devices.map(item => item.id);
        await this.$store.dispatch('config/saveDebugConfig', { debug: this.debug.debug, debug_new_devices: this.debug.debug_new_devices, devices });
        this.saved = true;
        setTimeout(() => this.saved = false, 5000);
      } catch (e) {
        console.log(e.response.data.errors);
        this.$refs.form.setErrors(e.response.data.errors);
      } finally {
        this.saving = false;
      }
    },
    disableDeviceDebug(idx) {
      this.debug.devices.splice(idx, 1);
    }
  },
  async beforeMount() {
    await this.$store.dispatch('config/fetchDebugConfig');
  }

}
</script>

<style scoped>

</style>
