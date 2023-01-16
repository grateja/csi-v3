<template>
    <v-container>
        <email-form @cancel="cancel" @save="save"></email-form>
    </v-container>
</template>

<script>
import EmailForm from './EmailForm.vue';

export default {
    components: {
        EmailForm
    },
    methods: {
        cancel() {
            this.$router.push('/account');
        },
        save(data) {
            data.id = 'self';
            this.$store.dispatch('account/updateEmail', data).then((res, rej) => {
                this.$router.push('/account');
            });
        }
    }
}
</script>
