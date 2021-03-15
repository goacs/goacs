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

        this.$emit('input', false);
      },
      save() {
        this.saving = true
        this.$emit('onSave', this.item)
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
