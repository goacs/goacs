<template>
  <CCard>
    <CCardHeader>
      <strong>Logs</strong>
      <CButton color="dark" class="float-right" variant="outline" size="sm" @click="modalDialog = true">
        <CIcon name="cil-magnifying-glass" class="btn-icon mt-0" size="sm"></CIcon> All
      </CButton>
    </CCardHeader>
    <CCardBody>
      <table class="table table-sm table-borderless table-hover">
        <thead>
        <tr>
          <th>At</th>
          <th>Message</th>
          <th>Details</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="fault in lastFaults" :key="fault.id">
          <td>{{ fault.created_at | moment }}</td>
          <td>{{ fault.message }}</td>
          <td>Code: {{ fault.detail.faultCode }} String: {{ fault.detail.faultString}}
            <CButton color="dark" class="float-right" variant="outline" size="sm" @click="showDetails(fault)">
              Details
            </CButton>
          </td>
        </tr>
        </tbody>
      </table>
      <LogDetails v-model="details.dialog" :details="details"></LogDetails>
    </CCardBody>
    <LogsModal v-if="device" v-model="modalDialog"></LogsModal>
  </CCard>
</template>

<script>
  import {mapGetters} from "vuex";
  import LogsModal from "./logs/LogsModal";
  import LogDetails from "../../components/LogDetails";

  export default {
    name: "DeviceLogs",
    components: {LogDetails, LogsModal},
    data() {
      return {
        lastFaults: [],
        modalDialog: false,
        details: {
          dialog: false,
          json: '',
        },
      };
    },
    computed:{
      ...mapGetters({
        device: 'device/getDevice',
      }),
    },
    methods: {
      showDetails(fault) {
        this.details.json = fault.detail;
        this.details.xml = fault.full_xml;
        this.details.dialog = true;
      },
      async loadLastFaults() {
        try {
          const response = await this.$store.dispatch('device/listLogs', {
            page: 1,
            filter: [],
            perPage: 5,
            device_id: this.$route.params.id,
          });
          this.lastFaults = response.data.data;
        } catch (e) {

        }
      }
    },
    mounted() {
      this.loadLastFaults();
    }
  }
</script>

<style scoped>

</style>
