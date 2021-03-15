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
      async onModalClose(_, event, accept) {
        if(accept) {
          await this.save();
          return;
        }

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
