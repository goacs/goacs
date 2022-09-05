<template>
  <CButton
    v-bind="{...$attrs, ...$props}"
  >
    <CSpinner size="sm" v-if="loading"></CSpinner>
    <template v-for="(_, slot) of $scopedSlots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="scope"/>
    </template>
    <slot></slot>
  </CButton>
</template>

<script>
export default {
  name: "LoadingButton",
  props: {
    loading: {
      type: Boolean,
      default: false,
    }
  }
}
</script>

<style scoped>

</style>
