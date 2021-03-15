<template>
  <CModal
    :title="`${isNew ? 'Add' : 'Edit'} task`"
    size="xl"
    color="dark"
    centered
    :closeOnBackdrop="false"
    :show="value"
    @update:show="onModalClose"
  >
    <CRow>
      <CCol lg="4" sm="12">
    <CSelect
      label="On Event"
      placeholder="Select"
      :value.sync="on_request"
      :options="requests"
    ></CSelect>
      </CCol>
      <CCol lg="4" sm="12">
        <CSelect
          label="Type"
          placeholder="Select"
          :value.sync="name"
          :options="types"
        ></CSelect>
      </CCol>
      <CCol lg="4" sm="12">
        <div class="form-group">
        <label>Infinite</label>
          <div class="form-control-plaintext">
            <CSwitch
              :checked.sync="infinite"
              type="checkbox"
              color="dark"
              label-on="yes"
              label-off="no"
            >
            </CSwitch>
          </div>
        </div>
      </CCol>
    </CRow>
    <CRow>
      <CCol>
        <div v-if="name === 'RunScript'">
          <label>Script</label>
          <CodeEditor v-model="script"></CodeEditor>
        </div>
        <div v-if="name === 'UploadFirmware'">
          <label>Choose file</label>
          <FirmwareSelect v-model="fileName"/>
        </div>
      </CCol>
    </CRow>
    <template #footer>
      <CButton @click="deleteItem()" color="danger" v-if="taskid">Delete</CButton>
      <CButton @click="hide()" color="secondary">Cancel</CButton>
      <CButton @click="save()" color="dark">OK</CButton>
    </template>
    <CElementCover v-if="saving" :opacity="0.8"/>
  </CModal>
</template>

<script>
  import CodeEditor from "./CodeEditor";
  import FirmwareSelect from "@/components/FirmwareSelect";
  import {Task} from "@/helpers/Task";
  export default {
    name: "TaskDialog",
    components: {FirmwareSelect, CodeEditor},
    props: {
      value: {
        type: Boolean,
        required: true,
      },
      isNew: {
        type: Boolean,
        default: () => false,
      },
      task: {
        type: Object,
        default: () => {
          return new Task()
        }
      }
    },
    data() {
      return {
        saving: false,
        fileName: '',
        fileType: '',
        name: '',
        on_request: '',
        script: '',
        path: '',
        infinite: false,
        for_id: '',
        for_name: '',
        taskid: 0,
        newtask: new Task(),
        types: [
          {
            value: 'RunScript',
            label: 'Run Script',
          },
          {
            value: 'UploadFirmware',
            label: 'Upload Firmware',
          },
          {
            value: 'Reboot',
            label: 'Reboot',
          }
        ],
        requests: [
          {
            value: 'inform',
            label: 'Inform',
          },
          {
            value: 'GetParameterValuesResponse',
            label: 'GetParameterValuesResponse',
          }
        ],
      };
    },
    methods: {
      serializeTask() {
        this.newtask.on_request = this.on_request
        this.newtask.for_id = this.for_id
        this.newtask.for_type = this.for_type
        this.newtask.id = this.taskid
        this.newtask.infinite = this.infinite
        if(this.name === 'UploadFirmware') {
          this.newtask.asFirmwareUpdateTask(this.fileType, this.fileName)
        } else if (this.name === 'RunScript') {
          this.newtask.asScriptTask(this.script)
        } else if (this.name === 'AddObject') {
          this.newtask.asAddObject(this.path)
        } else if (this.name === 'DeleteObject') {
          this.newtask.asDeleteObjectTask(this.path)
        } else if(this.name === 'Reboot') {
          this.newtask.asReboot()
        }
      },
      unserializeTask() {
        this.name = this.task.name
        this.on_request = this.task.on_request
        this.infinite = this.task.infinite
        this.for_id = this.task.for_id
        this.for_type = this.task.for_type
        this.taskid = this.task.id
        if(this.name === 'UploadFirmware') {
          this.fileType = this.task.payload.filetype
          this.fileName = this.task.payload.filename
        } else if (this.name === 'RunScript') {
          this.script = this.task.payload.script
        } else if (this.name === 'AddObject') {
          this.path = this.task.payload.parameter
        } else if (this.name === 'DeleteObject') {
          this.path = this.task.payload.parameter
        }
      },
      async onModalClose(_, event, accept) {
        if(accept) {
          this.save();
          return;
        }

        this.$emit('input', false);
      },
      save() {
        this.serializeTask()
        this.$emit('onSave', this.newtask)
      },
      deleteItem() {
        if(confirm(`Delete item: ${this.taskid}?`) === false) {
          return;
        }

        this.$emit('onDelete', this.taskid);
      },
      hide() {
        this.$emit('input', false);
      }
    },
    watch: {
      task() {
        console.log('unserialization')
        this.unserializeTask()
      }
    }
  }
</script>

<style lang="scss" scoped>
.dropdown-menu {
  position: static;
}

</style>
