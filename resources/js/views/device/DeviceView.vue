<template>
  <div v-if="device.id">
    <div class="columns">
      <div class="column is-half">
        <DeviceInfo></DeviceInfo>
      </div>
      <div class="column is-half">
        <DeviceQueuedTasks></DeviceQueuedTasks>
        <DeviceTemplates></DeviceTemplates>
        <DeviceLogs></DeviceLogs>
      </div>
    </div>
    <div class="columns">
      <div class="column">
        <DeviceParameterList></DeviceParameterList>
      </div>
    </div>
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
