export default {
  methods: {
    extractErrorsFromResponse(response) {
      if(response.status === 422) {
        return response.data.errors;
      }
      return [];
    },
    setErrorsFromResponse(response, mutation) {
      this.$store.commit(mutation, this.extractErrorsFromResponse(response));
    }
  }
}
