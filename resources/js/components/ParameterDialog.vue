<template>
  <b-modal
          v-model="value"
          has-modal-card
          :canCancel="false"
  >
    <form>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">{{ isNew ? `Add` : `Edit` }} parameter</p>
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
          <b-field label="Name" label-position="on-border">
            <b-input
                    type="text"
                    v-model="item.name"
                    placeholder="Name">
            </b-input>
          </b-field>
          <b-field label="Value" label-position="on-border">
            <b-input
                    type="text"
                    v-model="item.value"
                    placeholder="Value">
            </b-input>
          </b-field>
          <b-field label="Type" label-position="on-border">
            <b-input
                type="text"
                v-model="item.type"
                placeholder="Type">
            </b-input>
          </b-field>
          <b-field label="Flags">
            <FlagInput v-model="item.flags"></FlagInput>
          </b-field>
        </section>
        <footer class="modal-card-foot">
          <b-button @click="$emit('input', false)">Close</b-button>
          <b-button type="is-primary" class="is-align-content-end" :disabled="!canSave" @click="save" :loading="saving">Save</b-button>
        </footer>
      </div>
    </form>
  </b-modal>
</template>

<script>
  import FlagInput from "./FlagInput";
  export default {
    name: "ParameterDialog",
    components: {FlagInput},
    data() {
      return {
        saving: false,
      }
    },
    props: {
      value: {
        type: Boolean,
        required: true,
      },
      item: {
        type: Object,
        required: true,
      },
      isNew: {
        type: Boolean,
        default: () => false,
      }
    },
    computed: {
      canSave() {
        return this.item.name !== ""
      }
    },
    methods: {
      save() {
        this.saving = true
        this.$emit('onSave', this.item)
      }
    },
    watch: {
      value() {
        this.saving = false;
      }
    }
  }
</script>

<style scoped>

</style>
