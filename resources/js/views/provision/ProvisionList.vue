<template>
  <CCard>
    <CCardHeader>
      <strong>Provisions list</strong>
      <CButton color="dark" class="float-right" variant="outline" size="sm" @click="$router.push({name: 'provision-crete'})">
        <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon>New
      </CButton>
    </CCardHeader>
    <CCardBody>
      <PaginatedTable
        :fields="fields"
        :columnFilter='{ external: true, lazy: true }'
        action="provision/list"
        :autoload="false"
        :dense="true"
        ref="table"
      >
        <template #updated_at="{ item }">
          <td>{{ item.updated_at | moment }}</td>
        </template>
        <template #actions="{ item }">
          <td>
            <CButton color="dark" class="float-right" variant="outline" size="sm" :to="{ name: 'template-view', params: { id: item.id}}">
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
    name: "ProvisionList",
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
      async save() {
        try {
          await this.$store.dispatch('template/addTemplate', this.template)
        } catch (e) {

        } finally {
          this.dialog = false;
          this.$refs.table.fetchItems()
        }
      }
    }
  }
</script>

<style scoped>

</style>
