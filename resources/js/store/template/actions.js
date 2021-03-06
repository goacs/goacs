// import axios from 'axios'

import {filterToQueryString} from "../../helpers/URL";

export default {
  async list(context, parameters) {
    const filterStr = filterToQueryString(parameters.filter)
    return await this._vm.$http.get(`/api/template?page=${parameters.page}&per_page=${parameters.perPage}${filterStr}`)
  },
  async fetchTemplate({ commit }, templateId) {
    try {
      const response = await this._vm.$http.get(`/api/template/${templateId}`)
      commit('setTemplate', response.data.data)
    } catch (e) {
      console.error("Cannot fetch template")
    }
  },
  async listParameters({ commit }, parameters) {
    const filterStr = filterToQueryString(parameters.filter)
    try {
      const response = await this._vm.$http.get(`/api/template/${parameters.templateId}/parameters?page=${parameters.page}&per_page=${parameters.perPage}${filterStr}`)
      commit('setParameters', response.data.data)
      return response
    } catch (e) {
      console.error("Cannot fetch template parameters")
    }
  },
  async addTemplate(context, params) {
    return await this._vm.$http.post(`/api/template`, {
      name: params.name,
    })
  },
  async updateTemplate(context, params) {
    return await this._vm.$http.patch(`/api/template/${params.id}`, {
      name: params.name,
    })
  },
  async storeParameter(context, parameters) {
    return await this._vm.$http.post(`/api/template/${parameters.templateId}/parameters`, {
      ...parameters
    })
  },
  async updateParameter(context, parameters) {
    return await this._vm.$http.put(`/api/template/${parameters.templateId}/parameters/${parameters.id}`, {
      ...parameters
    })
  },
  async deleteParameter(context, parameters) {
    return await this._vm.$http.delete(`/api/template/${parameters.templateId}/parameters/${parameters.id}`)
  }
}
