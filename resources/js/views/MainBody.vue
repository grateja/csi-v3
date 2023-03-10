<template>
    <!-- <div>
        <v-content>
            <toolbar />
            <v-progress-linear v-if="isCheckingUser" indeterminate></v-progress-linear>
            <router-view v-else />
            <flash-message />
        </v-content>
    </div> -->
    <div>
        <div v-if="!onLine" class="you-are-offline">You are offline! You are not connected to ELS-CSI ECCMS WiFi</div>
        <menus />
        <v-content>
            <!-- <breadcrumbs></breadcrumbs> -->
            <v-progress-linear v-if="isCheckingUser" indeterminate></v-progress-linear>
            <router-view v-else />
        </v-content>
        <v-dialog :value="!onLine" width="520">
            <v-card>
                <v-card-title>
                    <span class="title">You are offline!</span>
                </v-card-title>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import Menus from './layout/Menu.vue';
//import Toolbar from './layout/Toolbar.vue';
//import Breadcrumbs from './layout/Breadcrumbs.vue';
//import FlashMessage from './layout/FlashMessage';
export default {
    components: {
        Menus//,
        //Breadcrumbs,
        //Toolbar,
        //FlashMessage
    },
    data() {
        return {
            onLine: navigator.onLine
        }
    },
    computed: {
        isCheckingUser() {
            return this.$store.getters['auth/isLoading'];
        }
    },
    mounted() {
        console.log('ekeme')
        try {
            window.addEventListener('offline', (e) => { this.onLine = false; });
    
            window.addEventListener('online', (e) => { this.onLine = true; });

        } catch(e) {
            console.log(e)
        }
    }
}
</script>

<style scoped>
    .you-are-offline {
        background-color: black;
        color: white;
        text-align: center;
    }
</style>