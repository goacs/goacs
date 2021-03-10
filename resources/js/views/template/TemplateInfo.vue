<template>
  <div class="card">
    <header class="card-header">
      <p class="card-header-title">
        Template Info
      </p>
    </header>
    <div class="card-content">
      <table class="table is-fullwidth is-striped">
        <tbody>
        <tr>
          <th>ID</th>
          <td>{{ template.id }}</td>
        </tr>
        <tr>
          <th>Name</th>
          <td>{{ template.name }}</td>
        </tr>
        <tr>
          <th>Used by</th>
          <td>0</td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
  import {mapGetters} from "vuex";

  export default {
    name: "TemplateInfo",
    computed: {
      ...mapGetters({
        template: 'template/getTemplate',
      }),
    },
    mounted() {
    },
    methods: {
    }
  }
</script>

<style scoped>

</style>
