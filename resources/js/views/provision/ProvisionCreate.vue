<template>
  <CCard>
    <CCardHeader>
      <strong>Create new provision</strong>
      <CButton color="dark" class="float-right" variant="outline" size="sm" @click="dialog = true">
        <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon>Add
      </CButton>
    </CCardHeader>
    <CCardBody>
    </CCardBody>
  </CCard>
</template>

<script>
  import PaginatedTable from "../../components/PaginatedTable";
  export default {
    name: "ProvisionCreate",
    components: {PaginatedTable},
    data() {
      return {

      };
    },
    methods: {

    }
  }
</script>

<style scoped>

</style>
