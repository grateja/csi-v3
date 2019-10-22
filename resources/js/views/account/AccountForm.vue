<template>
    <form method="post" @submit.prevent="save">
        <v-card>
            <v-card-title class="title">Profile</v-card-title>
            <v-progress-linear v-if="loading" height="3" indeterminate></v-progress-linear>
            <v-divider v-else></v-divider>
            <v-card-text>

                <v-layout row wrap>
                    <v-flex xs-4>
                        <v-text-field v-model="formData.lastname" label="Last name" :error-messages="errors.get('lastname')"></v-text-field>
                    </v-flex>
                    <v-flex xs-4>
                        <v-text-field v-model="formData.firstname" label="First name" :error-messages="errors.get('firstname')"></v-text-field>
                    </v-flex>
                    <v-flex xs-4>
                        <v-text-field v-model="formData.middlename" label="Middle name"></v-text-field>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs-6>
                        <v-text-field v-model="formData.contactNumber" label="Contact number" :error-messages="errors.get('contactNumber')"></v-text-field>
                    </v-flex>
                    <v-flex xs-6>
                        <v-text-field v-model="formData.address" label="Address"></v-text-field>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs-6>
                        <vuetify-autocomplete v-model="formData.barangay" label="Barangay" url="/api/autocomplete/barangays"></vuetify-autocomplete>
                    </v-flex>
                    <v-flex xs-6>
                        <vuetify-autocomplete v-model="formData.cityMunicipality" label="City/Municipality" url="/api/autocomplete/city-municipalities" :error-message="errors.get('cityMunicipality')"></vuetify-autocomplete>
                    </v-flex>
                </v-layout>

                <template v-if="mode == 'insert'">
                    <v-layout>
                        <v-flex xs-6>
                            <v-text-field v-model="formData.email" label="Email" :error-messages="errors.get('email')" hint="This email address will be used for log in"></v-text-field>
                        </v-flex>
                        <v-flex xs-6>
                            <v-text-field v-model="formData.email_confirmation" label="Retype email" :error-messages="errors.get('email_confirmation')"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout>
                        <v-flex xs-6>
                            <v-text-field type="password" v-model="formData.password" label="Password" :error-messages="errors.get('password')"></v-text-field>
                        </v-flex>
                        <v-flex xs-6>
                            <v-text-field type="password" v-model="formData.password_confirmation" label="Retype password" :error-messages="errors.get('password_confirmation')"></v-text-field>
                        </v-flex>
                    </v-layout>
                </template>

            </v-card-text>
            <v-card-actions>
                <v-btn class="primary" type="submit" :loading="saving">Save</v-btn>
                <v-btn @click="cancel">Cancel</v-btn>
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
                lastname: '',
                firstname: '',
                middlename: '',
                contactNumber: '',
                email: '',
                address: '',
                barangay: '',
                cityMunicipality: ''
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
            this.formData.lastname = this.accountInfo.lastname;
            this.formData.firstname = this.accountInfo.firstname;
            this.formData.middlename = this.accountInfo.middlename;
            this.formData.address = this.accountInfo.address;
            this.formData.contactNumber = this.accountInfo.contact_number;

            if(this.accountInfo.barangay) {
                this.formData.barangay = this.accountInfo.barangay.name;
            }

            if(this.accountInfo.city_municipality) {
                this.formData.cityMunicipality = this.accountInfo.city_municipality.name;
            }
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
