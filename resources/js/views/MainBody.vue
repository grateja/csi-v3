<template>
    <div>
        <v-content>
            <toolbar />
            <!-- <breadcrumbs></breadcrumbs> -->
            <v-progress-linear v-if="isCheckingUser" indeterminate></v-progress-linear>
            <router-view v-else />
        </v-content>
    </div>
</template>

<script>
import Toolbar from './layout/Toolbar.vue';
import Breadcrumbs from './layout/Breadcrumbs.vue';
export default {
    components: {
        Breadcrumbs,
        Toolbar
    },
    computed: {
        isCheckingUser() {
            return this.$store.getters['auth/isLoading'];
        }
    }
}
</script>
