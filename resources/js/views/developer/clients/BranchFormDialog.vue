<template>
    <v-dialog :value="value" persistent max-width="640px">

        <form @submit.prevent="submit">

            <v-card>
                <v-card-title class="title">Branch details</v-card-title>
                <v-card-text>

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
                    <v-btn class="primary" type="submit" :loading="loading">Save</v-btn>
                    <v-btn @click="close">Cancel</v-btn>
                </v-card-actions>
            </v-card>

        </form>

    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'client', 'branch'
    ],
    data() {
        return {
            formData: {
                shopName: '',
                shopContactNo: '',
                shopEmail: '',
                shopCityMunicipality: '',
                shopAddress: ''
            },
            mode: 'insert'
        }
    },
    methods: {
        submit() {
            this.$store.dispatch(`branch/${this.mode}Branch`, {
                clientId: this.client ? this.client.user_id : null,
                branchId: this.branch ? this.branch.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('save', {
                    mode: this.mode,
                    branch: res.data.branch
                });
                this.close();
            });
        },
        close() {
            this.$emit('input', false);
            this.$store.commit('branch/clearErrors');
            this.formData.shopName = '';
            this.formData.shopContactNo = '';
            this.formData.shopEmail = '';
            this.formData.shopCityMunicipality = '';
            this.formData.shopAddress = '';
        }
    },
    computed: {
        errors() {
            return this.$store.getters['branch/getErrors'];
        },
        loading() {
            return this.$store.getters['branch/isSaving'];
        }
    },
    watch: {
        branch(val) {
            if(val) {
                this.mode = 'update';
                this.formData.shopName = val.name;
                this.formData.shopContactNo = val.contact_no;
                this.formData.shopEmail = val.email;
                this.formData.shopAddress = val.address;
                if(val.city_municipality) {
                    this.formData.shopCityMunicipality = val.city_municipality.name;
                }
            } else {
                this.mode = 'insert';
                this.formData.shopName = '';
                this.formData.shopContactNo = '';
                this.formData.shopEmail = '';
                this.formData.shopCityMunicipality = '';
                this.formData.shopAddress = '';
            }
        }
    }
}
</script>
