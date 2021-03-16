// import axios from 'axios'

export default {
  async getConfig() {
    return await this._vm.$http.get('/api/config')
  },
  async saveConfig(context, config) {
    return await this._vm.$http.post('/api/config', {
      config: config
    })
  }
}
