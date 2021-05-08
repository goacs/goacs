// import axios from 'axios'

export default {
  async getConfig() {
    return await this._vm.$http.get('/api/settings')
  },
  async saveConfig(context, config) {
    return await this._vm.$http.post('/api/settings', config)
  },
  async fetchDebugConfig({ commit }) {
    const response = await this._vm.$http.get('/api/settings/debug');
    commit('setDebug', response.data.data);
  },
  async saveDebugConfig({ commit }, debugConfig) {
    const response = await this._vm.$http.post('/api/settings/debug', debugConfig)
    commit('setDebug', response.data.data);
  }
}
