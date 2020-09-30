<template>
    <form @submit.prevent="save">
        <v-card class="rounded-card">
            <v-card-title class="title grey--text">Update email</v-card-title>
            <v-divider class="py-3"></v-divider>

            <v-card-text>
                <v-text-field v-model="formData.email" :error-messages="errors.get('email')" label="Enter new email" append-icon="email" hint="Please keep in mind that emails are used to log in. Make sure the email is active"></v-text-field>
                <v-text-field v-model="formData.password" :error-messages="errors.get('password')" type="password" label="Password" hint="Password is required"></v-text-field>
            </v-card-text>

            <v-card-actions>
                <v-btn type="submit" class="primary" :loading="loading" round>
                    save
                </v-btn>
                <v-btn @click="cancel" round>
                    Cancel
                </v-btn>
            </v-card-actions>
        </v-card>
    </form>
</template>


<script>
export default {
    props: ['userId'],
    data() {
        return {
            formData: {
                email: '',
                password: ''
            }
        }
    },
    methods: {
        save() {
            this.$emit('save', {
                id: this.userId,
                formData: this.formData
            });
        },
        cancel() {
            this.$store.commit('account/clearErrors');
            this.$emit('cancel');
        }
    },
    computed: {
        loading() {
            return this.$store.getters['account/isUpdatingEmail'];
        },
        errors() {
            return this.$store.getters['account/getErrors'];
        }
    },
    created() {
        this.formData.email = this.$store.getters.getCurrentUser.email;
    }
}
</script>
