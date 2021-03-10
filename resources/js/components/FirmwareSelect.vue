<template>
  <b-autocomplete
      v-model="typedname"
      :data="filteredFiles"
      placeholder="Select firmware"
      field="filename"
      open-on-focus
      @select="onSelect">

    <template slot-scope="props">
      <div class="media">
        <div class="media-content">
          {{ props.option.filename }}
          <br>
          <small>
            Size {{ props.option.size }} bytes,
            added at {{ props.option.mod_time | moment('YYYY-MM-DD HH:ss')}}
          </small>
        </div>
      </div>
    </template>
  </b-autocomplete>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: "FirmwareSelect",
  data() {
    return {
      selected: null,
      typedname: '',
    };
  },
  computed: {
    ...mapGetters({
      'files': 'file/getFilesList'
    }),
    filteredFiles() {
      if(!this.typedname) {
        return this.files
      }

      return this.files.filter((option) => {
        return option
            .filename
            .toString()
            .toLowerCase()
            .indexOf(this.typedname.toLowerCase()) >= 0
      })
    }
  },
  mounted() {
    this.fetchFileList()
  },

  methods: {
    onSelect(option) {
      this.selected = option
      this.$emit('input', this.selected.filename)
    },
    fetchFileList() {
      this.$store.dispatch('file/all')
    }
  }
}
</script>

<style scoped>

</style>