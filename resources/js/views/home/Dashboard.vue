<template>
  <div>
    <CRow>
      <CCol sm="12" lg="4">
        <DashboardWidget icon="cil-gauge" inverse header="Last 24h informs" text="0" color="success"/>
      </CCol>
      <CCol sm="12" lg="4">
        <DashboardWidget icon="cil-gauge" inverse header="Devices" :text="`${dashboard.devices_count}`" color="info"/>
      </CCol>
      <CCol sm="12" lg="4">
        <DashboardWidget icon="cil-gauge" inverse header="Last 24h faults" :text="`${dashboard.faults_count}`" color="danger"/>
      </CCol>
    </CRow>
    <CRow>
      <CCol sm="12">
        <Faults></Faults>
      </CCol>
    </CRow>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import Faults from "./Faults";
  import DashboardWidget from "../../components/DashboardWidget";
  export default {
    name: "Dashboard",
    components: {DashboardWidget, Faults},
    computed: {
      ...mapGetters({
        dashboard: "dashboard/getDashboard"
      }),
      username() {
        return this.$auth.user().name || "UKNNOWN"
      }
    },
    beforeMount() {
      this.$store.dispatch("dashboard/fetchDashboard")
    }
  }
</script>

<style scoped>

</style>
