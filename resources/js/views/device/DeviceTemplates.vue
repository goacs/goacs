<template>
  <div class="card">
    <header class="card-header">
      <p class="card-header-title">
        Device templates
      </p>
      <div class="card-header-icon" aria-label="more options">
        <b-button size="is-small" @click="addTemplate">
          <b-icon
                  icon="plus"
                  size="is-small"
          ></b-icon>
          Assign
        </b-button>
      </div>
    </header>
    <div class="card-content">
      <div class="field is-grouped is-grouped-multiline">
        <template v-for="template in templates">
          <div class="control is-clickable" :key="template.id" @click="editTemplate(template)">
            <b-taglist attached>
                  <b-tag type="is-black" size="is-medium" ellipsis>{{ template.name }}</b-tag>
                  <b-tag type="is-info" size="is-medium">{{ template.pivot.priority }}</b-tag>
            </b-taglist>
          </div>
        </template>
      </div>

      <span class="is-small has-text-grey-light">
        Higher order priority
      </span>
    </div>
    <AddDialog v-model="addDialog"></AddDialog>
    <EditDialog v-model="editDialog" :item="editedItem"></EditDialog>
  </div>
</template>

<script>
  import {mapGetters} from "vuex";
  import EditDialog from "./template/EditDialog";
  import AddDialog from "./template/AddDialog";

  export default {
    name: "DeviceTemplates",
    components: {
      AddDialog,
      EditDialog
    },
    data() {
      return {
        addDialog: false,
        editDialog: false,
        editedItem: {},
      };
    },
    computed:{
      ...mapGetters({
        device: 'device/getDevice',
        templates: 'device/getDeviceTemplates'
      }),
    },
    methods: {
      addTemplate() {
        console.log("add templ")
        this.addDialog = true;
      },
      editTemplate(template) {
        this.editedItem = Object.assign({}, template); //clone item, not reference
        this.editDialog = true;
      },

    },
    mounted() {
      this.$store.dispatch('device/fetchDeviceTemplates', this.device.id)
    }
  }
</script>

<style scoped>
  .is-clickable {
    cursor: pointer;
  }
</style>
