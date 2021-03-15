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
    <template #footer>
      <CButton @click="deleteItem()" color="danger" v-if="item.template_id">Delete</CButton>
      <CButton @click="hide()" color="secondary">Cancel</CButton>
      <CButton @click="save()" color="dark">OK</CButton>
    </template>
    <CElementCover v-if="saving" :opacity="0.8"/>

  </CModal>
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
        saving: false,
        page: 1,
        totalPages: 1,
        items: [],
        item: {
          priority: 0,
          template_id: 0,
          device_id: this.$route.params.id,
        },
        limit: 100
      }
    },
    methods: {
      async save() {
        this.saving = true;
        try {
          this.item.device_id = this.$route.params.id;
         await this.$store.dispatch('device/assignTemplate', this.item)
         await this.$store.dispatch('device/fetchDeviceTemplates', this.item.device_id)
        } catch (e) {

        } finally {
          this.saving = false
          this.hide();
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
            filter: {}
          });
          this.page = response.data.current_page
          this.totalPages = response.data.last_page
          if(response.data.data !== null) {
            this.items.push(...response.data.data);
          }
        } catch (e) {
          console.log(e);
        }
        finally {
          this.fetchingItems = false;
        }
      },
      async onModalClose(_, event, accept) {
        if(accept) {
          await this.save();
          return;
        }

        this.hide();
      },
      setItem(item) {
        console.log(item);
        this.item = item;
      },
      async deleteItem() {
        if(confirm(`Delete item: ${this.item.name}?`) === false) {
          return;
        }

        try {
          await this.$store.dispatch('device/unAssignTemplate', this.item);
          await this.$store.dispatch('device/fetchDeviceTemplates', this.item.device_id);
        } catch (e) {

        } finally {
          this.hide();
        }
      },
      hide() {
        this.$emit('input', false);
      }
    },
    mounted() {
      this.getItems();
    }
  }
</script>

<style scoped>

</style>
