<template>
    <div>
        <v-content>
            <breadcrumbs></breadcrumbs>
            <v-progress-linear v-if="isCheckingUser" indeterminate></v-progress-linear>
            <router-view v-else />
        </v-content>
    </div>
</template>

<script>
import Breadcrumbs from './layout/Breadcrumbs.vue';
export default {
    components: {
        Breadcrumbs,
    },
    computed: {
        isCheckingUser() {
            return this.$store.getters['auth/isLoading'];
        }
    }
}
</script>
