<template>
  <b-modal
          v-model="value"
          has-modal-card
  >
    <form>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Edit template</p>
          <b-button
                  size="is-small"
                  type="is-danger"
                  @click="unassign"
          >
            <b-icon
                    size="is-small"
                    icon="delete"
            >

            </b-icon>
            Unassign
          </b-button>

        </header>
        <section class="modal-card-body">
          <b-field :label="`Edit template assignment ${item.name}`">

          </b-field>
          <b-field label="Priority" label-position="on-border">
            <b-input
                    type="text"
                    v-model.number="item.priority"
                    placeholder="Priority">
            </b-input>
          </b-field>
        </section>
        <footer class="modal-card-foot">
          <b-button @click="$emit('input', false)">Close</b-button>
          <b-button type="is-primary" class="is-align-content-end" @click="save" :loading="saving">Save</b-button>
        </footer>
      </div>
    </form>
  </b-modal>
</template>

<script>
  export default {
    name: "EditDialog",
    props: {
      value: {
        type: Boolean,
      },
      item: {
        type: Object,
        required: true,
      }
    },
    data() {
      return {
        saving: false,
        deleting: false,
      }
    },
    methods: {
      save() {
        this.saving = true
        const params = {
          device_id: this.$route.params.id,
          template_id: this.item.template_id,
          priority: this.item.priority,
        };
        console.log(params);
        try {
          this.$store.dispatch('device/unAssignTemplate', params)
          this.$store.dispatch('device/assignTemplate', params)
          this.$emit('input', false);
          setTimeout(() => this.$store.dispatch('device/fetchDeviceTemplates', params.device_id), 500)
        } catch (e) {
          this.$buefy.toast.open({
            type: 'is-danger',
            position: 'is-bottom',
            message: "Cannot edit template"
          })
        } finally {
          this.saving = false
        }

        },
      async unassign() {
        this.deleting = true;
        const params = {
          device_id: this.$route.params.id,
          template_id: this.item.id,
          priority: this.item.priority,
        };

        try {
          await this.$store.dispatch('device/unAssignTemplate', params)
          this.$emit('input', false);
          this.$store.dispatch('device/fetchDeviceTemplates', params.device_id)
        } catch (e) {
          this.$buefy.toast.open({
            type: 'is-danger',
            position: 'is-bottom',
            message: "Cannot unassign template"
          })
        } finally {
          this.deleting = false
        }

      }
    }
  }
</script>

<style scoped>

</style>
