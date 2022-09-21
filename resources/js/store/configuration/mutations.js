export default {
  setProvision: (state, data) => state.provision = data,
  resetProvision: (state, data) => state.provision = {
      rules: [],
      denied: [],
      events: '',
  },

}
