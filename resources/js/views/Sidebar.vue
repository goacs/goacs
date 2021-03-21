<template>
  <CSidebar
    fixed
    :minimize="minimize"
    :show="show"
    @update:show="(value) => $store.commit('set', ['sidebarShow', value])"
  >
    <CSidebarBrand class="d-md-down-none" to="/">
      GOACS
    </CSidebarBrand>
    <CSidebarNav>
      <CSidebarNavItem
        name="Dashboard"
        :to="{ path: '/dashboard' }"
      />
      <CSidebarNavItem
        name="Devices"
        :to="{ path: '/devices' }"
      />
      <CSidebarNavItem
        name="Templates"
        :to="{ path: '/templates' }"
      />
      <CSidebarNavItem
        name="Files"
        :to="{ path: '/files' }"
      />
      <CSidebarNavDropdown
        name="Settings"
      >
        <CSidebarNavItem
          name="Base"
          :to="{ path: '/settings' }"
        />
        <CSidebarNavItem
          name="Global tasks"
          :to="{ path: '/settings/tasks' }"
        >
        </CSidebarNavItem>
        <CSidebarNavItem
          name="Users"
          :to="{ path: '/settings/users' }"
        >
        </CSidebarNavItem>
      </CSidebarNavDropdown>

    </CSidebarNav>
    <CSidebarMinimizer
      class="d-md-down-none"
      @click.native="$store.commit('set', ['sidebarMinimize', !minimize])"
    />
  </CSidebar>
</template>

<script>
export default {
  name: "Sidebar",
  computed: {
    show () {
      return this.$store.state.sidebarShow
    },
    minimize () {
      return this.$store.state.sidebarMinimize
    }
  }
}
</script>

<style scoped>

</style>
