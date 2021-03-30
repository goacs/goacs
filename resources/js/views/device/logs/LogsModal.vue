<template>
  <CModal
    title="View logs"
    color="dark"
    size="xl"
    centered
    :show="value"
    @update:show="onModalClose"
  >
    <PaginatedTable
      v-if="device.id"
      :action="action"
      :columnFilter='{ external: true, lazy: true }'
      :fields="fields"
      ref="table"
    >
      <template #actions="{item}">
        <td>
        <CButton color="dark" class="float-right" variant="outline" size="sm" @click="showDetails(item)">
          Details
        </CButton>
        </td>
      </template>
      <template #created_at="{item}">
        <td>{{ item.created_at | moment }}</td>
      </template>
    </PaginatedTable>
    <CModal
      title="Fault details"
      size="lg"
      color="dark"
      centered
      :show.sync="details.dialog"
    >
      <json-tree :data="details.json"></json-tree>

      <CTextarea
        class="mt-3"
        label="Data"
        size="xl"
        rows="20"
        v-model="details.xml"
      >
      </CTextarea>
    </CModal>
  </CModal>
</template>

<script>
import PaginatedTable from "../../../components/PaginatedTable";
import {mapGetters} from "vuex";
export default {
  name: "LogsModal",
  components: {PaginatedTable},
  props: {
    value: {
      type: Boolean,
    }
  },
  data() {
    return {
      details: {
        dialog: false,
        json: '',
        xml: '',
      },
      fields: [
        {
          label: 'From',
          key: 'from',
        },
        {
          label: 'Type',
          key: 'type',
        },
        {
          label: 'At',
          key: 'created_at',
        },
        {
          label: '',
          key: 'actions',
          filter: false,
        }
      ],
      action: {
        name: 'device/listLogs',
        parameters: {
          device_id: this.$route.params.id
        }
      },
    };
  },
  computed: {
    ...mapGetters({
      device: 'device/getDevice',
    }),
  },
  methods: {
    showDetails(log) {
      this.details.json = log.detail;
      this.details.xml = log.full_xml;
      this.details.dialog = true;
    },
    async onModalClose(_, event, accept) {
      this.hide();
    },
    hide() {
      this.$emit('input', false);
    }
  }
}
</script>

<style scoped>

</style>
