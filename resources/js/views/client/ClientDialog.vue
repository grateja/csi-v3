<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Client info</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.shopName" :error-messages="errors.get('shopName')" outline label="Shop Name" ref="shopName"></v-text-field>
                    <v-text-field v-model="formData.shopEmail" :error-messages="errors.get('shopEmail')" outline label="Shop email"></v-text-field>
                    <v-text-field v-model="formData.shopAddress" :error-messages="errors.get('shopAddress')" outline label="Shop Address"></v-text-field>
                    <v-text-field v-model="formData.shopNumber" :error-messages="errors.get('shopNumber')" outline label="Shop Number"></v-text-field>
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
        'value', 'client'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                shopName: null,
                shopEmail: null,
                shopAddress: null,
                shopNumber: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`client/setUpClient`, {
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    client: res.data.client
                });
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['client/getErrors'];
        },
        saving() {
            return this.$store.getters['client/isUpdating'];
        }
    },
    watch: {
        value(val) {
            if(val && this.client) {
                this.formData.shopName = this.client.shop_name;
                this.formData.shopEmail = this.client.shop_email;
                this.formData.shopAddress = this.client.address;
                this.formData.shopNumber = this.client.shop_number;
            } else {
                this.formData.shopName = null;
                this.formData.shopEmail = null;
                this.formData.shopAddress = null;
                this.formData.shopNumber = null;
            }
            setTimeout(() => {
                this.$refs.shopName.$el.querySelector('input').select();
            }, 500);
        },
        client(val) {

        }
    }
}
</script>
