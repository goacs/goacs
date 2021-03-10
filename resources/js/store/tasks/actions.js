// import axios from 'axios'

export default {
  async fetchTasks() {
    return await this._vm.$http.get('/settings/tasks')
  },
  async storeTask(context, data) {
    return await this._vm.$http.post('/settings/tasks', data)
  },
  async updateTask(context, data) {
    return await this._vm.$http.put(`/settings/tasks/${data.id}`, data)
  }
}
