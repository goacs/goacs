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
          <th>Model</th>
          <td>{{ device.model_name }}</td>
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
        <tr>
          <th>Debug</th>
          <td>
            <CSwitch
            :checked.sync="device.debug"
            type="checkbox"
            color="dark"
            label-on="yes"
            label-off="no"
          >
          </CSwitch>
          </td>
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
      <CCardFooter>
        <CButton
          size="sm"
          color="dark"
          class="shadow-sm"
          @click="clearCache"
        >
          Clear Cache
        </CButton>
    </CCardFooter>
    <CachedParametersDialog v-model="cachedParamsDialog"></CachedParametersDialog>
    <CElementCover v-if="loading" :opacity="0.8"/>
  </CCard>
</template>

<script>
  import {mapGetters} from "vuex";
  import CachedParametersDialog from "@/components/CachedParametersDialog";

  export default {
    name: "DeviceInfo",
    components: { CachedParametersDialog},
    computed: {
      ...mapGetters({
        hasCachedParams: 'device/hasCachedParams',
        device: 'device/getDevice',
      })
    },
    data() {
      return {
        loading: false,
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
      async kick() {
        try {
          this.loading = true;
          await this.$store.dispatch('device/kickDevice', this.device.id)
        } finally {
          this.loading = false;
        }
      },
      async clearCache() {
        try {
          this.loading = true;
          await this.$store.dispatch('device/clearCache', this.device.id)
        } finally {
          this.loading = false;
        }
      },
      async update() {
        try {
          this.loading = true;
          await this.$store.dispatch('device/updateDevice', this.device);
        } finally {
          this.loading = false;
        }
      },
    },
    watch: {
      'device.debug': {
        handler(value) {
          this.update();
        }
      }
    }
  }
</script>

<style scoped>

</style>
