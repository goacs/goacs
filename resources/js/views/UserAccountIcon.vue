<template>
  <CDropdown
    inNav
    class="c-header-nav-items"
    placement="bottom-end"
    add-menu-classes="pt-0 pb-0"
  >
    <template #toggler>
      <CHeaderNavLink>
        <div class="c-avatar">
          <img
            :src="`https://eu.ui-avatars.com/api/?background=0D8ABC&color=fff&name=${$auth.user().name}`"
            class="c-avatar-img "
          />
        </div>
      </CHeaderNavLink>
    </template>

    <CDropdownItem :to="{name: 'logout'}">
      <CIcon name="cil-lock-locked"/> Logout
    </CDropdownItem>
  </CDropdown>
</template>

<script>
export default {
  name: 'UserAccountIcon',
  data () {
    return {
      itemsCount: 42
    }
  }
}
</script>

<style scoped>
.c-icon {
  margin-right: 0.3rem;
}
</style>
