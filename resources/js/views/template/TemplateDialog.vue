<template>
  <b-modal
          v-model="value"
          has-modal-card
  >
    <form>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">{{ isNew ? `Add` : `Edit` }} template</p>
          <div class="card-header-icon" aria-label="more options" v-if="isNew === false">
            <b-button
                    size="is-small"
                    type="is-danger"
                    @click="$emit('onDelete', item)"
            >
              <b-icon
                      size="is-small"
                      icon="trash-can-outline"
              >

              </b-icon>
              Delete
            </b-button>
          </div>
        </header>
        <section class="modal-card-body">
          <b-field label="Name">
            <b-input v-model="template.name"></b-input>
          </b-field>

        </section>
        <footer class="modal-card-foot">
          <b-button @click="$emit('input', false)">Close</b-button>
          <b-button type="is-primary" class="is-align-content-end" :loading="saving" @click="save">Save</b-button>
        </footer>
      </div>
    </form>
  </b-modal>
</template>

<script>
  export default {
    name: "TemplateDialog",
    props: {
      value: {
        type: Boolean,
        required: true,
      },
      isNew: {
        type: Boolean,
        default: () => false,
      }
    },
    data() {
      return {
        saving: false,
        template: {
          name: '',
        }
      }
    },
    methods: {
      save() {
        this.$emit('onSave', this.template)
      }
    }
  }
</script>

<style scoped>

</style>
