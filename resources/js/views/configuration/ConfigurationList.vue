<template>
  <CCard>
    <CCardHeader>
      <strong>Configuration list</strong>
      <CButton color="dark" class="float-right" variant="outline" size="sm" @click="$router.push({name: 'configuration-create'})">
        <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon>New
      </CButton>
    </CCardHeader>
    <CCardBody>
      <PaginatedTable
        :fields="fields"
        :columnFilter='{ external: true, lazy: true }'
        action="configuration/list"
        :autoload="false"
        :dense="true"
        ref="table"
      >
        <template #updated_at="{ item }">
          <td>{{ item.updated_at | moment }}</td>
        </template>
        <template #actions="{ item }">
          <td>
            <CButton color="dark" class="float-right" variant="outline" size="sm" :to="{ name: 'configuration-edit', params: { id: item.id}}">
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
    name: "ConfigurationList",
    components: {PaginatedTable},
    data() {
      return {
        dialog: false,
        saving: false,
        template: {
          name: ''
        },
        fields: [
          {
            key: 'id',
            label: 'ID',
          },
          {
            key: 'name',
            label: 'Name'
          },
          {
            key: 'updated_at',
            label: 'Updated at'
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
      onModalClose(_, event, accept) {
        if(accept) {
          this.save();
          return;
        }

        this.dialog = false;
      },
    }
  }
</script>

<style scoped>

</style>
