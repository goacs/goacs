// import axios from 'axios'

export default {
  async getConfig() {
    return await this._vm.$http.get('/config')
  },
  async saveConfig(context, config) {
    return await this._vm.$http.post('/config', {
      config: config
    })
  }
}
