<template>
  <b-modal
          v-model="value"
          has-modal-card
          scroll="keep"
          :canCancel="false"
  >
    <form>
      <div class="modal-card" style="width: 100%">
        <header class="modal-card-head">
          <p class="modal-card-title">{{ isNew ? `Add` : `Edit` }} task</p>
          <div class="card-header-icon" aria-label="more options" v-if="isNew === false">
            <b-button
                    size="is-small"
                    type="is-danger"
                    @click="$emit('onDelete', item)"
            >
              <b-icon
                      size="is-small"
                      icon="delete"
              >

              </b-icon>
              Delete
            </b-button>
          </div>
        </header>
        <section class="modal-card-body">
          <div class="content">
          <b-field label="Event" horizontal>
            <b-select placeholder="Select event"
                      v-model="on_request"
            >
              <option value="inform">Inform</option>
<!--              <option value="empty">Empty</option>-->
              <option value="GetParameterValuesResponse">GetParameterValues Response</option>
            </b-select>
          </b-field>
          <b-field label="Type" horizontal>
            <b-select placeholder="Select type"
              v-model="name"
            >
              <option value="RunScript">Run Script</option>
              <option value="SendParameters">Send Parameters</option>
              <option value="Reboot">Reboot</option>
              <option value="UploadFirmware">Upload Firmware</option>
            </b-select>
          </b-field>
          <b-field label="Infinite" horizontal>
            <b-checkbox v-model="infinite">
              {{ infinite ? `Payload will be not deleted when executed` : `` }}
            </b-checkbox>
          </b-field>
          <b-field v-if="name === 'RunScript'" label="Script" horizontal>
            <CodeEditor v-model="script"></CodeEditor>
<!--            <b-input type="textarea" v-model="task.script"></b-input>-->
          </b-field>
          <template v-if="name === 'UploadFirmware'">
          <b-field label="Choose file" horizontal>
              <FirmwareSelect v-model="fileName"/>
          </b-field>
          <b-field label="File type" horizontal>
            <b-select placeholder="Select type" v-model="fileType">
              <option value="1 Firmware Upgrade Image">1 Firmware Upgrade Image</option>
              <option value="2 Web Content">2 Web Content</option>
              <option value="3 Vendor Configuration File">3 Vendor Configuration File</option>
            </b-select>
          </b-field>
          </template>
          </div>
        </section>
        <footer class="modal-card-foot">
          <b-button @click="$emit('input', false)">Close</b-button>
          <b-button type="is-primary" class="is-align-content-end" :loading="saving" @click="save">Save</b-button>
        </footer>
      </div>
    </form>
  </b-modal>
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
        newtask: new Task()
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
      save() {
        this.serializeTask()
        this.$emit('onSave', this.newtask)
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
