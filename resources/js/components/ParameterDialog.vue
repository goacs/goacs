<template>
  <CModal
    :title="`${isNew ? 'Add' : 'Edit'} parameter`"
    color="dark"
    centered
    :closeOnBackdrop="false"
    :show="value"
    @update:show="onModalClose"
  >

    <CAlert v-if="Object.keys(errors).length > 0" color="danger">
      <h3>Validation errors</h3>
      <ul>
        <li v-for="error in errors">{{ error[0] }}</li>
      </ul>
    </CAlert>
    <CInput
      label="Name"
      v-model="item.name"
    ></CInput>

    <CFormGroup class="form-group">
      <template #label>
        <label>Type</label>
      </template>
      <template #input>
        <TypeSelect
          v-model="item.type"
        ></TypeSelect>
      </template>
    </CFormGroup>

    <CInput
      label="Value"
      v-model="item.value"
    ></CInput>

    <label>Flags</label>
    <FlagInput v-model="item.flags"></FlagInput>

    <template #footer>
      <CButton @click="deleteItem()" color="danger" v-if="item.id">Delete</CButton>
      <CButton @click="hide()" color="secondary">Cancel</CButton>
      <CButton @click="save()" color="dark">OK</CButton>
    </template>
    <CElementCover v-if="saving" :opacity="0.8"/>
  </CModal>
</template>

<script>
  import FlagInput from "./FlagInput";
  import TypeSelect from "./TypeSelect";
  export default {
    name: "ParameterDialog",
    components: {TypeSelect, FlagInput},
    data() {
      return {
      }
    },
    props: {
      value: {
        type: Boolean,
        required: true,
      },
      item: {
        type: Object,
        required: true,
      },
      isNew: {
        type: Boolean,
        default: () => false,
      },
      saving: {
        type: Boolean,
        default: () => false,
      },
      errors: {
        type: Object,
        default: () => [],
      }
    },
    computed: {
      canSave() {
        return this.item.name !== ""
      }
    },
    methods: {
      async onModalClose(_, event, accept) {
        if(accept) {
          await this.save();
          return;
        }

        this.hide();
      },
      save() {
        this.saving = true
        this.$emit('onSave', this.item)
      },
      deleteItem() {
        if(confirm(`Delete item: ${this.item.name}?`) === false) {
          return;
        }

        this.$emit('onDelete', this.item);
      },
      hide() {
        this.$emit('input', false);
      }
    },
  }
</script>

<style scoped>

</style>
