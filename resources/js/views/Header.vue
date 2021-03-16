<template>
  <CHeader fixed with-subheader light>
    <CToggler
      in-header
      class="ml-3 d-lg-none"
      @click="$store.commit('toggleSidebarMobile')"
    />
    <CToggler
      in-header
      class="ml-3 d-md-down-none"
      @click="$store.commit('toggleSidebarDesktop')"
    />
    <CHeaderBrand class="mx-auto d-lg-none" to="/">
      GOACS
<!--      <CIcon name="logo" height="48" alt="Logo"/>-->
    </CHeaderBrand>
    <CHeaderNav class="d-md-down-none mr-auto">
<!--      <CBreadcrumbRouter class="border-0 mb-0"/>-->
    </CHeaderNav>
    <CHeaderNav class="mr-4">
      <CHeaderNavItem class="d-md-down-none mx-2">
        <CHeaderNavLink>
          <CIcon name="cil-bell"/>
        </CHeaderNavLink>
      </CHeaderNavItem>
      <UserAccountIcon></UserAccountIcon>
    </CHeaderNav>

  </CHeader>
</template>

<script>
import UserAccountIcon from "./UserAccountIcon";
export default {
  name: "Header",
  components: {UserAccountIcon}
}
</script>

<style scoped>

</style>
