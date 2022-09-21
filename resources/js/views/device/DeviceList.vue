<template>
  <CCard>
    <CCardHeader>
        <strong>Device list</strong>
    </CCardHeader>
  <CCardBody>
    <PaginatedTable
      :fields="fields"
      :columnFilter='{ external: true, lazy: true }'
      action="device/list"
      :autoload="false"
      :dense="true"
      ref="table"
    >
      <template #updated_at="{ item }">
        <td>{{ item.updated_at | moment }}</td>
      </template>
      <template #actions="{ item }">
        <td>
          <CButton color="dark" class="float-right" variant="outline" size="sm" :to="{ name: 'devices-view', params: { id: item.id}}">
            <CIcon name="cil-magnifying-glass" class="btn-icon mt-0" size="sm"></CIcon> View
          </CButton>
        </td>
      </template>
    </PaginatedTable>
  </CCardBody>
  </CCard>
</template>

<script>
import PaginatedTable from "../../components/PaginatedTable";
export default {
  name: "DeviceList",
  components: {PaginatedTable},
  data() {
    return {
      fields: [
        {
          key: 'id',
          label: 'ID',
        },
        {
          key: 'serial_number',
          label: 'Serial Number'
        },
        {
          key: 'product_class',
          label: 'Product Class'
        },
        {
          key: 'model_name',
          label: 'Model'
        },
        {
          key: 'software_version',
          label: 'Software'
        },
        {
          key: 'updated_at',
          label: 'Last inform'
        },
        {
          key: 'actions',
          label: '',
          filter: false,
        }
      ]
    };
  },
  methods: {
  },
  mounted() {
  }

}
</script>

<style scoped>

</style>
