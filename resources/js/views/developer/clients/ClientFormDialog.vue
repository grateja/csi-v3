<template>
    <v-dialog :value="value" persistent max-width="640px">
        <form @submit.prevent="save">
            <v-card>
                <v-card-title class="title">Update client details</v-card-title>
                <v-card-text>
                    <v-layout row wrap>
                        <v-flex xs4>
                            <v-text-field v-model="formData.lastname" label="Last name" :error-messages="errors.get('lastname')" @input="errors.clear('lastname')"></v-text-field>
                        </v-flex>
                        <v-flex xs4>
                            <v-text-field v-model="formData.firstname" label="First name" :error-messages="errors.get('firstname')" @input="errors.clear('firstname')"></v-text-field>
                        </v-flex>
                        <v-flex xs4>
                            <v-text-field v-model="formData.middlename" label="Middle name"></v-text-field>
                        </v-flex>
                    </v-layout>

                    <v-layout row wrap>
                        <v-flex xs6>
                            <v-text-field v-model="formData.contactNumber" label="Contact number" :error-messages="errors.get('contactNumber')" @input="errors.clear('contactNumber')"></v-text-field>
                        </v-flex>
                        <v-flex xs6>
                            <v-text-field v-model="formData.email" label="Email" :error-messages="errors.get('email')" hint="This email address will be used for client log in" @input="errors.clear('email')"></v-text-field>
                        </v-flex>
                    </v-layout>

                    <v-text-field v-model="formData.address" label="Address"></v-text-field>

                    <vuetify-autocomplete v-model="formData.barangay" url="/api/autocomplete/barangays" label="Barangay"></vuetify-autocomplete>

                    <vuetify-autocomplete v-model="formData.cityMunicipality" url="/api/autocomplete/city-municipalities" label="City / Municipality" :error-message="errors.get('cityMunicipality')" @input="errors.clear('cityMunicipality')"></vuetify-autocomplete>

                </v-card-text>
                <v-card-actions>
                    <v-btn type="submit" class="primary" :loading="loading">Save</v-btn>
                    <v-btn @click="cancel">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'client'
    ],
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
            }
        }
    },
    methods: {
        cancel() {
            this.$emit('input', false);
            this.$store.commit('client/clearErrors');
        },
        save() {
            this.$store.dispatch(`client/updateClientDetails`, {
                clientId: this.client.user_id,
                formData: this.formData
            }).then((res, rej) => {
                console.log(res);
                this.$emit('save', res.data.user);
                this.cancel();
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['client/getErrors'];
        },
        loading() {
            return this.$store.getters['client/isUpdating'];
        }
    },
    watch: {
        client(val) {
            console.log('val', val);
            if(val) {
                this.formData.lastname = val.user.lastname;
                this.formData.firstname = val.user.firstname;
                this.formData.middlename = val.user.middlename;
                this.formData.contactNumber = val.user.contact_number;
                this.formData.email = val.user.email;
                this.formData.address = val.user.address;
                if(val.user.barangay) {
                    this.formData.barangay = val.user.barangay.name;
                } else {
                    this.formData.barangay = '';
                }
                if(val.user.city_municipality) {
                    this.formData.cityMunicipality = val.user.city_municipality.name;
                } else {
                    this.formData.cityMunicipality = '';
                }
            } else {
                this.formData.lastname = '';
                this.formData.firstname = '';
                this.formData.middlename = '';
                this.formData.contactNumber = '';
                this.formData.email = '';
            }
        }
    }
}
</script>
