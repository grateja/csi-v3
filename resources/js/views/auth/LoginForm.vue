<template>
    <form action="" method="post" @submit.prevent="login">
        <v-card color="transparent" flat>
            <v-card-title class="white--text title">
                <v-spacer></v-spacer>
                Laundry Card Management System {{ version }}
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

        <v-card class="transparent" flat>
            <v-card-title>
                <v-spacer></v-spacer>
                <!-- <v-tooltip top>
                    <v-btn flat icon slot="activator" @click="setTime">
                        <v-icon small>sync</v-icon>
                    </v-btn>
                    <span>Sync server time from device</span>
                </v-tooltip> -->
                <div>
                    <span class="caption">Server time</span>
                    <v-divider></v-divider>
                    <div>
                        {{sysDateTime}}
                    </div>
                </div>
                <v-spacer></v-spacer>
            </v-card-title>
        </v-card>
    </form>
</template>

<script>
export default {
    data() {
        return {
            email: '',
            password: '',
            rememberMe: false,
            sysDateTime: null,
            version: null
        }
    },
    methods: {
        login() {
            this.$store.dispatch('auth/loginAttempt', this.$data).then((res, rej) => {
                var cache = this.$store.getters['qrscanner/getCachedQRData']
                var url = '/';
                if(cache) {
                    url = `/new-transaction/qr?jo=${cache}`
                }
                this.$router.push(url);
            }).catch(err => {});
        },
        getSystemDateTime() {
            axios.get('/api/developer/system-date-time').then((res, rej) => {
                this.sysDateTime = new Date(res.data.sysDateTime);
                this.version = res.data.version;
            });
        },
        setTime() {
            var date = moment().format('Y-MM-D H:m:s A');
            axios.post('/api/developer/set-system-date-time', {
                date: date
            }).then((res, rej) => {

            }).finally(() => {

            })
        }
    },
    computed: {
        isLoggingIn() {
            return this.$store.getters['auth/getLoggingIn'];
        },
        errors() {
            return this.$store.getters['auth/getErrors'];
        }
    },
    created() {
        this.getSystemDateTime();
    }
}
</script>

<style scoped>
.login-form {
    margin: 20px auto;
}
</style>
