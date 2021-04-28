<template>
  <v-select
      v-model="selected"
      :options="files"
      placeholder="Select firmware"
      label="name"
      :reduce="item => item.name"
  >

    <template #option="item">
      <h5 style="margin: 0">{{ item.name }}</h5>
      <em>{{ item.type }} Size: {{ item.size }}</em>
    </template>
  </v-select>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: "FirmwareSelect",
  data() {
    return {
      selected: null,
    };
  },
  computed: {
    ...mapGetters({
      files: 'file/getFilesList'
    }),
  },
  mounted() {
    this.fetchFileList();
  },
  methods: {
    async fetchFileList() {
      await this.$store.dispatch('file/all');
    }
  },
  watch: {
    selected(val) {
      this.$emit('input', val);
    }
  }
}
</script>

<style scoped>

</style>
