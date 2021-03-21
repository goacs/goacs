<template>
  <CModal
    :title="`${user.id ? 'Edit' : 'Add'} user`"
    size="lg"
    color="dark"
    centered
    :closeOnBackdrop="false"
    :show="value"
  >
    <ValidationObserver ref="form" v-slot="{ passes }">
    <CForm novalidate>
    <CRow>
      <CCol sm="12">
        <ValidationProvider vid="name" name="Name"
                            rules="required"
                            v-slot="scope">
          <CInput
            name="name"
            label="Name"
            v-model="user.name"
            :invalid-feedback="scope.errors[0]"
            :is-valid="isvalid(scope)"
          ></CInput>
        </ValidationProvider>
      </CCol>
    </CRow>
    <CRow>
      <CCol sm="12">
        <ValidationProvider vid="email" name="Email"
                            rules="required|email"
                            v-slot="scope">
          <CInput
            name="email"
            label="Email"
            v-model="user.email"
            :invalid-feedback="scope.errors[0]"
            :is-valid="isvalid(scope)"
          ></CInput>
        </ValidationProvider>
      </CCol>
    </CRow>
    <CRow>
      <CCol sm="12">
        <ValidationProvider vid="password" name="Password"
                            v-slot="scope">
          <CInput
            name="password"
            label="Password (leave blank if no change)"
            v-model="user.password"
            type="password"
            :invalid-feedback="scope.errors[0]"
            :is-valid="isvalid(scope)"
          ></CInput>
        </ValidationProvider>
      </CCol>
    </CRow>
    <CRow>
      <CCol sm="12">
        <ValidationProvider vid="password_confirmation" name="Password Confirm"
                            v-slot="scope">
        <CInput
          name="password_confirmation"
          label="Password confirm (leave blank if no change)"
          v-model="user.password_confirmation"
          type="password"
          :invalid-feedback="scope.errors[0]"
          :is-valid="isvalid(scope)"
        ></CInput>
        </ValidationProvider>
      </CCol>
    </CRow>
    </CForm>
    </ValidationObserver>
    <template #footer>
      <CButton @click="deleteItem()" color="danger" v-if="user.id">Delete</CButton>
      <CButton @click="hide()" color="secondary">Cancel</CButton>
      <CButton @click="save()" color="dark">OK</CButton>
    </template>
    <CElementCover v-if="saving" :opacity="0.8"/>
  </CModal>
</template>

<script>
export default {
  name: "UserDialog",
  props: {
    value: {
      type: Boolean,
      required: true,
    },
    user: {
      type: Object,
      default: () => {
        return {

        };
      }
    }
  },
  data() {
    return {
      saving: false,
    };
  },
  methods: {
    async save() {
      try {
        this.saving = true;
        if (this.user.id) {
          await this.$store.dispatch('user/updateUser', this.user);
        } else {
          await this.$store.dispatch('user/storeUser', this.user);
        }
        this.hide();
      } catch (e) {
        this.$refs.form.setErrors(e.response.data.errors);
      } finally {
        this.saving = false
      }
    },
    async deleteItem() {
      if(confirm(`Delete user: ${this.user.name}?`) === false) {
        return;
      }

      try {
        this.saving = true;
        await this.$store.dispatch('user/deleteUser', this.user);
        this.hide();
      } catch (e) {
        this.$refs.form.setErrors(e.response.data.errors);
      } finally {
        this.saving = false;
      }
    },
    hide() {
      this.$emit('input', false);
    },
  }
}
</script>

<style scoped>

</style>
