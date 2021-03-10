<template>
  <div class="columns">
    <div class="column">
      <div class="card">
        <header class="card-header">
          <p class="card-header-title">
            Devices
          </p>
          <div class="card-header-icon" aria-label="more options">
            <b-button
                    size="is-small"
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
          action="device/list"
          :autoload="false"
          :dense="true"
          ref="table"
        >
          <b-table-column field="id" label="ID" searchable>
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
              {{ props.row.id }}
            </template>
          </b-table-column>

          <b-table-column field="serial_number" label="Serial Number" searchable>
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
              {{ props.row.serial_number }}
            </template>
          </b-table-column>

          <b-table-column field="software_version" label="Software Version" v-slot="props">
            {{ props.row.software_version }}
          </b-table-column>

          <b-table-column field="updated_at" label="Last connection time" v-slot="props">
            {{ props.row.updated_at | moment }}
          </b-table-column>

          <b-table-column field="actions" label="Actions" v-slot="props">
            <b-button
                    tag="router-link" :to="{ name: 'devices-view', params: { id: props.row.id } }"
                    size="is-small"
            >
              <b-icon
                      icon="magnify"
                      size="is-small"
              >
              </b-icon>
            </b-button>
          </b-table-column>
        </PaginatedTable>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import PaginatedTable from "../../components/PaginatedTable";
export default {
  name: "DeviceList",
  components: {PaginatedTable},
  data() {
    return {

    };
  },
  methods: {
  },
  mounted() {
  }

}
</script>

<style scoped>

</style>
