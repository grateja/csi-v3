<template>
    <v-toolbar flat dark app color="#4b7503">
        <v-toolbar-title v-if="!!user">
            <v-btn large to="/" flat class="ml-0 pl-0">
                <v-icon left large>apps</v-icon> MENU
            </v-btn>
        </v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn>keme</v-btn>

        <template v-if="!!user">
            <v-btn v-if="isOwner" @click="openShopPreferences = true" :icon="$vuetify.breakpoint.width < 580">
                <v-icon :left="$vuetify.breakpoint.width > 580">store</v-icon>
                <span v-if="$vuetify.breakpoint.width > 580">
                    shop preferences
                </span>
            </v-btn>
            <v-btn to="/account">
                <span>{{user.roles[0] | uppercase}}</span>
            </v-btn>
            <v-btn flat small @click="logout" :loading="isLoggingOut">
                 Logout<v-icon right>close</v-icon>
            </v-btn>
        </template>

        <template v-else>
            <v-btn flat router to="/login">Login</v-btn>
        </template>

        <shop-preferences-dialog v-model="openShopPreferences" />
        <rfid-terminal v-model="openRfidTerminal" />
    </v-toolbar>
</template>
<script>
import ShopPreferencesDialog from './ShopPreferencesDialog.vue';
import RfidTerminal from './RfidTerminal.vue';

export default {
    components: {
        ShopPreferencesDialog,
        RfidTerminal
    },
    data() {
        return {
            openShopPreferences: false,
            openRfidTerminal: false
        }
    },
    methods: {
        logout() {
            this.$store.dispatch('auth/logout').finally(() => {
                this.$router.push('/login');
            });
        }
    },
    computed: {
        isLoggingOut() {
            return this.$store.getters['auth/getLoggingOut'];
        },
        user() {
            return this.$store.getters.getCurrentUser;
        },
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            if(user) {
                return user.roles.some(r => r == 'admin');
            }
        }
    }
}
</script>
