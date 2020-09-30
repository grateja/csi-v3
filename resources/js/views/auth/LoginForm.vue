<template>
    <form action="" method="post" @submit.prevent="login">
        <v-card color="transparent" flat>
            <v-card-title class="white--text title">
                <v-spacer></v-spacer>
                Laundry Card Management System
                <v-spacer></v-spacer>
            </v-card-title>
        </v-card>
        <v-card max-width="400" class="rounded-card translucent login-form">
            <v-card-text class="pa-4">

                <v-text-field
                    name="email"
                    label="Email"
                    id="email"
                    append-icon="people"
                    v-model="email"
                    :error-messages="errors.get('email')"
                ></v-text-field>

                <v-text-field
                    name="password"
                    label="Password"
                    id="password"
                    append-icon="lock"
                    type="password"
                    v-model="password"
                ></v-text-field>

                <v-checkbox label="Remember me" v-model="rememberMe"></v-checkbox>
                <v-card-actions>
                    <v-spacer></v-spacer>
                        <v-btn round type="submit" flat class="primary ma-0" :loading="isLoggingIn">Log in</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>


            </v-card-text>
        </v-card>
    </form>
</template>

<script>
export default {
    data() {
        return {
            email: '',
            password: '',
            rememberMe: false
        }
    },
    methods: {
        login() {
            this.$store.dispatch('auth/loginAttempt', this.$data).then((res, rej) => {
                this.$router.push('/');
            }).catch(err => {});
        }
    },
    computed: {
        isLoggingIn() {
            return this.$store.getters['auth/getLoggingIn'];
        },
        errors() {
            return this.$store.getters['auth/getErrors'];
        }
    }
}
</script>

<style scoped>
.login-form {
    margin: 20px auto;
}
</style>
