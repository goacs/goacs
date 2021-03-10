<template>
  <div class="c-app">
    <Sidebar/>
    <CWrapper>
      <Header/>
      <div class="c-body">
        <main class="c-main">
          <CContainer fluid>
            <transition name="fade" mode="out-in">
              <router-view :key="$route.path"></router-view>
            </transition>
          </CContainer>
        </main>
      </div>
<!--      <TheFooter/>-->
    </CWrapper>
  </div>
</template>

<script>
import Sidebar from "./Sidebar";
import Header from "./Header";
export default {
  name: "Base",
  components: {Header, Sidebar},
  data() {
    return {
      drawer: null,
    }
  },
    computed: {
      user() {
          return this.$auth.user();
      }
    }
}
</script>

<style scoped>

</style>
