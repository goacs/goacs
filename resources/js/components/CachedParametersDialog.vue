<template>
  <b-modal
      v-model="value"
      has-modal-card
      full-screen
      :canCancel="false"
  >
    <div class="modal-card">
      <header class="modal-card-head">
        <p class="modal-card-title">Cached parameters</p>
      </header>
      <section class="modal-card-body">
        <table class="table">
          <tr>
            <th>Name</th>
            <th>Value</th>
          </tr>
          <tr v-for="parameter in cachedParams" :key="parameter.name">
            <td>{{parameter.name}}</td>
            <td>{{parameter.value}}</td>
          </tr>
        </table>
      </section>
      <footer class="modal-card-foot">
        <b-button @click="$emit('input', false)">Close</b-button>
      </footer>
    </div>
  </b-modal>
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
  }
}
</script>

<style scoped>

</style>