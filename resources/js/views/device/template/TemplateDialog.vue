<template>
  <CModal
    title="Assign template"
    color="dark"
    centered
    :show="value"
    @update:show="onModalClose"
  >
    <label>Select template</label>

    <v-select
      v-model="item.template_id"
      :options="items"
      label="name"
      :reduce="item => item.id"
      class="mb-2"
    ></v-select>

    <CInput
      label="Priority"
      v-model="item.priority"
    >
    </CInput>
  </CModal>
<!--  <b-modal-->
<!--          v-model="value"-->
<!--          has-modal-card-->
<!--          :canCancel="false"-->
<!--  >-->
<!--    <form>-->
<!--      <div class="modal-card">-->
<!--        <header class="modal-card-head">-->
<!--          <p class="modal-card-title">Assign template</p>-->

<!--        </header>-->
<!--        <section class="modal-card-body">-->
<!--          <b-field label="Assign Template">-->
<!--            <b-autocomplete-->
<!--                    @typing="getItems"-->
<!--                    field="name"-->
<!--                    :data="filteredDataArray"-->
<!--                    open-on-focus-->
<!--                    @select="option => selectedItem = option">-->
<!--              <template slot="empty">No results found</template>-->
<!--            </b-autocomplete>-->
<!--          </b-field>-->
<!--          <b-field label="Priority" label-position="on-border">-->
<!--            <b-input-->
<!--                    type="text"-->
<!--                    v-model.number="item.priority"-->
<!--                    placeholder="Priority">-->
<!--            </b-input>-->
<!--          </b-field>-->
<!--        </section>-->
<!--        <footer class="modal-card-foot">-->
<!--          <b-button @click="$emit('input', false)">Close</b-button>-->
<!--          <b-button type="is-primary" class="is-align-content-end" @click="save" :loading="saving">Save</b-button>-->
<!--        </footer>-->
<!--      </div>-->
<!--    </form>-->
<!--  </b-modal>-->
</template>

<script>
  export default {
    name: "TemplateDialog",
    props: {
      value: {
        type: Boolean,
      }
    },
    data() {
      return {
        page: 1,
        totalPages: 1,
        items: [],
        item: {
          priority: 0,
          template_id: 0,
          device_id: this.$route.params.id,
        }
      }
    },
    methods: {
      async save() {
        this.saving = true;
        try {
         await this.$store.dispatch('device/assignTemplate', this.item)
         await this.$store.dispatch('device/fetchDeviceTemplates', this.item.device_id)
        } catch (e) {

        } finally {
          this.saving = false
          this.$emit('input', false)
        }
      },
      async getItems() {
        this.page = 1
        this.totalPages = 1

        if (this.page > this.totalPages) {
          return
        }

        this.fetchingItems = true
        try {
          const response = await this.$store.dispatch('template/list', {
            page: this.page,
            perPage: this.limit,
          })
          this.page = response.data.current_page
          this.totalPages = response.data.last_page
          if(response.data.data !== null) {
            this.items.push(...response.data.data);
          }
        } catch (e) {

        }
        finally {
          this.fetchingItems = false;
        }
      },
      onModalClose() {
        this.$emit('input', false);
      },
      setItem(item) {
        console.log(item);
        this.item = item;
      }
    },
    mounted() {
      this.getItems();
    }
  }
</script>

<style scoped>

</style>
