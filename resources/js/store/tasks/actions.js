// import axios from 'axios'

export default {
  async fetchTasks() {
    return await this._vm.$http.get('/api/settings/tasks')
  },
  async storeTask(context, data) {
    return await this._vm.$http.post('/api/settings/tasks', data)
  },
  async updateTask(context, data) {
    return await this._vm.$http.put(`/api/settings/tasks/${data.id}`, data)
  },
  async deleteTask(context, taskid) {
    return await this._vm.$http.delete(`/api/settings/tasks/${taskid}`)
  }
}
