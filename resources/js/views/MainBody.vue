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
        <div v-if="!isOnline" class="you-are-offline">You are offline! You are not connected to ELS-CSI ECCMS WiFi</div>
        <menus />
        <v-content>
            <!-- <breadcrumbs></breadcrumbs> -->
            <v-progress-linear v-if="isCheckingUser" indeterminate></v-progress-linear>
            <router-view v-else />
        </v-content>
        <v-dialog :value="!isOnline" width="520">
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
            // isOnline: navigator.onLine
        }
    },
    computed: {
        isCheckingUser() {
            return this.$store.getters['auth/isLoading'];
        },
        isOnline() {
            return this.$store.getters.isOnline;
        },
        // checkInternetConnection() {
        //     console.log("testing")
        //     axios.get("https://www.google.com", {
        //         method: "HEAD",
        //         mode: "no-cors"
        //     }).then((res, rej) => {
        //         this.$store.commit('setOnlineStatus', true);
        //     }).catch(e => {
        //         this.$store.commit('setOnlineStatus', false);
        //     });
        // }
    },
    mounted() {
        console.log('ekeme')
        try {
            window.addEventListener('offline', (e) => {
                this.$store.commit('setOnlineStatus', false);
                console.log("offlkine")
                // this.onLine = false;
            });

            window.addEventListener('online', (e) => {
                console.log("online")
                // this.checkInternetConnection();
                this.$store.commit('setOnlineStatus', true);
              // this.onLine = true;
            });

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
