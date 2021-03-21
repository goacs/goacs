// import axios from 'axios'

import {filterToQueryString} from "../../helpers/URL";

export default {
  async list(context, parameters) {
    const filterStr = filterToQueryString(parameters.filter)
    return await this._vm.$http.get(`/api/settings/user?page=${parameters.page}&per_page=${parameters.perPage}${filterStr}`);
  },
  async storeUser(context, user) {
    return await this._vm.$http.post(`/api/settings/user`, user);
  },
  async updateUser(context, user) {
    return await this._vm.$http.put(`/api/settings/user/${user.id}`, user);
  },
  async deleteUser(context, user) {
    return await this._vm.$http.delete(`/api/settings/user/${user.id}`);
  }
}
