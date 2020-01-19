<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Customer info</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.name" :error-messages="errors.get('name')" outline label="Name"></v-text-field>
                    <v-text-field v-model="formData.address" :error-messages="errors.get('address')" outline label="Address"></v-text-field>
                    <v-text-field v-model="formData.contactNumber" :error-messages="errors.get('contactNumber')" outline label="Contact number"></v-text-field>
                    <v-text-field v-model="formData.email" :error-messages="errors.get('email')" outline label="Email"></v-text-field>
                    <v-text-field v-model="formData.firstVisit" :error-messages="errors.get('firstVisit')" outline label="Birthday" type="date"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit" round :loading="saving">Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'customer'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                name: null,
                address: null,
                contactNumber: null,
                email: null,
                firstVisit: moment().format('YYYY-DD-MM')
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`customer/${this.mode}Customer`, {
                customerId: this.customer ? this.customer.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    customer: res.data.customer,
                    mode: this.mode
                });
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['customer/getErrors'];
        },
        saving() {
            return this.$store.getters['customer/isSaving'];
        }
    },
    watch: {
        customer(val) {
            if(val) {
                this.mode = 'update';
                this.formData.name = val.name;
                this.formData.address = val.address;
                this.formData.contactNumber = val.contactNumber;
                this.formData.email = val.email;
                this.formData.firstVisit = moment(val.first_visit).format('YYYY-MM-DD');
            } else {
                this.mode = 'insert';
                this.formData.name = null;
                this.formData.address = null;
                this.formData.contactNumber = null;
                this.formData.email = null;
                this.formData.firstVisit = moment().format('YYYY-MM-DD');
            }
        }
    }
}
</script>
