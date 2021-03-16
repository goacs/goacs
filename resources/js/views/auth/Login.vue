<template>
    <div class="c-app flex-row align-items-center">
      <CContainer>
        <CRow class="justify-content-center">
          <CCol md="6">
            <CCardGroup>
              <CCard class="p-4">
                <CCardBody>
                  <ValidationObserver ref="form" v-slot="{ passes }">
                  <CForm novalidate @submit.prevent="passes(login)">
                    <h1>GOACS</h1>
                    <p class="text-muted">Sign In to your account</p>
                    <ValidationProvider vid="email" name="Email"
                                        rules="required|email"
                                        v-slot="scope">
                      <CInput
                        v-model="email"
                        placeholder="Email"
                        autocomplete="username email"
                        :invalid-feedback="scope.errors[0]"
                        :is-valid="isvalid(scope)"
                      >

                        <template #prepend-content><CIcon name="cil-at"/></template>
                      </CInput>
                    </ValidationProvider>
                    <ValidationProvider vid="password" name="Password"
                                        rules="required"
                                        v-slot="scope">
                    <CInput
                      v-model="password"
                      placeholder="Password"
                      type="password"
                      autocomplete="password"
                      :invalid-feedback="scope.errors[0]"
                      :is-valid="isvalid(scope)"
                    >
                      <template #prepend-content><CIcon name="cil-lock-locked"/></template>
                    </CInput>
                    </ValidationProvider>
                    <CRow>
                      <CCol col="6" class="text-left">
                        <CButton color="primary" class="px-4" type="submit">Login</CButton>
                      </CCol>
                      <CCol col="6" class="text-right">
<!--                        <CButton color="link" class="px-0">Forgot password?</CButton>-->
<!--                        <CButton color="link" class="d-lg-none">Register now!</CButton>-->
                      </CCol>
                    </CRow>
                  </CForm>
                  </ValidationObserver>
                </CCardBody>
              </CCard>
            </CCardGroup>
          </CCol>
        </CRow>
      </CContainer>
    </div>
</template>

<script>
    export default {
        name: "Login",
        data: () => ({
          email: '',
          password: '',
          loading: false,
          errors: {},
        }),
        methods: {
            async login() {
                this.loading = true
                try {
                    await this.$auth.login({
                        email: this.email,
                        password: this.password,
                    })
                } catch (e) {
                    this.$refs.form.setErrors(e.response.data.errors);
                } finally {
                    this.loading = false
                }
            }
        },
    }
</script>

<style scoped>
</style>
