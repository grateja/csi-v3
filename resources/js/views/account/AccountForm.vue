<template>
    <form method="post" @submit.prevent="save">
        <v-card class="rounded-card">
            <v-card-title class="title">Profile</v-card-title>
            <v-progress-linear v-if="loading" height="3" indeterminate></v-progress-linear>
            <v-divider v-else></v-divider>
            <v-card-text>
                <v-text-field v-model="formData.name" label="Name" :error-messages="errors.get('name')"></v-text-field>
                <v-text-field v-model="formData.contactNumber" label="Contact number" :error-messages="errors.get('contactNumber')"></v-text-field>
            </v-card-text>
            <v-card-actions>
                <v-btn class="primary" type="submit" :loading="saving" round>Save</v-btn>
                <v-btn @click="cancel" round>Cancel</v-btn>
            </v-card-actions>
        </v-card>
    </form>
</template>

<script>
export default {
    props: ['accountInfo'],
    data() {
        return {
            formData: {
                name: '',
                contactNumber: ''
            },
            mode: 'insert'
        }
    },
    methods: {
        save() {
            this.$emit('save', {
                id: this.accountInfo ? this.accountInfo.id : null,
                formData: this.formData,
                mode: this.mode
            });
        },
        cancel() {
            this.$emit('cancel');
        },
        setAccountInfo() {
            this.formData.name = this.accountInfo.name;
            this.formData.contactNumber = this.accountInfo.contact_number;
        }
    },
    computed: {
        saving() {
            return this.$store.getters['account/isSaving'];
        },
        errors() {
            return this.$store.getters['account/getErrors'];
        },
        loading() {
            return this.$store.getters['account/isLoading'];
        }
    },
    watch: {
        accountInfo(val) {
            if(val) {
                this.setAccountInfo();
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    },
    created() {
        if(this.accountInfo) {
            this.setAccountInfo();
        }
    }
}
</script>
