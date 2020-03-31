<template>
    <v-dialog :value="value" persistent max-width="580">
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="title grey--text">Shop preferences</v-card-title>
                <v-progress-linear class="my-0" height="3" v-if="loading" indeterminate></v-progress-linear>
                <v-divider v-else></v-divider>
                <v-card-text>
                    <v-text-field outline v-model="formData.shopName" :error-messages="errors.get('shopName')" label="Shop name" ref="shopName" :enabled="!loading"></v-text-field>
                    <v-text-field outline v-model="formData.shopAddress" :error-messages="errors.get('shopAddress')" label="Address" :enabled="!loading"></v-text-field>
                    <v-text-field outline v-model="formData.shopNumber" :error-messages="errors.get('shopNumber')" label="Shop contact number" :enabled="!loading"></v-text-field>
                    <v-text-field outline v-model="formData.shopEmail" :error-messages="errors.get('shopEmail')" label="Shop email" :enabled="!loading"></v-text-field>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn round class="primary" type="submit" :loading="saving">save</v-btn>
                    <v-btn round @click="close">close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'clientId'
    ],
    data() {
        return {
            formData: {
                shopName: null,
                shopAddress: null,
                shopNumber: null,
                shopEmail: null
            },
            loading: false
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get(`/api/admin/preferences/shop-details/${this.clientId | 'self'}`).then((res, rej) => {
                this.formData.shopName = res.data.client.shop_name;
                this.formData.shopAddress = res.data.client.address;
                this.formData.shopNumber = res.data.client.shop_number;
                this.formData.shopEmail = res.data.client.shop_email;
            }).finally(() => {
                setTimeout(() => {
                    this.$refs.shopName.$el.querySelector('input').select();
                    this.loading = false;
                }, 500);
            });
        },
        submit() {
            console.log('sumit');
            this.$store.dispatch('client/setUpClient', {
                formData: this.formData
            }).then((res, rej) => {
                this.close();
            });
        },
        close() {
            this.$emit('input', false);
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.load();
            }
        }
    },
    computed: {
        errors() {
            return this.$store.getters['client/getErrors'];
        },
        saving() {
            return this.$store.getters['client/isUpdating'];
        }
    }
}
</script>
