<template>
  <div class="card">
    <header class="card-header">
      <p class="card-header-title">
        Tasks
      </p>
      <div class="card-header-icon" aria-label="more options">
        <b-button
                size="is-small"
                @click="addDialog = true"
        >
          <b-icon
                  icon="plus"
                  size="is-small"
          >

          </b-icon>
          New
        </b-button>
      </div>
    </header>
    <div class="card-content">
      <div class="table-container">
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
      </div>
    </div>
    <TaskDialog is-new v-model="addDialog" @onSave="saveTask"></TaskDialog>
    <TaskDialog v-model="editDialog" :task="editedTask" @onSave="updateTask"></TaskDialog>
  </div>
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
