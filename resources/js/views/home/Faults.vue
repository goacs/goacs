<template>
  <div class="card events-card">
    <header class="card-header">
      <p class="card-header-title">
        Faults
      </p>
<!--      <a href="#" class="card-header-icon" aria-label="more options">-->
<!--                  <span class="icon">-->
<!--                    <i class="fa fa-angle-down" aria-hidden="true"></i>-->
<!--                  </span>-->
<!--      </a>-->
    </header>
    <div class="card-table">
      <div class="content">
        <table class="table is-fullwidth is-striped">
          <thead>
          <th>Code</th>
          <th>Message</th>
          <th>Date</th>
          <th>CPE</th>
          <th></th>
          </thead>
          <tbody>
            <template v-if="faults.length === 0">
              <tr>
                <td colspan="5" class="has-text-centered">Nothing to display</td>
              </tr>
            </template>
            <template v-else>
              <tr v-for="fault in faults" :key="fault.uuid">
                <td>{{ fault.code }}</td>
                <td>{{ fault.message }}</td>
                <td>{{ fault.created_at }}</td>
                <td><router-link :to="{ name: 'devices-view', params: {uuid: fault.cpe_uuid}}">{{ fault.cpe_uuid }}</router-link></td>
                <td class="level-right"><a class="button is-small is-primary" href="#">Details</a></td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
    <footer class="card-footer">
      <a href="#" class="card-footer-item">View All</a>
    </footer>
  </div>
</template>

<script>
  export default {
    name: "Faults",
    props: {
      faults: {
        type: Array,
        default: () => [],
      },
    },
    data() {
      return {

      }
    },
    mounted() {

    }
  }
</script>

<style scoped>

</style>
