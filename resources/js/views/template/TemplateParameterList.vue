<template>
  <CCard>
    <CCardHeader>
      <strong>Parameters</strong>
      <CButton color="dark" class="float-right" variant="outline" size="sm" @click="addItem">
        <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon>Add
      </CButton>
    </CCardHeader>
    <CCardBody>
      <PaginatedTable
        :action="action"
        :columnFilter='{ external: true, lazy: true }'
        :fields="fields"
        ref="table"

      >
        <template #value="{ item, index }">
          <td>
            <template v-if="item.value.length > 50">
              {{ stripString(item, 50) }}
              <CButton
                @click="$refs.table.toggleDetails(item, index)"
                size="sm"
                color="primary"
              >
                {{Boolean(item._toggled) ? 'Collapse' : 'Expand'}}
              </CButton>
            </template>
            <template v-else>
              {{ item.value }}
            </template>
          </td>
        </template>

        <template #flags="{item}">
          <td>{{ parseFlag(item.flags) }}</td>
        </template>
        <template #flags-filter="{item}">
          <FlagInput class='slim-select' v-model="flagFilter" @input="propagateFlagFilter"></FlagInput>
        </template>

        <template #actions="{item}">
          <td>
            <CButton
              size="sm"
              color="primary"
              variant="ghost"
              @click="editItem(item)"
            >
              <CIcon name="cil-pencil"/>
            </CButton>
          </td>
        </template>
        <template #details="{item}">
          <CCollapse :show="Boolean(item._toggled)" style="max-width: 100em">
            {{item.value}}
          </CCollapse>
        </template>
      </PaginatedTable>
    </CCardBody>
    <ParameterDialog
      v-model="addDialog"
      :item="addingItem"
      is-new
      @onSave="storeParameter"
      :saving="addSaving"
      :errors="errors"
    ></ParameterDialog>
    <ParameterDialog
      v-model="editDialog"
      :item="editedItem"
      @onSave="updateParameter"
      @onDelete="deleteParameter"
      :saving="editSaving"
      :errors="errors"
    ></ParameterDialog>
  </CCard>
</template>

<script>
  import PaginatedTable from "../../components/PaginatedTable";
  import {mapGetters} from "vuex";
  import {FlagParser} from "../../helpers/FlagParser";
  import ParameterDialog from "../../components/ParameterDialog";
  import FlagInput from "../../components/FlagInput";
  export default {
    name: "TemplateParameterList",
    components: {FlagInput, ParameterDialog, PaginatedTable },
    data() {
      return {
        addSaving: false,
        editSaving: false,
        headers: [
          {
            text: 'Name',
            value: 'name',
            searchable: true,
          },
          {
            text: 'Type',
            value: 'type',
          },
          {
            text: 'Value',
            value: 'value'
          },
          {
            text: 'Flag',
            value: 'flag',
          },
          {
            text: 'Actions',
            value: 'actions'
          }
        ],
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
            label: 'Value',
            key: 'value',
          },
          {
            label: 'Flags',
            key: 'flags',
          },
          {
            label: '',
            key: 'actions',
            filter: false,
          }
        ],
        flagFilter: {},
        flagSelection: [
          {
            value: 'r',
            text: 'Read',
          },
          {
            value: 'w',
            text: 'Write',
          },
          {
            value: 'a',
            text: 'AddObject',
          }
        ],
        action: {
          name: 'template/listParameters',
          parameters: {
            templateId: this.$route.params.id
          }
        },
        addDialog: false,
        addingItem: {
          name: "",
          value: "",
          flags: {},
        },
        editDialog: false,
        editedItem: {
          flags: {},
        },
        editedIndex: -1,
        saving: false,
      }
    },
    computed: {
      ...mapGetters({
        template: 'template/getTemplate',
        parameters: 'template/getParameters',
        errors: 'dialog/getTemplateParametersErrors',
      }),
    },
    methods: {
      propagateFlagFilter(data) {
        data = JSON.stringify(data);
        this.$refs.table.$refs.basetable.columnFilterEvent('flags', data, 'input')
        this.$refs.table.$refs.basetable.columnFilterEvent('flags', data, 'change')
      },
      parseFlag(flag) {
        const parser = new FlagParser(flag);
        return parser.toString();
      },
      addItem() {
        this.addDialog = true;
        this.addingItem.templateId = this.$route.params.id;
        this.$store.commit('dialog/setTemplateParametersErrors', {})
      },
      editItem(item) {
        this.editedItem = item;
        this.editedItem.templateId = this.$route.params.id;
        this.$store.commit('dialog/setTemplateParametersErrors', {})
        this.editDialog = true;
      },
      async storeParameter(savedItem) {
        try {
          await this.$store.dispatch('template/storeParameter', savedItem);
          this.addDialog = false;
          await this.$refs.table.fetchItems();
        } catch (e) {
          this.$store.commit('dialog/setTemplateParametersErrors', this.extractErrorsFromResponse(e.response));
        } finally {
          this.addSaving = false;
        }
      },
      async updateParameter(savedItem) {
        console.log(savedItem)

        try {
          await this.$store.dispatch('template/updateParameter', savedItem);
          this.editDialog = false;
          await this.$refs.table.fetchItems();
        } catch (e) {
          this.$store.commit('dialog/setTemplateParametersErrors', this.extractErrorsFromResponse(e.response));
        } finally {
          this.editSaving = false;
        }
      },
      async deleteParameter(savedItem) {
        try {
          await this.$store.dispatch('template/deleteParameter', {
            id: savedItem.id,
            templateId: savedItem.template_id,
          })
          this.editDialog = false
          await this.$refs.table.fetchItems()
        } catch (e) {
          this.$store.commit('dialog/setTemplateParametersErrors', this.extractErrorsFromResponse(e.response));
        } finally {
          this.editSaving = false;
        }
      },
      stripString(prop, len) {
        return prop.value.substr(0, len);
      },
    },
    beforeDestroy() {
      this.$store.commit('template/setParameters', {})
    },
  }
</script>

<style lang="scss" scoped>
  .b-tooltips {
    .b-tooltip:not(:last-child) {
      margin-right: .5em
    }

  }
</style>
