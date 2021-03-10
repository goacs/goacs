<template>
  <b-dropdown
          v-model="value"
          multiple
          ref="dropdown"
          aria-role="list"
          v-bind="$props"
  >
    <b-button class="is-primary" size="is-small" type="button" slot="trigger">
      Selected ({{ value.length }})
      <b-icon icon="menu-down"></b-icon>
    </b-button>
    <template v-for="item in items">
      <b-dropdown-item custom value="option1" aria-role="listitem" :key="item.value">
        <b-checkbox @input="$refs.dropdown.selectItem(item.value)">
          <span>{{ item.text }}</span>
        </b-checkbox>
      </b-dropdown-item>
    </template>
  </b-dropdown>
</template>

<script>
  export default {
    name: "CheckboxSelection",
    data() {
      return {

      };
    },
    props: {
      value: {
        type: Array,
        default: () => []
      },
      items: {
        type: Array,
        required: true,
      }
    },
    watch: {
      value() {
        this.$emit('input', this.value);
      }
    }
  }
</script>

<style scoped>

</style>
