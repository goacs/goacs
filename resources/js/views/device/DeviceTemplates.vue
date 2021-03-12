<template>
  <CCard>
    <CCardHeader>
      <strong>Device templates</strong>
      <CButton color="dark" class="float-right" variant="outline" size="sm" @click="addTemplate">
        <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon> Assign</CButton>
    </CCardHeader>
    <CCardBody>
      <CButton v-for="template in templates" :key="template.id" type="button" color="primary" size="sm" @click="editTemplate(template)">
        {{ template.name }} <CBadge color="light" class="ml-2 position-static">{{ template.pivot.priority }}</CBadge>
      </CButton>
    </CCardBody>
    <TemplateDialog v-model="templateDialog" ref="dialog"></TemplateDialog>
  </CCard>
</template>

<script>
  import {mapGetters} from "vuex";
  import TemplateDialog from "./template/TemplateDialog";

  export default {
    name: "DeviceTemplates",
    components: {
      TemplateDialog,
    },
    data() {
      return {
        templateDialog: false,
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
        this.templateDialog = true;
        this.$refs.dialog.setItem({
            template_id: 0,
            priority: 100,
            device_id: 0,
        });
      },
      editTemplate(template) {
        this.templateDialog = true;
        this.$refs.dialog.setItem(template.pivot);
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
