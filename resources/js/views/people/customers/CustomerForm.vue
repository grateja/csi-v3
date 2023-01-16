<template>
    <form @submit.prevent="submit">
        <v-card>
            <v-card-title class="title grey--text">Customer details</v-card-title>
            <v-divider class="py-3"></v-divider>

            <v-card-text>
                <v-text-field v-model="formData.name" :error-messages="errors.get('name')" label="Name" hint="Please follow the format (Given name, MI. Surname)" persistent-hint></v-text-field>
                <v-text-field v-model="formData.address" label="Address"></v-text-field>
                <v-text-field v-model="formData.contactNumber" label="Contact No."></v-text-field>
                <v-text-field v-model="formData.email" label="Email"></v-text-field>
                <v-text-field v-model="formData.birthday" label="Birth day" type="date"></v-text-field>
            </v-card-text>

            <v-card-actions>
                <v-btn type="submit" class="primary" :loading="loading">
                    save
                </v-btn>
                <v-btn @click="cancel">
                    Cancel
                </v-btn>
            </v-card-actions>
        </v-card>
    </form>
</template>


<script>
export default {
    props: ['customer'],
    data() {
        return {
            formData: {
                name: '',
                address: '',
                contactNumber: '',
                email: '',
                birthday: ''
            },
            mode: 'insert'
        }
    },
    methods: {
        submit() {
            this.$emit('submit', {
                id: this.customerId,
                formData: this.formData,
                mode: this.mode
            });
        },
        cancel() {
            this.$store.commit('customer/clearErrors');
            this.$emit('cancel');
        }
    },
    computed: {
        loading() {
            return this.$store.getters['customer/isSaving'];
        },
        errors() {
            return this.$store.getters['customer/getErrors'];
        }
    // },
    // watch: {
    //     customer(val) {
    //         if(val) {
    //             this.mode = 'update';
    //         } else {
    //             this.mode = 'insert';
    //         }
    //     }
    }
}
</script>
