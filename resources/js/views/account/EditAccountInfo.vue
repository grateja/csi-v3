<template>
    <v-container>
        <h4 class="title grey--text">Account info</h4>

        <v-divider class="my-3"></v-divider>

        <account-form @cancel="cancel" @save="save" :account-info="accountInfo"></account-form>
    </v-container>
</template>

<script>
import AccountForm from './AccountForm.vue';

export default {
    components: {
        AccountForm
    },
    data() {
        return {
            accountInfo: null
        }
    },
    methods: {
        save(data) {
            this.$store.dispatch('account/updateProfile', data).then((res, rej) => {
                this.$store.dispatch('updateName', res.data.user, {root: true});
                this.$router.push('/account');
            });
        },
        get() {
            this.$store.dispatch('account/getAccountInfo', 'self').then((res, rej) => {
                this.accountInfo = res.data.accountInfo;
            });
        },
        cancel() {
            this.$router.push('/account');
        }
    },
    created() {
        this.get();
    }
}
</script>
