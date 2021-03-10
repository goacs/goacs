<template>
  <div class="card">
    <header class="card-header">
      <p class="card-header-title">
        Device Info
      </p>
      <div class="card-header-icon" aria-label="more options">
        <b-button
                type="is-danger"
                size="is-small"
                @click="deleteDevice"
        >
          <b-icon
                  icon="delete"
                  size="is-small"
          >

          </b-icon>
          Delete
        </b-button>
      </div>
    </header>
    <div class="card-content">
      <div class="table-container">
      <table class="table is-fullwidth is-striped">
        <tbody>
        <tr>
          <th>UUID</th>
          <td>{{ device.id }}</td>
        </tr>
        <tr>
          <th>Manufacturer</th>
          <td>{{ device.manufacturer }}</td>
        </tr>
        <tr>
          <th>OUI</th>
          <td>{{ device.oui }}</td>
        </tr>
        <tr>
          <th>Serial</th>
          <td>{{ device.serial_number }}</td>
        </tr>
        <tr>
          <th>Hardware version</th>
          <td>{{ device.hardware_version }}</td>
        </tr>
        <tr>
          <th>Software version</th>
          <td>{{ device.software_version }}</td>
        </tr>
        <tr>
          <th>Connection request URL</th>
          <td><a :href="device.connection_request_url">{{ device.connection_request_url }}</a></td>
        </tr>
        <tr>
          <th>Last connection time</th>
          <td>{{ device.updated_at | moment }}</td>
        </tr>
        </tbody>
      </table>
      </div>
      <div class="buttons">
        <b-button
        @click="kick"
        >
          Provision
        </b-button>
        <b-button
                @click="lookup"
        >
          Lookup parameters
        </b-button>
        <b-button
            v-if="hasCachedParams"
            @click="cached"
        >
          Cached parameters
        </b-button>
      </div>
    </div>
  <CachedParametersDialog v-model="cachedParamsDialog"></CachedParametersDialog>
  </div>
</template>

<script>
  import {mapGetters} from "vuex";
  import CachedParametersDialog from "@/components/CachedParametersDialog";

  export default {
    name: "DeviceInfo",
    components: {CachedParametersDialog},
    computed: {
      ...mapGetters({
        device: 'device/getDevice',
        hasCachedParams: 'device/hasCachedParams'
      }),
    },
    data() {
      return {
        cachedParamsDialog: false,
      };
    },
    mounted() {
    },
    methods: {
      async deleteDevice() {
        await this.$store.dispatch('device/deleteDevice', this.device.id)
        await this.$router.push({ name: 'devices-list'})
      },
      kick() {
        this.$store.dispatch('device/kickDevice', this.device.id)
      },
      lookup() {
        this.$store.dispatch('device/lookup', this.device.id)
      },
      cached() {
        this.$store.dispatch('device/fetchCachedParameters', this.device.id);
        this.cachedParamsDialog = true;
      }
    }
  }
</script>

<style scoped>

</style>
