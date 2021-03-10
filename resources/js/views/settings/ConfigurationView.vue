<template>
  <div class="card">
    <header class="card-header">
      <p class="card-header-title">
        Configuration
      </p>
    </header>
    <div class="card-content">
      <b-notification
              v-if="saved"
              type="is-info"
              aria-close-label="Close notification">
        Please restart GoACS server application to load changes
      </b-notification>

      <b-field label="Periodic Inform Interval Spread">
        <b-input type="text" v-model="config.ppi"></b-input>
      </b-field>
      <b-field>
        <p class="control">
          <b-button class="button is-primary" :loading="saving" @click="save">
            Save
          </b-button>
        </p>
      </b-field>
    </div>
  </div>
</template>

<script>
  export default {
    name: "ConfigurationView",
    data() {
      return {
        saving: false,
        saved: false,
      };
    },
    computed: {
      config: {
        get() {
          return this.$store.getters['config/getConfig']
        },
        set(config) {
          this.$store.commit('config/setConfig', config)
        }
      },
    },
    methods: {
      save() {
        try {
          this.saving = true
          this.$store.dispatch('config/saveConfig', this.config);
          this.saved = true
          setTimeout(() => this.saved = false, 5000);
        } catch (e) {
          this.$buefy.toast.open({
            duration: 5000,
            message: `Cannot save config: ${e.response.data.message}`,
            position: 'is-bottom',
            type: 'is-danger'
          })
        } finally {
          this.saving = false
          this.$buefy.toast.open({
            duration: 5000,
            message: `Config saved`,
            position: 'is-bottom',
            type: 'is-success'
          })
        }
      }
    },
    async beforeMount() {
      try {
        const response = await this.$store.dispatch('config/getConfig')
        this.$store.commit('config/setConfig', response.data.data)
      } catch (e) {
        this.$buefy.toast.open({
          duration: 5000,
          message: `Cannot fetch config data`,
          position: 'is-bottom',
          type: 'is-danger'
        })
      }
    }

  }
</script>

<style scoped>

</style>
