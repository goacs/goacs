<template>
    <div class="card">
        <header class="card-header">
            <p class="card-header-title">
                Files
            </p>
            <div class="card-header-icon" aria-label="more options">
                <b-button
                        size="is-small"
                        @click="uploadDialog = true"
                >
                    <b-icon
                            icon="upload"
                            size="is-small"
                    >

                    </b-icon>
                    Upload
                </b-button>
                <UploadFile v-model="uploadDialog" @uploaded="refreshList"></UploadFile>
            </div>
        </header>
        <div class="card-content">
        <PaginatedTable
                action="file/list"
                :autoload="false"
                :dense="true"
                ref="table"
                :headers="headers"
        >

            <b-table-column field="name" label="Name" searchable>
              <template
                  slot="searchable"
                  slot-scope="props">
                <b-input
                    v-model="props.filters[props.column.field]"
                    placeholder="Search..."
                    icon="magnify"
                    size="is-small" />
              </template>
              <template v-slot="props">
                {{ props.row.name }}
              </template>
            </b-table-column>

            <b-table-column field="type" label="Type" searchable>
              <template
                  slot="searchable"
                  slot-scope="props">
                <b-input
                    v-model="props.filters[props.column.field]"
                    placeholder="Search..."
                    icon="magnify"
                    size="is-small" />
              </template>
              <template v-slot="props">
                {{ props.row.type }}
              </template>
            </b-table-column>

            <b-table-column field="size" label="File size" v-slot="props">
                {{ props.row.size }} bytes
            </b-table-column>

            <b-table-column field="created_at" label="Upload time" v-slot="props">
                {{ props.row.created_at | moment }}
            </b-table-column>

            <b-table-column field="actions" label="Actions" v-slot="props">
              <section class="b-tooltips">
                <b-tooltip label="Download" type="is-dark">
                  <b-button tag="a" type="is-primary" size="is-small" @click="download(props.row)">
                      <b-icon icon="download" size="is-small"></b-icon>
                  </b-button>
                </b-tooltip>

                <b-tooltip label="Delete" type="is-dark">
                  <b-button type="is-primary" size='is-small' @click="deleteFile(props.row)">
                    <b-icon icon="delete" size="is-small"></b-icon>
                  </b-button>
                </b-tooltip>
              </section>
            </b-table-column>
        </PaginatedTable>
    </div>
    </div>
</template>

<script>
    import { saveAs } from 'file-saver';
    import PaginatedTable from "../../components/PaginatedTable";
    import UploadFile from "./UploadFile";
    export default {
        name: "FileList",
        components: {UploadFile, PaginatedTable},
        data() {
            return {
              headers: [
                {
                  text: 'Name',
                  value: 'name',
                  searchable: true,
                },
                {
                  text: 'Type',
                  value: 'type',
                  searchable: true,
                },
                {
                  text: 'Size',
                  value: 'size',
                  searchable: true,
                },
              ],
              uploadDialog: false,
            }
        },
        methods: {
          download(file) {
            const response = this.$store.dispatch('file/download', file.id)
            saveAs(response.data, file.name)
          },
          async deleteFile(file) {
            if(confirm(`Delete file: ${file.name}?`) === false) {
              return;
            }

            try {
              await this.$store.dispatch('file/delete', file.id)
              this.refreshList()
            } catch (e) {
              this.$buefy.toast.open({
                duration: 5000,
                message: `Cannot delete file: ${e.response.data.message}`,
                position: 'is-bottom',
                type: 'is-danger'
              })
            }

          },
          refreshList() {
              this.$refs.table.fetchItems()
          }
        }
    }
</script>

<style lang="scss" scoped>
.b-tooltips {
  .b-tooltip:not(:last-child) {
    margin-right: .5em
  }
}
</style>
