<template>
  <CCard>
    <CCardHeader>
      <strong>Queued Tasks</strong>
    </CCardHeader>
    <CCardBody>
      <CListGroup>
        <CListGroupItem href='#' @click.prevent="" class="flex-column align-items-start" v-for="task in tasks" :key="task.id">
          <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">{{ task.name }}</h5>
            <small>{{ task.created_at | duration('humanize') }}</small>
          </div>
          <p class="mb-1 h5">
            <CBadge color="primary">On request: {{ task.on_request }}</CBadge>
            <CBadge :color="task.infinite ? 'danger' : 'success'">Infinite: {{ task.infinite }}</CBadge>
          </p>
        </CListGroupItem>
      </CListGroup>
    </CCardBody>
  </CCard>
<!--    <TaskDialog v-model="addDialog" is-new @onSave="saveTask"></TaskDialog>-->
</template>

<script>
  import {mapGetters} from "vuex";
  import TaskDialog from "../../components/TaskDialog";

  export default {
    name: "DeviceQueuedTasks",
    components: {TaskDialog},
    data() {
      return {
        addDialog: false,
      }
    },
    computed: {
      ...mapGetters({
        device: 'device/getDevice',
        tasks: 'device/getQueuedTasks',
      }),
    },
    methods: {
      async fetchTasks() {
        this.$store.dispatch('device/fetchQueuedTasks', this.device.id)
      },
      async saveTask(task) {
        task.for_id = this.device.id

        const params = {
          task: task,
          device_id: this.device.id
        }

        try {
          await this.$store.dispatch('device/addTask', params)
          this.fetchTasks()
          this.addDialog = false;
        } catch (e) {
        }
      }
    },
    async mounted() {
      await this.fetchTasks()
    },
    beforeDestroy() {
      this.$store.commit('device/setQueuedTasks', [])
    }
  }
</script>

<style scoped>
  .script-description {
    margin-left: 1em;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 60%;
  }
</style>
