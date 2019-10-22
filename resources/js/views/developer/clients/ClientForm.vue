<template>
    <form @submit.prevent="submit" class="frm-medium">
        <v-card>
            <v-card-text>
                <h4 class="title">Client details</h4>
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

                <v-divider class="transparent my-3"></v-divider>
                <h4 class="title">Branch details</h4>
                <v-text-field v-model="formData.shopName" label="Shop name" :error-messages="errors.get('shopName')" @input="errors.clear('shopName')"></v-text-field>

                <v-layout row wrap>
                    <v-flex xs-6>
                        <v-text-field v-model="formData.shopContactNo" label="Contact number" :error-messages="errors.get('shopContactNo')" @input="errors.clear('shopContactNo')"></v-text-field>
                    </v-flex>

                    <v-flex xs-6>
                        <v-text-field v-model="formData.shopEmail" label="Email address" :error-messages="errors.get('shopEmail')" @input="errors.clear('shopEmail')"></v-text-field>
                    </v-flex>
                </v-layout>

                <vuetify-autocomplete v-model="formData.shopCityMunicipality" url="/api/autocomplete/city-municipalities" label="City / Municipality" :error-message="errors.get('shopCityMunicipality')" @input="errors.clear('shopCityMunicipality')"></vuetify-autocomplete>

                <v-text-field v-model="formData.shopAddress" label="Address" :error-messages="errors.get('shopAddress')" @input="errors.clear('shopAddress')"></v-text-field>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn class="primary" type="submit" :loading="loading">Save</v-btn>
                <v-btn class="default" type="button" @click="cancel">Cancel</v-btn>
            </v-card-actions>

        </v-card>
    </form>
</template>

<script>
export default {
    props: [
        'branch'
    ],
    data() {
        return {
            formData: {
                lastname: '',
                firstname: '',
                middlename: '',
                email: '',
                address: '',
                barangay: '',
                cityMunicipality: '',
                contactNumber: '',
                // shop details
                shopName: '',
                shopContactNo: '',
                shopEmail: '',
                shopAddress: '',
                shopCityMunicipality: ''
            },
            mode: 'insert',
            branchId: null
        }
    },
    methods: {
        submit() {
            if(this.loading) return;

            this.$emit('submit', {
                branchId: this.branchId,
                formData: this.formData,
                mode: this.mode
            });
        },
        cancel() {
            this.$emit('cancel');
        }
    },
    computed: {
        errors() {
            return this.$store.getters['client/getErrors'];
        },
        loading() {
            return this.$store.getters['client/isSaving'];
        }
    },
    watch: {
        branch(val) {
            if(val) {
                this.mode = 'update';
                this.branchId = val.id;
                this.formData.lastname = val.client.user.lastname;
                this.formData.firstname = val.client.user.firstname;
                this.formData.middlename = val.client.user.middlename;
                this.formData.contactNumber = val.client.user.contact_number;
                this.formData.email = val.client.user.email;
                this.formData.address = val.client.user.address;

                if(val.client.user.barangay) {
                    this.formData.barangay = val.client.user.barangay.name;
                }
                if(val.client.user.city_municipality) {
                    this.formData.cityMunicipality = val.client.user.city_municipality.name;
                }

//
                this.formData.shopName = val.name;
                this.formData.shopContactNo = val.contact_no;

                if(val.city_municipality) {
                    this.formData.shopCityMunicipality = val.city_municipality.name;
                }

                this.formData.shopEmail = val.email;
                this.formData.shopAddress = val.address;

            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>

<style>
.frm-medium {
    max-width: 640px;
}
</style>
