// import axios from 'axios'


import {filterToQueryString} from "@/helpers/URL";

export default {
  async list(context, parameters) {
    const filterStr = filterToQueryString(parameters.filter)
    return await this._vm.$http.get(`/api/file?page=${parameters.page}&per_page=${parameters.perPage}${filterStr}`)
  },
  async all({ commit }) {
    const response = await this._vm.$http.get('/api/file')
    commit('setFilesList', response.data.data)
    return response
  },
  async upload(context, params) {
    const formData = new FormData();
    formData.append('file', params.file)
    formData.append('type', params.type)
    return await this._vm.$http.post('/api/file', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  },
  async download(context, file_id) {
      return await this._vm.$http.get(`/api/file/${file_id}/download`, {
        responseType: 'blob',
      })
  },
  async delete(context, file_id) {
    return await this._vm.$http.delete(`/api/file/${file_id}`);
  }
}
