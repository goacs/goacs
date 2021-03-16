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
        type: '1 FIRMWARE',
        types: [
          {
            value: '1 FIRMWARE',
            label: '1 FIRMWARE'
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
      upload() {
        this.uploading = true

        try {
          this.$store.dispatch('file/upload', {
            file: this.file,
            type: this.type,
          })
          setTimeout(() => this.$emit('uploaded', true), 500);
        } catch (e) {

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
