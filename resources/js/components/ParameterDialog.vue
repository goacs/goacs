<template>
  <CModal
    :title="`${isNew ? 'Add' : 'Edit'} parameter`"
    color="dark"
    centered
    :closeOnBackdrop="false"
    :show="value"
    @update:show="onModalClose"
  >

    <CInput
      label="Name"
      v-model="item.name"
    ></CInput>

    <CInput
      label="Type"
      v-model="item.type"
    ></CInput>

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
  export default {
    name: "ParameterDialog",
    components: {FlagInput},
    data() {
      return {
        saving: false,
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
    watch: {
      value() {
        this.saving = false;
      },
    }
  }
</script>

<style scoped>

</style>
