<template>
    <v-dialog :value="value" max-width="400" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="grey--text title">Purchase item details</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-combobox :items="results" label="Search product" outline :error-messages="errors.get('productName')" v-model="formData.productName" @input.native="search($event)" ref="keyword" item-value="name" item-text="name"></v-combobox>

                    <v-text-field v-model="formData.date" type="date" label="Date" :error-messages="errors.get('date')" outline></v-text-field>
                    <v-text-field v-model="formData.quantity" label="Quantity" :error-messages="errors.get('quantity')" outline></v-text-field>
                    <v-text-field v-model="formData.unitCost" @keyup="computeTotalCost" label="Unit cost" :error-messages="errors.get('unitCost')" outline></v-text-field>
                    <v-text-field v-model="formData.totalCost" @keyup="computeUnitCost" label="Total cost" :error-messages="errors.get('totalCost')" outline></v-text-field>
                    <v-text-field v-model="formData.remarks" label="Remarks" :error-messages="errors.get('remarks')" outline></v-text-field>
                    <v-text-field v-model="formData.receipt" label="Receipt" :error-messages="errors.get('receipt')" outline></v-text-field>

                </v-card-text>
                <v-card-actions>
                    <v-btn @click="close">close</v-btn>
                    <v-btn class="primary" type="submit" :loading="saving">save</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'productPurchase'
    ],
    data() {
        return {
            cancelSource: null,
            formData: {
                productName: null,
                date: new Date().toISOString().substring(0, 10),
                receipt: null,
                quantity: 0,
                unitCost: 0,
                totalCost: 0,
                remarks: null
            },
            results: [],
            mode: 'insert'
        }
    },
    methods: {
        search(e) {
            this.formData.id = null;
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();
            axios.get('/api/products/all', {
                params: {
                    keyword: e.target.value
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                this.results = res.data.result;
            });
        },
        close() {
            this.$emit('input', false);
        },
        submit() {
            this.formData.productName = this.formData.productName ? this.formData.productName.name || this.formData.productName : null;
            this.$store.dispatch(`productpurchase/${this.mode}ProductPurchase`, {
                productPurchaseId: this.productPurchase ? this.productPurchase.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('save', {
                    mode: this.mode,
                    productPurchase: res.data.productPurchase
                });
                this.close();
            });
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        computeTotalCost() {
            this.formData.totalCost = this.formData.quantity * this.formData.unitCost;
        },
        computeUnitCost() {
            this.formData.unitCost = this.formData.totalCost / this.formData.quantity;
        }
    },
    computed: {
        errors() {
            return this.$store.getters['productpurchase/getErrors'];
        },
        saving() {
            return this.$store.getters['productpurchase/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(val && this.productPurchase) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
                this.formData.productName = null;
                this.formData.date = new Date().toISOString().substring(0, 10);
                this.formData.receipt = null;
                this.formData.quantity = 0;
                this.formData.unitCost = 0;
                this.formData.totalCost = 0;
                this.formData.remarks = null;
            }
        }
    }
}
</script>
