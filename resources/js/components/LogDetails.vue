<template>
  <CModal
    title="Log details"
    size="lg"
    color="dark"
    centered
    :show="value"
    @update:show="onUpdateShow"
  >
    <template v-if="hasJson">
      <label>JSON</label>
      <json-tree :data="details.json"></json-tree>
    </template>

    <CTextarea
      class="mt-3"
      label="DATA"
      rows="20"
      v-model="details.xml"
    >
    </CTextarea>
  </CModal>
</template>

<script>
import {isEmpty} from "lodash";

export default {
  name: "LogDetails",
  props: {
    value: {
      type: Boolean,
      required: true,
    },
    details: {
      type: Object,
      required: true,
    }
  },
  computed: {
    hasJson() {
      return !isEmpty(this.details.json);
    },
  },
  methods: {
    onUpdateShow(event) {
      if(event === false) {
        this.$emit('input', false);
      }
    }
  }
}
</script>

<style scoped>

</style>
