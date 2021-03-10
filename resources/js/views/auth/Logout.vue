<template>
    <div></div>
</template>

<script>
export default {
    name: "Logout",
    data: () => ({

    }),
    mounted() {
        this.logout();
    },
    methods: {
        async logout() {
            this.$auth.logout();
        }
    },
}
</script>

<style scoped>
</style>
