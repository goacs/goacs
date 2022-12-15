<template>
  <CCard>
    <CCardHeader>
      <strong>Parameters</strong>
      <div  class="float-right">
        <LoadingButton
          size="sm"
          color="dark"
          class="shadow-sm"
          @click.native="lookupParameters"
          :loading="lookupLoading"
        >
          Lookup parameters
        </LoadingButton>
      <CButton
        v-if="hasCachedParams"
        size="sm"
        color="dark"
        class="shadow-sm"
        @click="cached"
      >
        View lookuped parameters
      </CButton>
      <CButton color="dark" variant="outline" size="sm" @click="addItem">
        <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon> Add
      </CButton>
      </div>
    </CCardHeader>
    <CCardBody>
        <PaginatedTable
                :action="action"
                :columnFilter='{ external: true, lazy: true }'
                :fields="fields"
                ref="table"
                @items:loaded="toggleLookup"

        >
          <template #value="{ item, index }">
            <td>
              <template v-if="item.value.length > 50">
                {{ stripString(item.value, 50) }}
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

          <template #cached="{ item, index }">
            <td>
              <template v-if="item.cached.length > 50">
                {{ stripString(item.cached, 50) }}
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
            <FlagInput v-model="flagFilter" @input="propagateFlagFilter"></FlagInput>
          </template>

          <template #actions="{item}">
            <td v-if="item.source === 'device'">
              <CButton
                v-if="item.flags.object === true"
                size="sm"
                color="dark"
                variant="ghost"
              >
                <CIcon name="cil-plus"/>
              </CButton>
              <CButton
                v-if="item.flags.write === true"
                size="sm"
                color="dark"
                variant="ghost"
                @click="editItem(item)"
              >
                <CIcon name="cil-pencil"/>
              </CButton>
            </td>
            <td v-else>
              <CButton type="button" color="primary" size="sm" class="mr-2" :to="{name: 'template-view', params: {id: item.device_id}}">
                {{ templateName(item.device_id) }}
                <CBadge color="light" class="ml-2 position-static">{{ templatePriority(item.device_id) }}</CBadge>
              </CButton>
            </td>
          </template>
          <template #details="{item}">
            <CCollapse :show="Boolean(item._toggled)" style="max-width: 100em">
              <h5>Value: </h5>{{ item.value }}
              <div v-if="item.cached"><h5 class="mt-3">Readed:</h5> {{ item.cached }}</div>
            </CCollapse>
          </template>
        </PaginatedTable>
    </CCardBody>
    <ParameterDialog
      v-model="addDialog"
      :item="addingItem"
      @onSave="storeParameter"
      :saving.sync="addSaving"
      :errors="dialogErrors"
    ></ParameterDialog>
    <ParameterDialog
      v-model="editDialog"
      :item="editedItem"
      :isNew="false"
      @onSave="updateParameter"
      @onDelete="deleteParameter"
      :saving.sync="editSaving"
      :errors="dialogErrors"
    ></ParameterDialog>
  </CCard>

</template>

