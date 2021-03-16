<template>
  <CCard>
    <CCardHeader>Files
      <CButton color="dark" class="float-right" variant="outline" size="sm" @click="uploadDialog = true">
        <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon> Upload
      </CButton>
    </CCardHeader>
    <CCardBody>
      <PaginatedTable
                action="file/list"
                :autoload="false"
                ref="table"
                :fields="fields"
        >


        <template #size="{item}">
            <td>{{ item.size }} bytes</td>
        </template>

        <template #created_at="{item}">
          <td>{{ item.created_at | moment }}</td>
        </template>

        <template #actions="{item}">
          <td>
            <CButton
              size="sm"
              color="primary"
              variant="ghost"
              @click="download(item)"
            >
              <CIcon name="cil-cloud-download"/>
            </CButton>
            <CButton
              size="sm"
              color="primary"
              variant="ghost"
              @click="deleteFile(item)"
            >
              <CIcon name="cil-trash"/>
            </CButton>
          </td>
        </template>
        </PaginatedTable>
    </CCardBody>
    <UploadFile v-model="uploadDialog" @uploaded="refreshList"></UploadFile>
  </CCard>
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
              fields: [
                {
                  label: 'Name',
                  key: 'name',
                },
                {
                  label: 'Type',
                  key: 'type',
                },
                {
                  label: 'Size',
                  key: 'size',
                },
                {
                  label: 'Upload date',
                  key: 'created_at',
                },
                {
                  label: '',
                  key: 'actions',
                  filter: false,
                }
              ],
              uploadDialog: false,
            }
        },
        methods: {
          async download(file) {
            const response = await this.$store.dispatch('file/download', file.id);
            saveAs(response.data, file.name);
          },
          async deleteFile(file) {
            if(confirm(`Delete file: ${file.name}?`) === false) {
              return;
            }

            try {
              await this.$store.dispatch('file/delete', file.id);
              this.refreshList();
            } catch (e) {

            }

          },
          refreshList() {
              this.$refs.table.fetchItems();
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
