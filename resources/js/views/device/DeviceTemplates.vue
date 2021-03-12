<template>
  <CCard>
    <CCardHeader>
      <strong>Device templates</strong>
    </CCardHeader>
    <CCardBody>
      <CButton v-for="template in templates" :key="template.id" type="button" color="primary">
        {{ template.name }} <CBadge color="light" class="ml-2 position-static">{{ template.pivot.priority }}</CBadge>
      </CButton>
    </CCardBody>
  </CCard>
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
