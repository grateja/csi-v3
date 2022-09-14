<template>
    <v-dialog :value="value" max-width="600" persistent>
        <v-card class="rounded-card" v-if="!!transaction">
            <v-card-title><span class="title">What do you want to print?</span>
                <v-spacer></v-spacer>
                <v-btn @click="close" icon><v-icon>close</v-icon></v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-layout>
                    <v-flex xs6>
                        <v-radio-group v-model="entity">
                            <span v-if="transaction.date_paid == null" class="red--text font-italic caption">For paid Job Orders only!</span>
                            <v-radio label="Job Order only" value="job-order" :disabled="transaction.date_paid == null"></v-radio>
                            <v-radio label="Claim stub only" value="claim-stub"></v-radio>
                        </v-radio-group>
                    </v-flex>
                    <v-flex xs6>
                        <v-list>
                            <!-- <v-list-tile>
                                <v-checkbox label="Include items" v-model="includeItems" :disabled="entity == 'job-order'"></v-checkbox>
                            </v-list-tile> -->
                            <!-- <v-list-tile>
                                <v-checkbox label="Include remarks" v-model="includeRemarks"></v-checkbox>
                            </v-list-tile> -->
                            <v-list-tile>
                                <v-checkbox label="Itemized" v-model="options.itemized"></v-checkbox>
                            </v-list-tile>
                            <v-list-tile>
                                <v-checkbox label="Products" v-model="options.products"></v-checkbox>
                            </v-list-tile>
                            <v-list-tile>
                                <v-checkbox label="Wash/Dry Services" v-model="options.services"></v-checkbox>
                            </v-list-tile>
                            <v-list-tile>
                                <v-checkbox label="Scarpa" v-model="options.scarpa"></v-checkbox>
                            </v-list-tile>
                            <v-list-tile>
                                <v-checkbox label="Lagoon" v-model="options.lagoon"></v-checkbox>
                            </v-list-tile>
                            <v-list-tile>
                                <v-checkbox label="Include QR Code" v-model="includeQRCode" v-if="canPrintQRCode" :disabled="dopuSetup == 'slave'"></v-checkbox>
                            </v-list-tile>
                        </v-list>
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn round class="primary" @click="submit" :loading="loading">
                    <v-icon left>print</v-icon>
                    Print
                </v-btn>
                <v-spacer />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'transaction', 'value'
    ],
    data() {
        return {
            entity: 'claim-stub',
            includeItems: true,
            // includeRemarks: true,
            includeQRCode: false,
            options: {
                itemized: false,
                products: false,
                services: false,
                scarpa: false,
                lagoon: false,
            }
        }
    },
    methods: {
        submit() {
            this.$store.dispatch('printer/print', {
                transactionId: this.transaction.id,
                entity: this.entity,
                formData: {
                    includeItems: this.includeItems,
                    // includeRemarks: this.includeRemarks,
                    includeQRCode: this.includeQRCode,
                    options: this.options
                }
            });
        },
        close() {
            this.$emit('input', false);
        }
    },
    watch: {
        value(val) {
            if(val && this.transaction) {
                if(this.transaction.date_paid == null) {
                    this.entity = 'claim-stub';
                    this.includeItems = false;
                } else {
                    this.entity = 'job-order';
                    this.includeItems = true;
                }
                if(!this.canPrintQRCode) {
                    this.formData.includeQRCode = false;
                }
            }
        },
        entity(val) {
            if(val == 'claim-stub') {
                this.includeItems = false;
            } else {
                this.includeItems = true
            }
        },
        dopuSetup: {
            handler(val) {
                if(val == 'slave') {
                    this.includeQRCode = true
                }
            },
            deep: true,
            immediate: true
        }
    },
    computed: {
        loading() {
            return this.$store.getters['printer/isLoading'];
        },
        dopuSetup() {
            return this.$store.getters.getDopuSetup
        },
        canPrintQRCode() {
            return this.dopuSetup == 'master' || this.dopuSetup == 'slave'
        }
    }
}
</script>
