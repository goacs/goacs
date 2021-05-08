// import axios from 'axios'

import {filterToQueryString} from "../../helpers/URL";

export default {
  async list(context, parameters) {
    const filterStr = filterToQueryString(parameters.filter)
    return await this._vm.$http.get(`/api/device?page=${parameters.page}&per_page=${parameters.perPage}${filterStr}`)
  },
  async fetchDevice({ commit }, id) {
    try {
      const response = await this._vm.$http.get(`/api/device/${id}`)
      commit('setDevice', response.data.data)
    } catch (e) {
      console.error("Cannot fetch device")
    }
  },
  async updateDevice({commit}, device) {
    try {
      const response = await this._vm.$http.patch(`/api/device/${device.id}`, device);
      commit('setDevice', response.data.data)
    } catch (e) {
      console.error("Cannot update device")
    }
  },
  async fetchParameters({ commit }, parameters) {
    const filterStr = filterToQueryString(parameters.filter)
    try {
      const response = await this._vm.$http.get(`/api/device/${parameters.id}/parameters?page=${parameters.page}&per_page=${parameters.perPage}${filterStr}`)
      const data = response.data.data.map(item => {
        if(item.source === 'template') {
          item._classes = 'table-info';
        }

        return item;
      })
      commit('setParameters', response.data.data)
      commit('hasCachedParams', response.data.has_cached_items)
      return response
    } catch (e) {
      console.error("Cannot fetch device parameters")
    }
  },
  async fetchCachedParameters({ commit }, device_id) {
    try {
      const response = await this._vm.$http.get(`/api/device/${device_id}/parameters/cached`)
      commit('setCachedParameters', response.data.data)
      return response
    } catch (e) {
      console.error("Cannot fetch device cached parameters")
    }
  },
  async storeParameter(context, data) {
    return await this._vm.$http.post(`/api/device/${data.device_id}/parameters`, {
      ...data
    })
  },
  async updateParameters(context, data) {
    return await this._vm.$http.put(`/api/device/${data.device_id}/parameters/${data.id}`, {
      ...data
    })
  },
  async deleteParameter(context, data) {
    return await this._vm.$http.delete(`/api/device/${data.device_id}/parameters/${data.id}`)
  },
  async kickDevice(context, id) {
    return await this._vm.$http.get(`/api/device/${id}/provision`)
  },
  async deleteDevice(context, id) {
    return await this._vm.$http.delete(`/api/device/${id}`)
  },
  async addObject(context, params) {
    const {id, name, key} = params
    return await this._vm.$http.post(`/api/device/${id}/addobject`, {
      name,
      key,
    })
  },
  async lookup(context, id) {
    return await this._vm.$http.get(`/api/device/${id}/lookup`)
  },
  async fetchQueuedTasks({ commit }, id) {
    try {
      const response = await this._vm.$http.get(`/api/device/${id}/tasks`)
      commit('setQueuedTasks', response.data.data)
      return response
    } catch (e) {
      console.log('cannot fetch queued tasks', e)
    }
  },
  async fetchDeviceTemplates({ commit }, id) {
    try {
      const response = await this._vm.$http.get(`/api/device/${id}/templates`)
      commit('setDeviceTemplates', response.data.data)
      return response
    } catch (e) {
      console.log('cannot fetch device templates', e)
    }
  },
  async assignTemplate(context, params) {
    return await this._vm.$http.post(`/api/device/${params.device_id}/templates`, {
      template_id: params.template_id,
      priority: params.priority,
    })
  },
  async unAssignTemplate(context, params) {
    return await this._vm.$http.delete(`/api/device/${params.device_id}/templates/${params.template_id}`)
  },

  async addTask(context, params) {
    return await this._vm.$http.post(`/api/device/${params.device_id}/tasks`, params.task)
  },
  async listLogs(context, parameters) {
    const filterStr = filterToQueryString(parameters.filter)
    return await this._vm.$http.get(`/api/device/${parameters.device_id}/logs?page=${parameters.page}&per_page=${parameters.perPage}${filterStr}`)
  },
}
