<template>
  <div class="columns">
    <div class="column">
      <div class="card">
        <header class="card-header">
          <p class="card-header-title">
            Templates
          </p>
          <div class="card-header-icon" aria-label="more options">
            <b-button
                    size="is-small"
                    @click="addDialog = true"
            >
              <b-icon
                      icon="plus"
                      size="is-small"
              >

              </b-icon>
              New
            </b-button>
          </div>
        </header>
        <div class="card-content">
          <PaginatedTable
                  action="template/list"
                  :autoload="false"
                  :dense="true"
                  ref="table"
          >
            <b-table-column field="id" label="ID" v-slot="props">
              {{ props.row.id }}
            </b-table-column>

            <b-table-column field="name" label="Name" v-slot="props">
              {{ props.row.name }}
            </b-table-column>

            <b-table-column field="parameter_count" label="Parameter count" v-slot="props">
              {{ props.row.parameters_count }}
            </b-table-column>

            <b-table-column field="actions" label="Actions" v-slot="props">
              <b-button tag="router-link" type="is-primary" :to="{ name: 'template-view', params: { id: props.row.id } }">
                <b-icon icon="magnify"></b-icon>
              </b-button>
            </b-table-column>
          </PaginatedTable>
        </div>
      </div>
    </div>
    <TemplateDialog is-new v-model="addDialog" @onSave="save"></TemplateDialog>
  </div>
</template>

<script>
  import PaginatedTable from "../../components/PaginatedTable";
  import TemplateDialog from "./TemplateDialog";
  export default {
    name: "TemplateList",
    components: {TemplateDialog, PaginatedTable},
    data() {
      return {
        addDialog: false
      };
    },
    methods: {
      save(template) {
        try {
          this.$store.dispatch('template/addTemplate', template)
          this.addDialog = false;
        } catch (e) {
          this.$buefy.toast.open({
            duration: 5000,
            message: `Error. Cannot add template: ${e.response.data.message}`,
            position: 'is-bottom',
            type: 'is-danger'
          })
        }
        this.$refs.table.fetchItems()
      }
    }
  }
</script>

<style scoped>

</style>
