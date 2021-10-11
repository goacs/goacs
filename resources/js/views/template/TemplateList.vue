<template>
  <CCard>
    <CCardHeader>
      <strong>Template list</strong>
      <CButton color="dark" class="float-right" variant="outline" size="sm" @click="dialog = true">
        <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon>Add
      </CButton>
    </CCardHeader>
    <CCardBody>
      <PaginatedTable
        :fields="fields"
        :columnFilter='{ external: true, lazy: true }'
        action="template/list"
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
    <CModal
      title="Add template"
      color="dark"
      centered
      :show="dialog"
      @update:show="onModalClose"
    >
      <CInput
        label="Name"
        v-model="template.name"
      >
      </CInput>
      <CElementCover v-if="saving" :opacity="0.8"/>
    </CModal>
  </CCard>
</template>

<script>
  import PaginatedTable from "../../components/PaginatedTable";
  import TemplateDialog from "./TemplateDialog";
  export default {
    name: "TemplateList",
    components: {TemplateDialog, PaginatedTable},
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
            key: 'parameters_count',
            label: 'Parameters count',
            filter: false,
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
