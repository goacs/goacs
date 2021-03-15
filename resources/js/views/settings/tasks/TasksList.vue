<template>
  <CCard>
    <CCardHeader>Global Tasks
    <CButton color="dark" class="float-right" variant="outline" size="sm" @click="addDialog = true">
      <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon> Add
    </CButton>
    </CCardHeader>
    <CCardBody>
      <table class="table is-fullwidth is-hoverable">
        <thead>
        <tr>
          <th>ID</th>
          <th>For</th>
          <th>On request</th>
          <th>Task</th>
          <th>Payload</th>
          <th>Infinite</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr is="TaskRow" v-for="task in tasks" :key="task.id" :task="task"></tr>
        </tbody>
      </table>
    </CCardBody>
    <TaskDialog is-new v-model="addDialog" @onSave="saveTask"></TaskDialog>
    <TaskDialog v-model="editDialog" :task="editedTask" @onSave="updateTask"></TaskDialog>
  </CCard>
</template>

<script>
  import TaskRow from "./TaskRow";
  import {mapGetters} from "vuex";
  import TaskDialog from "../../../components/TaskDialog";
  export default {
    name: "TasksList",
    components: {TaskDialog, TaskRow},
    computed: {
      ...mapGetters({
        tasks: 'tasks/getTasks',
      }),
    },
    data() {
      return {
        addDialog: false,
        editDialog: false,
        editedTask: {},
      };
    },
    methods: {
      async fetchTasks() {
        try {
          const response = await this.$store.dispatch('tasks/fetchTasks')
          this.$store.commit('tasks/setTasks', response.data.data)
        } catch (e) {
          this.$store.commit('tasks/setTasks', [])
        }
      },
      async editTask(task) {
        this.editedTask = task;
        this.editDialog = true;
      },
      async updateTask(task) {
        try {
          await this.$store.dispatch('tasks/updateTask', task)
          await this.fetchTasks()
          this.editDialog = false
        } catch (e) {
          this.$buefy.toast.open({
            duration: 5000,
            message: `Error. Cannot update task: ${e.response.data.message}`,
            position: 'is-bottom',
            type: 'is-danger'
          })
        }
      },
      async saveTask(task) {
        try {
          await this.$store.dispatch('tasks/storeTask', task)
          await this.fetchTasks()
          this.addDialog = false;
        } catch (e) {
          this.$buefy.toast.open({
            duration: 5000,
            message: `Error. Cannot add task: ${e.response.data.message}`,
            position: 'is-bottom',
            type: 'is-danger'
          })
        }
      }
    },
    async beforeMount() {
      await this.fetchTasks()
    }
  }
</script>

<style scoped>

</style>
