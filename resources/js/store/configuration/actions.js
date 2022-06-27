// import axios from 'axios'

import {filterToQueryString} from "../../helpers/URL";

export default {
  async list(context, parameters) {
    const filterStr = filterToQueryString(parameters.filter)
    return await this._vm.$http.get(`/api/provision?page=${parameters.page}&per_page=${parameters.perPage}${filterStr}`)
  },
  async store(context, form) {
    return await this._vm.$http.post('/api/provision', form);
  },
  async update({commit}, form) {
    const response = await this._vm.$http.put(`/api/provision/${form.id}`, form);
    commit('setProvision', response.data.data);
    return response;
  },
  async destroy({ commit }, id) {
    const response = await this._vm.$http.delete(`/api/provision/${id}`);
    commit('resetProvision');
    return response;
  },
  async fetchProvision({commit}, id) {
    const response = await this._vm.$http.get(`/api/provision/${id}`);
    commit('setProvision', response.data.data);
    return response;
  }
}
