<template>
  <b-modal
          v-model="value"
          has-modal-card
          :canCancel="false"
  >
    <form>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Assign template</p>

        </header>
        <section class="modal-card-body">
          <b-field label="Assign Template">
            <b-autocomplete
                    @typing="getItems"
                    field="name"
                    :data="filteredDataArray"
                    open-on-focus
                    @select="option => selectedItem = option">
              <template slot="empty">No results found</template>
            </b-autocomplete>
          </b-field>
          <b-field label="Priority" label-position="on-border">
            <b-input
                    type="text"
                    v-model.number="item.priority"
                    placeholder="Priority">
            </b-input>
          </b-field>
        </section>
        <footer class="modal-card-foot">
          <b-button @click="$emit('input', false)">Close</b-button>
          <b-button type="is-primary" class="is-align-content-end" @click="save" :loading="saving">Save</b-button>
        </footer>
      </div>
    </form>
  </b-modal>
</template>

<script>
  export default {
    name: "AddDialog",
    props: {
      value: {
        type: Boolean,
      },
    },
    data() {
      return {
        saving: false,
        deleting: false,
        name: '',
        selectedItem: {},
        item: {
          priority: 0,
          template_id: 0,
          device_id: this.$route.params.id,
        },
        page: 1,
        totalPages: 1,
        items: [],
        fetchingItems: false,
      }
    },
    methods: {
      async save() {
        this.saving = true;
        try {
         await this.$store.dispatch('device/assignTemplate', this.item)
         this.$store.dispatch('device/fetchDeviceTemplates', this.item.device_id)

        } catch (e) {
          this.$buefy.toast.open({
            type: 'is-danger',
            position: 'is-bottom',
            message: "Cannot assign template"
          })
        } finally {
          this.saving = false
          this.$emit('input', false)
        }
      },
      async getItems(name) {
        if (name && this.name !== name) {
          this.name = name
          this.items = []
          this.page = 1
          this.totalPages = 1
        }

        if (this.page > this.totalPages) {
          return
        }

        this.fetchingItems = true
        try {
          const response = await this.$store.dispatch('template/list', {
            page: this.page,
            perPage: 25,
          })
          this.page = response.data.page
          this.totalPages = response.data.total
          if(response.data.data !== null) {
            this.items.push(...response.data.data);
          }
        } catch (e) {
          this.$buefy.toast.open({
            type: 'is-danger',
            position: 'is-bottom',
            message: "Cannot fetch templates"
          })
        }
        finally {
          this.fetchingItems = false;
        }
      }
    },
    computed: {
      filteredDataArray() {
        return this.items.filter((option) => {
          return option.name
            .toString()
            .toLowerCase()
            .indexOf(this.name.toLowerCase()) >= 0
        })
      }
    },
    watch: {
      selectedItem: {
        handler(val) {
          this.item.template_id = val.id
        },
        deep: true
      }
    },
    mounted() {
      this.getItems()
    }
  }
</script>

<style scoped>

</style>
