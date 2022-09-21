<template>
  <div v-if="device.id">
    <CRow>
      <CCol lg="12">
        <DeviceCachedParameterList></DeviceCachedParameterList>
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
import DeviceCachedParameterList from "./DeviceCachedParameterList";
export default {
  name: "DeviceCachedParametersView",
  components: {
    DeviceCachedParameterList,
    DeviceTemplates, DeviceQueuedTasks, DeviceLogs, DeviceParameterList, DeviceInfo},
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
