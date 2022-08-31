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

                    <v-btn @click="openStoreHours = true" round class="ml-0">
                        <v-icon left>access_time</v-icon>
                        Store hours
                    </v-btn>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn round class="primary" type="submit" :loading="saving">save</v-btn>
                    <v-btn round @click="close">close</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="generateQRCode" :loading="generatingQRCode">
                        <img :src="`/img/dos-icons/qr-code.png`" alt="" width="20" class="mt-1 mx-2" />
                    </v-btn>
                </v-card-actions>
            </v-card>
        </form>
        <store-hours-dialog v-model="openStoreHours" />
        <v-dialog v-model="showQRCode" width="400px">
            <v-card v-if="client && qrData">
                <v-img :src="qrData" alt="" />
                <v-layout>
                    <v-flex xs5 class="text-xs-right mr-3">Partner ID:</v-flex>
                    <v-flex xs7>{{client.user_id}}</v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs5 class="text-xs-right mr-3">Shop name:</v-flex>
                    <v-flex xs7>{{client.shop_name}}</v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs5 class="text-xs-right mr-3">Address:</v-flex>
                    <v-flex xs7>{{client.address}}</v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs5 class="text-xs-right mr-3">Contact number:</v-flex>
                    <v-flex xs7>{{client.contact_number}}</v-flex>
                </v-layout>
                <v-card-actions>
                    <v-spacer />
                    <v-btn icon @click="print" :loading="printingQRCode">
                        <v-icon>print</v-icon>
                    </v-btn>
                    <v-spacer />
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-dialog>
</template>

<script>
import StoreHoursDialog from '../shop-preferences/StoreHoursDialog.vue';

export default {
    components: {
        StoreHoursDialog
    },
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
            client: null,
            loading: false,
            openStoreHours: false,
            showQRCode: false,
            qrData: null,
            generatingQRCode: false,
            printingQRCode: false,
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get(`/api/admin/preferences/shop-details/${this.clientId | 'self'}`).then((res, rej) => {
                this.client = res.data.client;
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
        },
        generateQRCode() {
            this.generatingQRCode = true;
            axios.post('/api/admin/preferences/generate-qr-code').then((res, rej) => {
                this.qrData = res.data;
                this.showQRCode = true;
            }).finally(() => {
                this.generatingQRCode = false;
            });
        },
        print() {
            this.printingQRCode = true;
            axios.post('/api/admin/preferences/print-qr-code').then((res, rej) => {
                // if(!res.data.method) {
                //     let w = window.open('about:blank', 'print', 'width=800,height=1000');

                //     w.document.write(res.data);
                //     w.document.close();

                // }
                this.showQRCode = false;
            }).finally(() => {
                this.printingQRCode = false;
            });
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
