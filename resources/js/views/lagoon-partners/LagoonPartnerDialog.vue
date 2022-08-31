<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="title">Shop info</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.id" :error-messages="errors.get('id')" outline label="ID"></v-text-field>
                    <v-text-field v-model="formData.shopName" :error-messages="errors.get('shopName')" outline label="Shop name"></v-text-field>
                    <v-text-field v-model="formData.address" :error-messages="errors.get('address')" outline label="Address"></v-text-field>
                    <v-text-field v-model="formData.contactNumber" :error-messages="errors.get('contactNumber')" outline label="Contact number"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit" round :loading="saving">Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                    <v-spacer />
                    <template v-if="mode == 'insert'">
                        <v-btn icon @click="scanQRCode('camera')">
                            <v-icon>camera_alt</v-icon>
                        </v-btn>
                        <v-btn icon @click="scanQRCode('file')">
                            <v-icon>collections</v-icon>
                        </v-btn>
                    </template>
                </v-card-actions>
            </v-card>
        </form>
        <scanner-dialog v-model="showScanner" @success="decode" :mode="scannMode" />
    </v-dialog>
</template>

<script>
import ScannerDialog from '../qrscanner/ScannerDialog.vue';

export default {
    components: {
        ScannerDialog
    },
    props: [
        'value', 'lagoonPartner'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                id: null,
                shopName: null,
                address: null
            },
            scannMode: null,
            showScanner: false,
            QRData: null
        }
    },
    methods: {
        close() {
            this.clear();
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`lagoonpartner/${this.mode}LagoonPartner`, {
                lagoonPartnerId: this.lagoonPartner ? this.lagoonPartner.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    lagoonPartner: res.data.lagoonPartner,
                    mode: this.mode
                });
            });
        },
        clear() {
            this.$store.commit('lagoonpartner/clearErrors');
        },
        scanQRCode(mode) {
            this.showScanner = true;
            this.scannMode = mode;
        },
        decode(data) {
            try {
                this.QRData = JSON.parse(data);
                this.formData.id = this.QRData.user_id;
                this.formData.shopName = this.QRData.shop_name;
                this.formData.address = this.QRData.address;
                this.formData.contactNumber = this.QRData.contact_number;
                console.log(this.QRData)
            } catch(e) {
                this.QRData = null;
                alert(e.message)
            }
        }
    },
    computed: {
        errors() {
            return this.$store.getters['lagoonpartner/getErrors'];
        },
        saving() {
            return this.$store.getters['lagoonpartner/isSaving'];
        }
    },
    watch: {
        lagoonPartner(val) {
            if(val) {
                this.mode = 'update';
                this.formData.id = val.id;
                this.formData.shopName = val.shop_name;
                this.formData.address = val.address;
                this.formData.contactNumber = val.contact_number;
            } else {
                this.mode = 'insert';
                this.formData.id = null;
                this.formData.shopName = null;
                this.formData.address = null;
                this.formData.contactNumber = null;
            }
        }
    }
}
</script>
