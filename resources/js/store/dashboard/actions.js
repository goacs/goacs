// import axios from 'axios'

export default {
  async fetchDashboard({ commit }) {
    try {
      const response = await this._vm.$http.get(`/dashboard`)
      commit('setDashboard', response.data)
    } catch (e) {
      console.error("Cannot fetch dashboard data")
    }
  },
}
