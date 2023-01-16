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
                    <v-text-field v-model="formData.crn" :error-messages="errors.get('crn')" @keydown.native="clear('crn')" outline label="CRN" ref="crn" hint="Customer Reference Number" :disabled="loadingCRN" :loading="loadingCRN"></v-text-field>
                    <v-text-field v-model="formData.name" :error-messages="errors.get('name')" @keydown.native="clear('name')" outline label="Name" ref="name"></v-text-field>
                    <v-text-field v-model="formData.address" :error-messages="errors.get('address')" outline label="Address"></v-text-field>
                    <v-text-field v-model="formData.contactNumber" :error-messages="errors.get('contactNumber')" outline label="Contact number"></v-text-field>
                    <v-text-field v-model="formData.email" :error-messages="errors.get('email')" outline label="Email"></v-text-field>
                    <v-combobox outline v-model="formData.organization" :items="organizations" label="Organization"  item-text="organization" @input.native="search($event)"></v-combobox>
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
        'value', 'customer', 'initialName', 'preRegister'
    ],
    data() {
        return {
            mode: 'insert',
            loadingCRN: false,
            organizations: [],
            formData: {
                id: null,
                crn: null,
                remarks: null,
                name: null,
                address: null,
                contactNumber: null,
                email: null,
                organization: null,
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
        },
        clear(key) {
            this.$store.commit('customer/clearErrors', key);
        },
        search(e) {
            this.formData.organization = e.target.value;
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
    created() {
        axios.get('/api/autocomplete/organizations').then((res, rej) => {
            this.organizations = res.data;
        });
    },
    watch: {
        value(val) {
            if(val && this.customer) {
                this.mode = 'update';
                this.formData.id = this.customer.id;
                this.formData.crn = this.customer.crn;
                this.formData.remarks = this.customer.remarks;
                this.formData.name = this.customer.name;
                this.formData.address = this.customer.address;
                this.formData.contactNumber = this.customer.contact_number;
                this.formData.email = this.customer.email;
                this.formData.organization = this.customer.organization;
                this.formData.birthday = moment(this.customer.first_visit).format('YYYY-MM-DD');
                setTimeout(() => {
                    this.$refs.name.$el.querySelector('input').select();
                }, 500);
            } else {
                this.mode = 'insert';
                this.formData.id = null;
                this.formData.name = this.initialName;
                this.formData.remarks = null;
                this.formData.crn = null;
                this.formData.address = null;
                this.formData.contactNumber = null;
                this.formData.email = null;
                this.formData.organization = null;
                this.formData.birthday = moment().format('YYYY-MM-DD');
            }
            if(val && !this.customer) {
                this.getCRN();
            }
            if(this.preRegister) {
                this.getCRN();
                this.mode = 'insert';
            }
            this.clear();
        }
        // customer(val) {
        //     if(!!val) {
        //         this.mode = 'update';
        //     } else {
        //         this.mode = 'insert';
        //     }
        // }
    }
}
</script>
