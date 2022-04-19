<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    <span class="title grey--text">Cashless Payment</span>
                    <v-spacer></v-spacer>
                    <v-btn small icon @click="close">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-card-text>
                    <template>
                        <v-radio-group v-model="provider">
                            <v-radio label="G-Cash" value="G-Cash"></v-radio>
                            <v-radio label="PayMaya" value="PayMaya"></v-radio>
                            <v-radio label="Other" value="Other"></v-radio>
                        </v-radio-group>
                    </template>
                    <v-expand-transition>
                        <div v-if="provider == 'Other'">
                            <v-text-field class="round-input mt-3 text-xs-center" outline v-model="customProvider" label="Other provider" :error-messages="providerError"></v-text-field>
                        </div>
                    </v-expand-transition>
                    <v-text-field class="round-input mt-3 text-xs-center" outline v-model="amount" label="Amount" ref="amount" :error-messages="amountError"></v-text-field>
                    <v-text-field class="round-input mt-3 text-xs-center" outline v-model="referenceNumber" label="Reference number" :error-messages="referenceNumberError"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn type="submit" class="primary" round>Confirm</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'cashLess', 'amountToPay'
    ],
    data() {
        return {
            referenceNumber: null,
            provider: null,
            customProvider: null,
            amount: 0,
            providerError: null,
            amountError: null,
            referenceNumberError: null
        }
    },
    methods : {
        close() {
            this.$emit('input', false);
        },
        submit() {
            var hasError = false
            if(this.referenceNumber == null) {
                this.referenceNumberError = "This field is required"
                hasError = true
            }
            if(this.amount == null) {
                this.amountError = "This field is required"
                hasError = true
            } else if(isNaN(this.amount) || this.amount <= 0) {
                this.amountError = "Not a valid number"
                hasError = true
            }
            
            if(hasError) {
                return
            }
            this.$emit('confirm', {
                provider: this.provider != "Other" ? this.provider : this.customProvider,
                amount: this.amount,
                referenceNumber: this.referenceNumber
            })
            this.close()
        }
    },
    watch: {
        value(val) {
            console.log(this.cashLess)
            if(val && this.cashLess) {
                this.referenceNumber = this.cashLess.referenceNumber
                this.provider = this.cashLess.provider
                this.amount = this.cashLess.amount
            } else {
                this.referenceNumber = null
                this.provider = null
                this.amount  = this.amountToPay || 0
            }
            this.referenceNumberError = null
            this.amountError = null
        }
    }
}
</script>