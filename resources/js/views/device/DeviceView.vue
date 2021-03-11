<template>
  <div v-if="device.id">
    <CRow>
      <CCol sm="12" lg="6">
        <DeviceInfo></DeviceInfo>
      </CCol>
      <CCol sm="12" lg="6">
        <DeviceQueuedTasks></DeviceQueuedTasks>
        <DeviceTemplates></DeviceTemplates>
        <DeviceLogs></DeviceLogs>
      </CCol>
    </CRow>
    <CRow>
      <CCol lg="12">
        <DeviceParameterList></DeviceParameterList>
      </CCol>
    </CRow>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import DeviceInfo from "./DeviceInfo";
import DeviceParameterList from "./DeviceParameterList";
import DeviceLogs from "./DeviceLogs";
import DeviceQueuedTasks from "./DeviceQueuedTasks";
import DeviceTemplates from "./DeviceTemplates";
export default {
  name: "DeviceView",
  components: {DeviceTemplates, DeviceQueuedTasks, DeviceLogs, DeviceParameterList, DeviceInfo},
  data() {
    return {

    };
  },
  methods: {

  },
  computed: {
    ...mapGetters({
      device: 'device/getDevice',
    }),
  },
  async beforeMount() {
    await this.$store.dispatch('device/fetchDevice', this.$route.params.id)
  },
  beforeDestroy() {
    this.$store.commit('device/setDevice', {})
  }

}
</script>

<style scoped>

</style>
