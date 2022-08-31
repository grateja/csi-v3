<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Shop info</v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.id" :error-messages="errors.get('id')" outline label="ID"></v-text-field>
                    <v-text-field v-model="formData.shopName" :error-messages="errors.get('shopName')" outline label="Shop name"></v-text-field>
                    <v-text-field v-model="formData.address" :error-messages="errors.get('address')" outline label="Address"></v-text-field>
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
        'value', 'lagoonPartner'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                id: null,
                shopName: null,
                address: null
            }
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
            } else {
                this.mode = 'insert';
                this.formData.id = null;
                this.formData.shopName = null;
                this.formData.address = null;
            }
        }
    }
}
</script>
