<template>
    <v-toolbar flat dark app color="#4b7503">
        <v-toolbar-title v-if="!!user">
            <v-btn large to="/" flat class="ml-0 pl-0">
                <v-icon left large>apps</v-icon> MENU
            </v-btn>
        </v-toolbar-title>

        <v-spacer></v-spacer>

        <template v-if="!!user">
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

    </v-toolbar>
</template>
<script>
export default {
    methods: {
        logout() {
            this.$store.dispatch('auth/logout').then((res, rej) => {
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
        }
    }
}
</script>
