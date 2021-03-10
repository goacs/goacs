<template>
  <div>
    <b-table
            :data="items"
            :loading="loading"
            paginated
            :total="total"
            :per-page="options.itemsPerPage"
            :current-page="options.page"
            @page-change="pageChanged"
            @filters-change="filterChanged"
            aria-next-label="Next page"
            aria-previous-label="Previous page"
            aria-page-label="Page"
            aria-current-label="Current page"
            v-bind="Object.assign($attrs, $props)"
            backend-pagination
            backend-filtering
            backend-sorting
            :debounce-search="500"
            ref="basetable"
    >
      <slot></slot>
      <template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope"><slot :name="slot" v-bind="scope"/></template>

    </b-table>
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
    }
  },
  data() {
    return {
      items: [],
      filter: {},
      total: 0,
      meta: {},
      footerOptions: {itemsPerPageOptions: [25, 50, 100, 300]},
      options: {
        itemsPerPage: 25,
        page: 1,
      },
      loading: false,
    };
  },
  methods: {
    pageChanged(pageNumber) {
      this.options.page = pageNumber
      this.fetchItems();
    },
    filterChanged(filter) {
      this.filter = filter;
      this.fetchItems();
    },
    getMeta() {
      return this.meta
    },
    async fetchItems() {
      try {
        this.loading = true;
        const response = await this.$store.dispatch(this.actionData.name, this.actionData.parameters)
        this.items = response.data.data ?? []
        this.total = response.data.total ?? 0
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
            page: this.options.page,
            perPage: this.options.itemsPerPage,
            filter: this.filter
          }
        }
      }
      return {
        name: this.action.name,
        parameters: {
          ...this.action.parameters,
          page: this.options.page,
          perPage: this.options.itemsPerPage,
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
