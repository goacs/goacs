<template>
  <div>
    <CRow>
      <CCol sm="12" lg="4">
        <DashboardWidget icon="cil-gauge" inverse header="Last 24h informs" text="1000" color="success"/>
      </CCol>
      <CCol sm="12" lg="4">
        <DashboardWidget icon="cil-gauge" inverse header="Last 24h faults" text="1000" color="danger"/>
      </CCol>
      <CCol sm="12" lg="4">
        <DashboardWidget icon="cil-gauge" inverse header="Devices count" text="1000" color="info"/>
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
