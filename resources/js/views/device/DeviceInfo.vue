<template>
  <CCard>
    <CCardHeader>
      <strong>Device Info</strong>
      <CButton color="danger" class="float-right" variant="outline" size="sm" @click="deleteDevice">
        <CIcon name="cil-trash" class="btn-icon mt-0" size="sm"></CIcon>Delete
      </CButton>
    </CCardHeader>
    <CCardBody>
      <table class="table table-sm table-borderless table-hover">
        <tbody>
        <tr>
          <th>ID</th>
          <td>{{ device.id }}</td>
        </tr>
        <tr>
          <th>Product Class</th>
          <td>{{ device.product_class }}</td>
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
    </CCardBody>
    <CCardFooter>
      <CButton
        size="sm"
        color="dark"
        class="shadow-sm"
        @click="kick"
      >
        Provision
      </CButton>
      <CButton
        size="sm"
        color="dark"
        class="shadow-sm"
        @click="lookup"
      >
        Lookup parameters
      </CButton>
      <CButton
          v-if="hasCachedParams"
          size="sm"
          color="dark"
          class="shadow-sm"
          @click="cached"
      >
        Cached parameters
      </CButton>
    </CCardFooter>
<!--  <CachedParametersDialog v-model="cachedParamsDialog"></CachedParametersDialog>-->
  </CCard>
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
        if(confirm('Delete device?') === false) {
          return;
        }
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
