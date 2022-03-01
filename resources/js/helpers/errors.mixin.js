export default {
  data() {
    return {
      errors: [],
    }
  },
  methods: {
    isvalid(scope) {
      if(scope.validated === false) {
        return null;
      }
      return scope.valid;
    },
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
