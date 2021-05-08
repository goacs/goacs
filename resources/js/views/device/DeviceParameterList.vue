<template>
  <CCard>
    <CCardHeader>
      <strong>Parameters</strong>
      <CButton color="dark" class="float-right" variant="outline" size="sm" @click="addDialog = true">
        <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon> Add
      </CButton>
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
    <ParameterDialog v-model="addDialog" :item="addingItem" @onSave="storeParameter"></ParameterDialog>
    <ParameterDialog v-model="editDialog" :item="editedItem" :isNew="false" @onSave="updateParameter" @onDelete="deleteParameter"></ParameterDialog>
  </CCard>

</template>

<script>
  import PaginatedTable from "../../components/PaginatedTable";
  import {mapGetters} from "vuex";
  import {FlagParser} from "../../helpers/FlagParser";
  import ParameterDialog from "../../components/ParameterDialog";
  import Multiselect from "../../components/Multiselect";
  import FlagInput from "../../components/FlagInput";
  export default {
    name: "DeviceParameterList",
    components: {FlagInput, Multiselect, ParameterDialog, PaginatedTable },
    data() {
      return {
        lookup: false,
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
      }
    },
    computed: {
      ...mapGetters({
        device: 'device/getDevice',
        parameters: 'device/getParameters',
        templates: 'device/getDeviceTemplates',
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
      editItem(item) {
        this.editedIndex = this.$refs.table.items
        this.editedItem = item
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
          console.log(e)

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
          console.log(e)

        }
      },
      stripString(prop, len) {
        return prop.value.substr(0, len);
      },
      templateName(template_id) {
        const template =  _.find(this.templates, {id: template_id});
        return template.name
      }
    },
    beforeDestroy() {
      this.$store.commit('device/setParameters', {})
    },
  }
</script>

<style lang="scss">

</style>
