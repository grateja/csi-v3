<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="grey--text title">Add product stock</v-card-title>

                <v-divider></v-divider>

                <v-card-text>

                    <v-card v-if="product" class="my-3">
                        <v-card-text>
                            <dl>
                                <dt class="caption grey--text">Product name</dt>
                                <dd class="ml-3">{{product.name}}</dd>
                                <dt class="caption grey--text">Current stock</dt>
                                <dd class="ml-3">{{product.available}}</dd>
                            </dl>
                        </v-card-text>
                    </v-card>

                    <v-text-field type="date" v-model="formData.date" label="Date" :error-messages="errors.get('date')"></v-text-field>

                    <v-text-field type="number" v-model="formData.quantity" @input="computeAll" label="Quantity" :error-messages="errors.get('quantity')"></v-text-field>

                    <v-text-field v-model="formData.unitCost" label="Unit cost" @input="computeTotalCost" :error-messages="errors.get('unitCost')"></v-text-field>
                    <v-text-field v-model="formData.totalCost" label="Total cost" @input="computeUnitCost"  :error-messages="errors.get('totalCost')"></v-text-field>
                    <v-text-field v-model="formData.receipt" label="Receipt" :error-messages="errors.get('receipt')"></v-text-field>
                    <v-textarea v-model="formData.remarks" label="Remarks" rows="3"></v-textarea>

                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn type="submit" class="primary" :loading="loading">Ok</v-btn>
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
        'product'
    ],
    data() {
        return {
            formData: {
                date: new Date().toISOString().substring(0, 10),
                quantity: 0,
                unitCost: 0,
                totalCost: 0,
                remarks: '',
                receipt: ''
            }
        }
    },
    methods: {
        clear() {
            this.formData.date = new Date().toISOString().substring(0, 10);
            this.formData.quantity = 0;
            this.formData.unitCost = 0;
            this.formData.totalCost = 0;
            this.formData.remarks = null;
            this.formData.receipt = null;
        },
        cancel() {
            this.$emit('input', false);
            this.clear();
        },
        submit() {
            this.$store.dispatch('product/addStock', {
                productId: this.product.id,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('ok', res.data);
                this.$emit('input', false);
                this.clear();
            });
        },
        computeUnitCost() {
            this.formData.unitCost = parseFloat(parseFloat(this.formData.totalCost) / this.formData.quantity).toFixed(2);
            console.log('total cost', this.formData.totalCost);
        },
        computeTotalCost() {
            this.formData.totalCost = parseFloat(this.formData.quantity * this.formData.unitCost).toFixed(2);
            console.log('unit cost', this.formData.unitCost);
        },
        computeAll() {
            this.formData.unitCost = parseFloat(this.formData.totalCost / this.formData.quantity).toFixed(2);
            this.formData.totalCost = parseFloat(this.formData.quantity * this.formData.unitCost).toFixed(2);
        }
    },
    computed: {
        errors() {
            return this.$store.getters['product/getErrors'];
        },
        loading() {
            return this.$store.getters['product/isSaving'];
        }
    }
}
</script>