<script>
  import PaginatedTable from "../../components/PaginatedTable";
  import {mapGetters} from "vuex";
  import {FlagParser} from "../../helpers/FlagParser";
  import ParameterDialog from "../../components/ParameterDialog";
  import Multiselect from "../../components/Multiselect";
  import FlagInput from "../../components/FlagInput";
  import LoadingButton from "../../components/LoadingButton";
  export default {
    name: "DeviceParameterList",
    components: {LoadingButton, FlagInput, Multiselect, ParameterDialog, PaginatedTable },
    data() {
      return {
        lookup: false,
        addSaving: false,
        editSaving: false,
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
        action: {
          name: 'device/fetchParameters',
          parameters: {
            id: this.$route.params.id
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
          name: "",
          value: "",
          flags: {},
        },
        editedIndex: -1,
        saving: false,
        flagFilter: {},
        lookupLoading: false,
      }
    },
    computed: {
      ...mapGetters({
        device: 'device/getDevice',
        parameters: 'device/getParameters',
        templates: 'device/getDeviceTemplates',
        dialogErrors: 'dialog/getDeviceParametersErrors',
        hasCachedParams: 'device/hasCachedParams',

      }),
    },
    methods: {
      propagateFlagFilter(data) {
        data = JSON.stringify(data);
        this.$refs.table.$refs.basetable.columnFilterEvent('flags', data, 'input');
        this.$refs.table.$refs.basetable.columnFilterEvent('flags', data, 'change');
      },
      setColorOnDiff(row) {
        if(!row['cached']) {
          return '';
        }

        if(row.value === row.cached) {
          return '';
        }
        return 'is-info'
      },
      toggleLookup() {
         this.lookup = this.$refs.table.items.filter(function(item) {
          // eslint-disable-next-line no-prototype-builtins
          return item.hasOwnProperty('cached')
        }).length > 0

        const index = this.getLookupColumnIndex();

        if(this.lookup === true && index === -1) {
          this.fields.splice(3,0, {
            label: 'Readed',
            key: 'cached',
            filter: false,

          });
        } else if(this.lookup === false && index !== -1) {
            this.fields.splice(index, 1);
        }
      },
      getLookupColumnIndex() {
        return this.fields.findIndex(field => field.key === 'cached');
      },
      parseFlag(flag) {
        const parser = new FlagParser(flag)
        return parser.toString()
      },
      saveParameter(params) {
        console.log(params)
      },
      addObject(item) {
        this.$store.dispatch('device/addObject', {
          id: this.device.id,
          name: item.name,
          key: '',
        })
      },
      addItem() {
        this.$store.commit('dialog/setDeviceParametersErrors', {})
        this.addDialog = true;
      },
      editItem(item) {
        this.editedIndex = this.$refs.table.items
        this.editedItem = item
        this.$store.commit('dialog/setDeviceParametersErrors', {})
        this.editDialog = true
      },
      async storeParameter(item) {
        try {
          await this.$store.dispatch('device/storeParameter', {
            device_id: this.device.id,
            ...item
          })
          this.addDialog = false
          await this.$refs.table.fetchItems()
        } catch (e) {
          this.$store.commit('dialog/setDeviceParametersErrors', this.extractErrorsFromResponse(e.response));
        } finally {
          this.addSaving = false;
        }

        this.addingItem = {
          name: "",
          value: "",
          flags: {},
        }
      },
      async updateParameter(item) {
        try {
          await this.$store.dispatch('device/updateParameters', {
            device_id: this.device.id,
            ...item
          })
          this.editDialog = false
          await this.$refs.table.fetchItems()
        } catch (e) {
          this.$store.commit('dialog/setDeviceParametersErrors', this.extractErrorsFromResponse(e.response));
        } finally {
          this.editSaving = false;
        }
      },
      async deleteParameter(item) {
        try {
          await this.$store.dispatch('device/deleteParameter', {
            device_id: this.device.id,
            ...item
          })
          this.editDialog = false
          await this.$refs.table.fetchItems()
        } catch (e) {
          this.$store.commit('dialog/setDeviceParametersErrors', this.extractErrorsFromResponse(e.response));
        } finally {
          this.editSaving = false;
        }
      },
      async lookupParameters() {
        try {
          this.lookupLoading = true;
          await this.$store.dispatch('device/lookup', this.device.id)
        } catch (e) {

        } finally {
          this.lookupLoading = false;
        }
      },
      cached() {
        const route = this.$router.resolve({name: 'devices-cached-params', params: {id: this.device.id }});
        window.open(route.href, '_blank');
        // this.$store.dispatch('device/fetchCachedParameters', this.device.id);
        // this.cachedParamsDialog = true;
      },
      stripString(prop, len) {
        return prop.substr(0, len);
      },
      templateName(template_id) {
        const template = _.find(this.templates, {id: template_id});
        return template.name;
      },
      templatePriority(template_id) {
        const template = _.find(this.templates, {id: template_id});
        return template.pivot.priority;
      }
    },
    beforeDestroy() {
      this.$store.commit('device/setParameters', {})
    },
  }
</script>

<style lang="scss">
.template-item {
  background-color: rgba(0,0,21,.05);
}
</style>
