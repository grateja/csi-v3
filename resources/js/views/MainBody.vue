<template>
    <div>
        <v-content>
            <toolbar />

            <!-- <breadcrumbs></breadcrumbs> -->
            <v-progress-linear v-if="isCheckingUser" indeterminate></v-progress-linear>
            <router-view v-else />
            <flash-message />
        </v-content>
    </div>
</template>

<script>
import Toolbar from './layout/Toolbar.vue';
import Breadcrumbs from './layout/Breadcrumbs.vue';
import FlashMessage from './layout/FlashMessage';
export default {
    components: {
        Breadcrumbs,
        Toolbar,
        FlashMessage
    },
    computed: {
        isCheckingUser() {
            return this.$store.getters['auth/isLoading'];
        }
    }
}
</script>
