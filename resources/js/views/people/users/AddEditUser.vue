<template>
    <v-container>
        <h4 class="title grey--text">User info</h4>

        <v-divider class="my-3"></v-divider>

        <account-form @cancel="cancel" @save="save" :account-info="accountInfo"></account-form>
    </v-container>
</template>

<script>
import AccountForm from '../../account/AccountForm.vue';

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
            this.$store.dispatch(`account/${data.mode}Profile`, data).then((res, rej) => {
                this.$router.push('/people/users');
            });
        },
        get() {
            if(this.$route.params.id) {
                this.$store.dispatch('account/getAccountInfo', this.$route.params.id).then((res, rej) => {
                    this.accountInfo = res.data.accountInfo;
                });
            }
        },
        cancel() {
            this.$router.push('/people/users');
        }
    },
    created() {
        this.get();
    }
}
</script>
