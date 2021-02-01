<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card class="rounded-card">
                <v-card-title>
                    <span class="title">Customer info</span>
                    <v-spacer></v-spacer>
                    <v-btn icon small @click="close">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-text-field v-model="formData.crn" :error-messages="errors.get('crn')" outline label="CRN" ref="crn" hint="Customer Reference Number" :disabled="loadingCRN" :loading="loadingCRN"></v-text-field>
                    <v-text-field v-model="formData.name" :error-messages="errors.get('name')" outline label="Name" ref="name"></v-text-field>
                    <v-text-field v-model="formData.address" :error-messages="errors.get('address')" outline label="Address"></v-text-field>
                    <v-text-field v-model="formData.contactNumber" :error-messages="errors.get('contactNumber')" outline label="Contact number"></v-text-field>
                    <v-text-field v-model="formData.email" :error-messages="errors.get('email')" outline label="Email"></v-text-field>
                    <v-text-field v-model="formData.birthday" :error-messages="errors.get('birthday')" outline label="Birthday" type="date"></v-text-field>
                    <v-text-field v-model="formData.remarks" :error-messages="errors.get('remarks')" outline label="Remarks"></v-text-field>
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
        'value', 'customer', 'initialName'
    ],
    data() {
        return {
            mode: 'insert',
            loadingCRN: false,
            formData: {
                crn: null,
                remarks: null,
                name: null,
                address: null,
                contactNumber: null,
                email: null,
                birthday: moment().format('YYYY-MM-DD')
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
        },
        getCRN() {
            this.loadingCRN = true;
            this.$store.dispatch('customer/getCRN').then((res, rej) => {
                console.log(res.data);
                this.formData.crn = res.data;
                this.$refs.name.$el.querySelector('input').select();
            }).finally(() => {
                this.loadingCRN = false;
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
        value(val) {
            if(val && this.customer) {
                this.mode = 'update';
                this.formData.crn = this.customer.crn;
                this.formData.remarks = this.customer.remarks;
                this.formData.name = this.customer.name;
                this.formData.address = this.customer.address;
                this.formData.contactNumber = this.customer.contact_number;
                this.formData.email = this.customer.email;
                this.formData.birthday = moment(this.customer.first_visit).format('YYYY-MM-DD');
                setTimeout(() => {
                    this.$refs.name.$el.querySelector('input').select();
                }, 500);
            } else {
                this.mode = 'insert';
                this.formData.name = this.initialName;
                this.formData.remarks = null;
                this.formData.crn = null;
                this.formData.address = null;
                this.formData.contactNumber = null;
                this.formData.email = null;
                this.formData.birthday = moment().format('YYYY-MM-DD');
            }
            if(val && !this.customer) {
                this.getCRN();
            }
        },
        customer(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
