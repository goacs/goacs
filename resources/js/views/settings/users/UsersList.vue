<template>
  <CCard>
    <CCardHeader>Users
    <CButton color="dark" class="float-right" variant="outline" size="sm" @click="addUser">
      <CIcon name="cil-plus" class="btn-icon mt-0" size="sm"></CIcon> Add
    </CButton>
    </CCardHeader>
    <CCardBody>
      <PaginatedTable
        :fields="fields"
        :columnFilter='{ external: true, lazy: true }'
        action="user/list"
        :autoload="false"
        :dense="true"
        ref="table"
      >
        <template #updated_at="{ item }">
          <td>{{ item.updated_at | moment }}</td>
        </template>
        <template #actions="{ item }">
          <td>
            <CButton
              size="sm"
              color="dark"
              variant="ghost"
              @click="editUser(item)"
            >
              <CIcon name="cil-pencil"/>
            </CButton>
          </td>
        </template>
      </PaginatedTable>
    </CCardBody>
    <UserDialog v-model="dialog" :user="user"></UserDialog>
  </CCard>
</template>

<script>
  import {mapGetters} from "vuex";
  import PaginatedTable from "@/components/PaginatedTable";
  import UserDialog from "./UserDialog";
  export default {
    name: "UsersList",
    components: {UserDialog, PaginatedTable},
    computed: {
      ...mapGetters({
        user: 'user/getUser'
      }),
    },
    data() {
      return {
        dialog: false,
        fields: [
          {
            key: 'id',
            label: 'ID',
          },
          {
            key: 'name',
            label: 'Name'
          },
          {
            key: 'email',
            label: 'Email'
          },
          {
            key: 'updated_at',
            label: 'Updated at'
          },
          {
            key: 'actions',
            label: '',
            filter: false,
          }
        ]
      };
    },
    methods: {
      editUser(user) {
        this.$store.commit('user/setUser', user);
        this.dialog = true;
      },
      addUser() {
        this.$store.commit('user/setUser', {
          name: '',
          email: '',
          password: '',
          password_confirmation: '',
        });
        this.dialog = true;
      },
    },
    watch: {
      dialog(value) {
        if(!value) {
          this.$refs.table.fetchItems();
        }
      }
    }
  }
</script>

<style scoped>

</style>
