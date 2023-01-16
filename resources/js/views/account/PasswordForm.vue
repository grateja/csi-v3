<template>
    <form @submit.prevent="submit">

        <v-card class="rounded-card">
            <v-card-title class="title grey--text">Change password</v-card-title>
            <v-card-text>
                <v-text-field type="password" label="Old password" v-model="formData.oldPassword" :error-messages="errors.get('oldPassword')"></v-text-field>

                <v-text-field type="password" label="New password" v-model="formData.password" :error-messages="errors.get('password')"></v-text-field>

                <v-text-field type="password" label="Password confirmation" v-model="formData.password_confirmation" :error-messages="errors.get('password_confirmation')"></v-text-field>
            </v-card-text>
            <v-card-actions>
                <v-btn type="submit" class="primary" :loading="saving" round>Save</v-btn>
                <v-btn @click="cancel" round>Cancel</v-btn>
            </v-card-actions>
        </v-card>

    </form>
</template>

<script>
export default {
    data() {
        return {
            formData: {
                oldPassword: '',
                password: '',
                password_confirmation: ''
            }
        }
    },
    methods: {
        cancel() {
            this.$emit('cancel');
        },
        submit() {
            this.$emit('submit', this.formData);
        }
    },
    computed: {
        saving() {
            return this.$store.getters['account/isSaving'];
        },
        errors() {
            return this.$store.getters['account/getErrors'];
        }
    },
    beforeDestroy() {
        this.$store.commit('account/clearErrors');
    }
}
</script>
