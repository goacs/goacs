<template>
  <b-modal
          v-model="value"
          has-modal-card
          :can-cancel="false"
  >
    <form>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Upload file</p>
        </header>
        <section class="modal-card-body">
          <b-field label="File type">
            <b-select placeholder="Select" v-model="type">
              <option selected value="1 FIRMWARE">
                1 FIRMWARE
              </option>
            </b-select>
          </b-field>
          <b-field class="file is-primary" :class="{'has-name': !!file}">
            <b-upload v-model="file" class="file-label" :loading="uploading" expanded>
              <span class="file-cta">
                <b-icon class="file-icon" icon="upload"></b-icon>
                <span class="file-label">Click to upload</span>
              </span>
              <span class="file-name" v-if="file">
                {{ file.name }}
              </span>
            </b-upload>
          </b-field>
        </section>
        <footer class="modal-card-foot">
          <b-button @click="$emit('input', false)">Close</b-button>
          <b-button @click="upload" class="is-primary">Save</b-button>
        </footer>
      </div>
    </form>
  </b-modal>
</template>

<script>
  export default {
    name: "UploadFile",
    props: {
      value: {
        type: Boolean,
      },
    },
    data() {
      return {
        type: '1 FIRMWARE',
        file: null,
        uploading: false,
      }
    },
    methods: {
      upload() {
        this.uploading = true

        try {
          this.$store.dispatch('file/upload', {
            file: this.file,
            type: this.type,
          })
          setTimeout(() => this.$emit('uploaded', true), 500);
        } catch (e) {
          this.$buefy.toast.open({
            type: 'is-danger',
            position: 'is-bottom',
            message: "Cannot upload file"
          })
        } finally {
          this.uploading = true
          this.$emit('input', false);
        }
      }
    }
  }
</script>

<style scoped>

</style>
