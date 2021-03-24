<template>
  <CModal
    title="Lookup parameters"
    size="xl"
    color="dark"
    centered
    :closeOnBackdrop="false"
    :show="value"
    @update:show="onModalClose"
  >
    <table class="table">
      <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Value</th>
      </tr>
      <tr v-for="parameter in cachedParams" :key="parameter.name">
        <td>{{ parameter.name }}</td>
        <td>{{ parameter.type }}</td>
        <td>{{ stripString(parameter.value, 100) }}</td>
      </tr>
    </table>
  </CModal>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "CachedParametersDialog",
  data() {
    return {
      saving: false,
    }
  },
  props: {
    value: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    ...mapGetters({
      device: 'device/getDevice',
      hasCachedParams: 'device/hasCachedParams',
      cachedParams: 'device/getCachedParameters'
    }),
  },
  methods: {
    async onModalClose(_, event, accept) {
      this.$emit('input', false);
    },
    stripString(prop, len) {
      if(prop.length < len) {
        return prop;
      }
      return prop.substr(0, len);
    },
  }
}
</script>

<style scoped>

</style>
