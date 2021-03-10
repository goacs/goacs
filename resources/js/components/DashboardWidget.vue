<template>
  <div :class="inverse ? `card text-white bg-${color}` : 'card'">
    <div class="card-body">
      <div
        v-if="$slots.default"
        class="h1 text-muted text-right mb-4"
      >
        <slot></slot>
      </div>
      <div v-if="header" class="h4 mb-2">{{header}}</div>
      <div class="h3 mb-0 text-muted text-uppercase font-weight-bold"
        v-if="text"
      >
        {{text}}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CWidgetProgressIcon',

  props: {
    header: String,
    text: String,
    color: String,
    inverse: Boolean,
    value: {
      type: Number,
      default: 25
    }
  }
}
</script>
