<template>
  <CCard>
    <CCardHeader>
      <strong>Lookuped Parameters</strong>
      <div  class="float-right">
        <LoadingButton
          size="sm"
          color="dark"
          class="shadow-sm"
          @click.native="downloadParameters"
          :loading="loading"
        >
          Download
        </LoadingButton>
      </div>
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

          <template #flag-filter="{item}">
            <FlagInput v-model="flagFilter" @input="propagateFlagFilter"></FlagInput>
          </template>
          <template #flag="{item}">
            <td>{{ parseFlag(item.flag) }}</td>
          </template>
          <template #details="{item}">
            <CCollapse :show="Boolean(item._toggled)" style="max-width: 100em">
              {{item.value}}
            </CCollapse>
          </template>
        </PaginatedTable>
    </CCardBody>
  </CCard>

</template>

<script>
  import PaginatedTable from "../../components/PaginatedTable";
  import {mapGetters} from "vuex";
  import {FlagParser} from "../../helpers/FlagParser";
  import Multiselect from "../../components/Multiselect";
  import FlagInput from "../../components/FlagInput";
  import LoadingButton from "../../components/LoadingButton";
  export default {
    name: "DeviceCachedParameterList",
    components: {LoadingButton, FlagInput, Multiselect, PaginatedTable },
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
            key: 'flag',
          },
        ],
        action: {
          name: 'device/fetchCachedParameters',
          parameters: {
            id: this.$route.params.id
          }
        },
        loading: false,
        flagFilter: {},
      }
    },
    computed: {
      ...mapGetters({
        device: 'device/getDevice',
      }),
    },
    methods: {
      propagateFlagFilter(data) {
        data = JSON.stringify(data);
        this.$refs.table.$refs.basetable.columnFilterEvent('flag', data, 'input');
        this.$refs.table.$refs.basetable.columnFilterEvent('flag', data, 'change');
      },
      parseFlag(flag) {
        const parser = new FlagParser(flag)
        return parser.toString()
      },

      stripString(prop, len) {
        return prop.value.substr(0, len);
      },
      async downloadParameters() {
        this.loading = true;
        try {
          const response = await this.$store.dispatch('device/downloadCachedParameters', {
            device_id: this.$route.params.id,
          });
          window.open(response.data.data.url);
        } catch (e) {
          //alert
        } finally {
          this.loading = false;
        }

      },
    },
    beforeDestroy() {

    },
  }
</script>

<style lang="scss">
.template-item {
  background-color: rgba(0,0,21,.05);
}
</style>
