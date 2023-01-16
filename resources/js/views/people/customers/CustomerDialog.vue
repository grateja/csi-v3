<template>
    <v-dialog :value="value" persistent max-width="640">
        <!-- <customer-form @submit="save" @cancel="cancel"></customer-form> -->
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

    </v-dialog>
</template>

<script>
// import CustomerForm from './CustomerForm.vue';

export default {
    props: [
        'value',
        'customer'
    ],
    // components: {
    //     CustomerForm
    // },
    data() {
        return {
            formData: {
                name: '',
                address: '',
                contactNumber: '',
                email: '',
                birthday: new Date().toISOString().substring(0, 10)
            },
            mode: 'insert'
        }
    },
    methods: {
        // submit() {
        //     this.$emit('submit', {
        //         id: this.customerId,
        //         formData: this.formData,
        //         mode: this.mode
        //     });
        // },
        cancel() {
            this.$store.commit('customer/clearErrors');
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`customer/${this.mode}Customer`, {
                customerId: this.customer ? this.customer.id : null,
                formData: this.formData
            }).then((res, rej) => {
                console.log(res.data.customer);
                this.$emit('save', {
                    customer: res.data.customer,
                    mode: this.mode
                });
                this.$emit('input', false);
            });
        }
    },
    computed: {
        loading() {
            return this.$store.getters['customer/isSaving'];
        },
        errors() {
            return this.$store.getters['customer/getErrors'];
        }
    },
    watch: {
        customer(val) {
            if(val) {
                this.mode = 'update';
                this.formData.name = val.name;
                this.formData.contactNumber = val.contact_number;
                this.formData.address = val.address;
                this.formData.email = val.email;
                this.formData.birthday = new Date(val.birthday).toISOString().substr(0, 10)
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
