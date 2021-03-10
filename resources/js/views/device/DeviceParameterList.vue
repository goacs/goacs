<template>
  <div class="card">
    <header class="card-header">
      <p class="card-header-title">
        Parameters
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
          Add
        </b-button>

      </div>
    </header>
    <div class="card-content">
      <div>
        <PaginatedTable
                :action="action"
                :headers="headers"
                :autoload="false"
                :dense="true"
                detailed
                detail-key="name"
                :show-detail-icon="false"
                ref="table"
                :row-class="setColorOnDiff"
                @items:loaded="toggleLookup"

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

          <b-table-column field="value" label="Value" searchable>
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
              <template v-if="props.row.value.length > 50">
                {{ stripString(props, 50) }}
                <b-button
                @click="$refs.table.$refs.basetable.toggleDetails(props.row)"
                size="is-small"
                type="is-primary"
                >
                  ...
                </b-button>
              </template>
              <template v-else>
                {{ props.row.value }}
              </template>
            </template>
          </b-table-column>

          <b-table-column field="flag" label="Flag">

            <template v-slot="props">
              {{ parseFlag(props.row.flags) }}
            </template>
          </b-table-column>

          <b-table-column field="actions" label="Actions" v-slot="props">
            <section class="b-tooltips">
              <b-tooltip label="New instance" type="is-dark" v-if="props.row.flags.object === true">
                <b-button type="is-primary" size='is-small' @click="addObject(props.row)">
                  <b-icon icon="plus" size="is-small"></b-icon>
                </b-button>
              </b-tooltip>
              <b-tooltip label="Edit parameter" type="is-dark">
              <b-button type="is-primary" size='is-small' @click="editItem(props.row)">
                <b-icon icon="pencil" size="is-small"></b-icon>
              </b-button>
              </b-tooltip>
            </section>
          </b-table-column>
          <b-table-column field="lookup" label="Lookup" v-if="lookup">

            <template v-slot="props">
              <template v-if="props.row.cached && props.row.cached.length > 50">
                {{ stripString(props, 50) }}
                <b-button
                    @click="$refs.table.$refs.basetable.toggleDetails(props.row)"
                    size="is-small"
                    type="is-primary"
                >
                  ...
                </b-button>
              </template>
              <template v-else-if="props.row.cached">
                {{ props.row.cached }}
              </template>
            </template>
          </b-table-column>
          <template slot="detail"  slot-scope="props">
            <article class="media">
              <div class="media-content">
                <div class="content">
                  <pre class="parameter">{{ props.row.value }}</pre>
                </div>
              </div>
            </article>
          </template>
        </PaginatedTable>
      </div>
    </div>
    <ParameterDialog v-model="addDialog" :item="addingItem" @onSave="storeParameter"></ParameterDialog>
    <ParameterDialog v-model="editDialog" :item="editedItem" :isNew="false" @onSave="updateParameter" @onDelete="deleteParameter"></ParameterDialog>
  </div>
</template>

<script>
  import PaginatedTable from "../../components/PaginatedTable";
  import {mapGetters} from "vuex";
  import {FlagParser} from "../../helpers/FlagParser";
  import ParameterDialog from "../../components/ParameterDialog";
  export default {
    name: "DeviceParameterList",
    components: {ParameterDialog, PaginatedTable },
    data() {
      return {
        lookup: false,
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
            text: 'Object',
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
          valuestruct: {
            value: ""
          },
          flags: "",
        },
        editDialog: false,
        editedItem: {
          valuestruct: {
            value: ""
          },
        },
        editedIndex: -1,
        saving: false,
      }
    },
    computed: {
      ...mapGetters({
        device: 'device/getDevice',
        parameters: 'device/getParameters'
      }),
    },
    methods: {
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
          this.$buefy.toast.open({
            duration: 5000,
            message: `Cannot save parameter: ${e.response.data.message}`,
            position: 'is-bottom',
            type: 'is-danger'
          })
        }
        this.addingItem = {
          name: "",
          value: "",
          flag: "",
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
          this.$buefy.toast.open({
            duration: 5000,
            message: `Cannot save parameter: ${e.response.data.message}`,
            position: 'is-bottom',
            type: 'is-danger'
          })
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
          this.$buefy.toast.open({
            duration: 5000,
            message: `Cannot delete parameter: ${e.response.data.message}`,
            position: 'is-bottom',
            type: 'is-danger'
          })
        }
      },
      stripString(prop, len) {
        return prop.row.value.substr(0, len);
      },
    },
    beforeDestroy() {
      this.$store.commit('device/setParameters', {})
    },
  }
</script>

<style lang="scss" >

  .is-info {
    background: #f5f5f5;
  }

  .b-tooltips {
    .b-tooltip:not(:last-child) {
      margin-right: .5em
    }
  }

  pre.parameter {
    white-space: pre-wrap;       /* Since CSS 2.1 */
    white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
    white-space: -pre-wrap;      /* Opera 4-6 */
    white-space: -o-pre-wrap;    /* Opera 7 */
    word-wrap: break-word;       /* Internet Explorer 5.5+ */
  }
</style>
