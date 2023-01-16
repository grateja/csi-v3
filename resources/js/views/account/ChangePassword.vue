<template>
    <v-container>
        <h4 class="title grey--text">Change password</h4>
        <v-divider class="my-3"></v-divider>

        <password-form @submit="save" @cancel="cancel"></password-form>
    </v-container>
</template>

<script>
import PasswordForm from './PasswordForm.vue';

export default {
    components: {
        PasswordForm
    },
    methods: {
        save(formData) {
            this.$store.dispatch('account/changePassword', {
                id: 'self',
                formData: formData
            }).then((res, rej) => {
                this.$router.push('/account');
            });
        },
        cancel() {
            this.$router.push('/account');
        }
    }
}
</script>
