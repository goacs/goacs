<template>
  <CModal
    title="Upload file"
    size="xl"
    color="dark"
    centered
    :closeOnBackdrop="false"
    :show="value"
    @update:show="onModalClose"
  >
    <CRow>
      <CCol lg="6" sm="12">
        <CSelect
          label="File type"
          placeholder="Select"
          :value.sync="type"
          :options="types"
        ></CSelect>
      </CCol>
      <CCol lg="6" sm="12">
        <CInputFile
          label="File"
          @change="selectFile"
        >

        </CInputFile>
      </CCol>
    </CRow>
    <CElementCover v-if="uploading" :opacity="0.8"/>
  </CModal>
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
        type: '1 Firmware Upgrade Image',
        types: [
          {
            value: '1 Firmware Upgrade Image',
            label: '1 Firmware Upgrade Image'
          },
        ],
        file: null,
        uploading: false,
      }
    },
    methods: {
      async onModalClose(_, event, accept) {
        if(accept) {
          this.upload();
          return;
        }

        this.$emit('input', false);
      },
      selectFile(files) {
        this.file = files[0];
      },
      async upload() {
        this.uploading = true

        try {
          await this.$store.dispatch('file/upload', {
            file: this.file,
            type: this.type,
          })
          setTimeout(() => this.$emit('uploaded', true), 500);
        } catch (e) {

        } finally {
          this.uploading = false
          this.$emit('input', false);
        }
      }
    }
  }
</script>

<style scoped>

</style>
