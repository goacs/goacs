<template>
  <tr>
    <td>{{ task.id }}</td>
    <td v-if="task.for_id === 0">{{ task.for_type }}</td>
    <td v-else>{{ task.for_type }} {{ task.for_id }}</td>
    <td>{{ task.on_request }}</td>
    <td>{{ task.name }}</td>
    <td>{{ stripString(task.payload, 50) }}</td>
    <td>{{ task.infinite }}</td>
    <td>
      <CButton
        size="sm"
        color="primary"
        variant="ghost"
        @click="$parent.editTask(task)"
      >
        <CIcon name="cil-pencil"/>
      </CButton>
    </td>
  </tr>
</template>

<script>
  export default {
    name: "TaskRow",
    props: {
      task: {
        type: Object,
        required: true,
      }
    },
    methods: {
      stripString(value, len) {
        if(!value) {
          return
        }
        value = JSON.stringify(value)
        return value.substr(0, len)+"...";
      },
    }
  }
</script>

<style scoped>

</style>
