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
    }
  }
}
