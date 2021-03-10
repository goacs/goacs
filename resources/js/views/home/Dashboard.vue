<template>
  <div>
    <CRow>
      <CCol sm="12" lg="4">
        <DashboardWidget icon="cil-gauge" inverse header="Devices count" text="1000" color="primary"/>
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
