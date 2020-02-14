<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="title grey--text">Product item details</v-card-title>

                <v-card-text>
                    <v-combobox :items="results" label="Search product" @input.native="search($event)" ref="keyword" item-text="name" @change="select"></v-combobox>

                    <v-text-field label="Name" v-model="formData.name" :error-messages="errors.get('name')" outline></v-text-field>
                    <v-text-field label="Quantity" v-model="formData.quantity" :error-messages="errors.get('quantity')" outline @input="updatePrice"></v-text-field>
                    <v-text-field label="Unit Price" v-model="unitPrice" outline @input="updatePrice"></v-text-field>
                    <v-text-field label="Total Price" v-model="formData.price" :error-messages="errors.get('price')" outline></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" round :loading="saving" type="submit">save</v-btn>
                    <v-btn round @click="close">close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>
<script>
export default {
    props: [
        'value', 'productItem', 'fullServiceId'
    ],
    data() {
        return {
            mode: 'insert',
            openPictureDialog: false,
            formData: {
                name: null,
                quantity: 0,
                price: 0,
                fullServiceId: null
            },
            results: [],
            unitPrice: 0
        }
    },
    methods: {
        search(e) {
            this.formData.id = null;
            axios.get('/api/products/all', {
                params: {
                    keyword: e.target.value
                }
            }).then((res, rej) => {
                this.results = res.data.result;
            });
        },
        select(item) {
            if(!!item && item.id) {
                this.formData.name = item.name;
                this.formData.quantity = 1;
                this.formData.price = item.selling_price;
                this.unitPrice = item.selling_price;
            }
        },
        close() {
            this.$emit('input', false);
            this.$store.commit('fullserviceproduct/clearErrors');
        },
        submit() {
            this.$store.dispatch(`fullserviceproduct/${this.mode}Service`, {
                productItemId: this.productItem ? this.productItem.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('save', {
                    mode: this.mode,
                    productItem: res.data.productItem
                });
                this.close();
            });
        },
        updatePrice() {
            this.formData.price = this.unitPrice * this.formData.quantity;
        }
    },
    computed: {
        errors() {
            return this.$store.getters['fullserviceproduct/getErrors'];
        },
        saving() {
            return this.$store.getters['fullserviceproduct/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(!!val && this.productItem) {
                this.mode = 'update';
                this.formData.name = this.productItem.name;
                this.formData.quantity = this.productItem.quantity;
                this.formData.price = this.productItem.price;
                this.unitPrice = this.productItem.price / this.productItem.quantity;
            } else {
                this.mode = 'insert';
                this.formData.name = null;
                this.formData.quantity = 0;
                this.formData.price = 0;
                this.unitPrice = 0;
            }
            setTimeout(() => {
                this.$refs.keyword.$el.querySelector('input').select();
            }, 500);
        },
        fullServiceId(val) {
            this.formData.fullServiceId = val;
        }
    }
}
</script>
