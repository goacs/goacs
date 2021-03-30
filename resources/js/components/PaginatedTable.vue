<template>
  <div>
    <CDataTable
            :items="items"
            :loading="loading"
            :itemsPerPageSelect="itemsPerPageOptions"
            :items-per-page="25"
            @pagination-change="paginationChanged"
            @update:column-filter-value="filterChanged"
            v-bind="Object.assign($attrs, $props)"
            ref="basetable"
    >
      <slot></slot>
      <template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
        <slot :name="slot" v-bind="scope"/>
      </template>
    </CDataTable>
    <CPagination :active-page="pagination.activePage" :pages="pagination.pages" @update:activePage="pageChanged"></CPagination>
  </div>
</template>

<script>
export default {
  name: "PaginatedTable",
  props: {
    dense: {
      default: () => false
    },
    action: {
      type: [String, Object],
      required: true,
    },
    autoload: {
      type: Boolean,
      default: () => true,
    },
  },
  data() {
    return {
      items: [],
      filter: {},
      meta: {},
      collapseDuration: 0,
      itemsPerPageOptions: { values: [25, 50, 100, 300]},
      pagination: {
        itemsPerPage: 25,
        activePage: 1,
        pages: 0,
      },
      loading: false,
    };
  },
  methods: {
    paginationChanged(number) {
      this.pagination.itemsPerPage = number;
      this.fetchItems();
    },
    pageChanged(pageNumber) {
      this.pagination.activePage = pageNumber;
      this.fetchItems();
    },
    filterChanged(filter) {
      this.filter = filter;
      this.fetchItems();
    },
    getMeta() {
      return this.meta
    },
    toggleDetails(item, index) {
      this.$set(this.items[index], '_toggled', !item._toggled)
      this.collapseDuration = 300
      this.$nextTick(() => { this.collapseDuration = 0})
    },
    async fetchItems() {
      try {
        this.loading = true;
        const response = await this.$store.dispatch(this.actionData.name, this.actionData.parameters)
        this.items = response.data.data ?? []
        this.pagination.pages = response.data.last_page ?? 0
        // eslint-disable-next-line no-unused-vars
        const {data, ...meta} = response.data
        this.meta = meta;
        this.$emit('items:loaded', true)
      } catch (e) {
        console.error("Cannot load table items")
      } finally {
        this.loading = false;
      }
    }
  },
  computed: {
    actionData() {
      if(typeof this.action === 'string') {
        return {
          name: this.action,
          parameters: {
            page: this.pagination.activePage,
            perPage: this.pagination.itemsPerPage,
            filter: this.filter
          }
        }
      }
      return {
        name: this.action.name,
        parameters: {
          ...this.action.parameters,
          page: this.pagination.activePage,
          perPage: this.pagination.itemsPerPage,
          filter: this.filter
        }
      }
    }
  },
  // watch: {
  //   options: {
  //     deep: true,
  //     handler() {
  //       this.fetchItems()
  //     }
  //   },
  // },
  mounted() {
    this.fetchItems()
  }
}
</script>

<style scoped>

</style>
