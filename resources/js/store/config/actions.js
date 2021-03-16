// import axios from 'axios'

export default {
  async getConfig() {
    return await this._vm.$http.get('/api/settings')
  },
  async saveConfig(context, config) {
    return await this._vm.$http.post('/api/settings', config)
  }
}
