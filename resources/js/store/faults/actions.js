// import axios from 'axios'

import {filterToQueryString} from "../../helpers/URL";

export default {
  async list(context, parameters) {
    const filterStr = filterToQueryString(parameters.filter)
    return await this._vm.$http.get(`/api/faults?page=${parameters.page}&per_page=${parameters.perPage}${filterStr}`)
  },
}
