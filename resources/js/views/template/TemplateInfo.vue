<template>
  <CCard>
    <CCardHeader>
      <strong>Template Info</strong>
      <CButton color="dark" class="float-right" variant="outline" size="sm" @click="dialog = true">
        <CIcon name="cil-pencil" class="btn-icon mt-0" size="sm"></CIcon> Edit
      </CButton>
    </CCardHeader>
    <CCardBody>
      <table class="table is-fullwidth is-striped">
        <tbody>
        <tr>
          <th>ID</th>
          <td>{{ template.id }}</td>
        </tr>
        <tr>
          <th>Name</th>
          <td>{{ template.name }}</td>
        </tr>
        <tr>
          <th>Used by</th>
          <td>0</td>
        </tr>
        </tbody>
      </table>
    </CCardBody>
    <CModal
      title="Edit template"
      color="dark"
      centered
      :show="dialog"
      @update:show="onModalClose"
    >
      <CInput
        label="Name"
        v-model="template.name"
      >
      </CInput>
      <CElementCover v-if="saving" :opacity="0.8"/>
    </CModal>
  </CCard>
</template>

<script>
  import {mapGetters} from "vuex";

  export default {
    name: "TemplateInfo",
    data() {
      return {
        dialog: false,
        saving: false,
      };
    },
    computed: {
      ...mapGetters({
      }),
      template: {
        get() {
          return this.$store.state.template.template;
        },
        set(value) {
          this.$store.commit('template/setTemplate', value);
        }
      }
    },
    methods: {
      onModalClose(_, event, accept) {
        if(accept) {
          this.save();
          return;
        }

        this.dialog = false;
      },
      async save() {
        try {
          await this.$store.dispatch('template/updateTemplate', this.template)
        } catch (e) {

        } finally {
          this.dialog = false;
        }
      }
    }
  }
</script>

<style scoped>

</style>
