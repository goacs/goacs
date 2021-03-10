<template>
  <div class="columns">
    <div class="column is-2">
      <aside class="menu is-hidden-mobile">
        <p class="menu-label">
          Settings
        </p>
        <ul class="menu-list">
          <li><router-link :to="{name: 'settings-main'}">Configuration</router-link></li>
          <li><router-link :to="{name: 'settings-tasks'}">Tasks</router-link></li>
        </ul>
      </aside>
    </div>
    <div class="column is-10">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
  export default {
    name: "SettingsView"
  }
</script>

<style scoped>

</style>
