<template>
  <CCard>
    <CCardHeader>
      <strong>Faults</strong>
      <CButton color="dark" class="float-right" variant="outline" size="sm">
        <CIcon name="cil-magnifying-glass" class="btn-icon mt-0" size="sm"></CIcon> All
      </CButton>
    </CCardHeader>
    <CCardBody>
      <PaginatedTable
        :action="action"
        :columnFilter='{ external: true, lazy: true }'
        :fields="fields"
        ref="table"
      >
        <template #device_id="{item}">
          <td>
            <router-link v-if="item.device" :to="{name: 'devices-view', params: {id: item.device_id}}">
              {{ item.device.serial_number }}
            </router-link>
            <span v-else>NO DEVICE</span>
          </td>
        </template>
        <template #created_at="{item}">
          <td>{{ item.created_at | moment }}</td>
        </template>
        <template #actions="{item}">
          <td>
            <CButton color="dark" class="float-right" variant="outline" size="sm" @click="showDetails(item)">
            Details
          </CButton>
          </td>
        </template>
      </PaginatedTable>
      <CModal
        title="Fault details"
        size="lg"
        color="dark"
        centered
        :show.sync="details.dialog"
      >
        <json-tree :data="details.json"></json-tree>
      </CModal>
    </CCardBody>
  </CCard>
</template>

<script>
  import PaginatedTable from "../../components/PaginatedTable";
  export default {
    name: "Faults",
    components: {PaginatedTable},
    data() {
      return {
        action: 'faults/list',
        details: {
          dialog: false,
          json: '',
        },
        fields: [
          {
            label: 'ID',
            key: 'id',
          },
          {
            label: 'Device',
            key: 'device_id',
          },
          {
            label: 'Code',
            key: 'code',
          },
          {
            label: 'Message',
            key: 'message',
          },
          {
            label: 'At',
            key: 'created_at',
          },
          {
            label: '',
            key: 'actions',
            filter: false,
          }
        ]
      }
    },
    methods: {
      showDetails(fault) {
        this.details.json = fault.detail;
        this.details.dialog = true;
      },
    },
  }
</script>

<style scoped>

</style>
