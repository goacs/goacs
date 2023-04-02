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
          <div class="float-right">
            <CButton color="dark" variant="outline" size="sm" @click="downloadLogs(item)">
              Download
            </CButton>
            <CButton color="dark" variant="outline" size="sm" @click="showDetails(item)">
              Details
            </CButton>
          </div>
        </td>
      </template>
      <template #created_at="{item}">
        <td>{{ item.created_at | moment }}</td>
      </template>
    </PaginatedTable>
    <LogDetails v-model="details.dialog" :details="details"></LogDetails>
  </CModal>
</template>

<script>
import PaginatedTable from "../../../components/PaginatedTable";
import {mapGetters} from "vuex";
import LogDetails from "../../../components/LogDetails";
export default {
  name: "LogsModal",
  components: {LogDetails, PaginatedTable},
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
          label: 'Message',
          key: 'message',
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
    async downloadLogs(item) {
      try {
        const response = await this.$store.dispatch('device/downloadLogs', {
          device_id: this.$route.params.id,
          session_id: item.session_id,
        });
        window.open(response.data.data.url);
      } catch (e) {
        //alert
        console.log(e);
      }

    },
    async onModalClose(_, event, accept) {
      this.hide();
    },
    hide() {
      this.$emit('input', false);
    }
  },
  watch: {
    value(show) {
      if(show) {
        this.$refs.table.fetchItems();
      }
    }
  }
}
</script>

<style scoped>

</style>
