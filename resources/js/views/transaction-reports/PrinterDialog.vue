<template>
    <v-dialog :value="value" max-width="600" persistent>
        <v-card class="rounded-card" v-if="!!transaction">
            <v-card-title><span class="title">What do you want to print?</span>
                <v-spacer></v-spacer>
                <v-btn @click="close" icon><v-icon>close</v-icon></v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-checkbox label="Itemized" v-model="itemized"></v-checkbox>
                <v-btn @click="print('claim-stub')" round :loading="entity == 'claim-stub'">
                    Claim Stub
                </v-btn>
                <v-btn @click="print('job-order')" :loading="entity == 'job-order'" round>
                    Job Order
                </v-btn>
                <v-btn v-if="canPrintQRCode" @click="print('dopu')" :loading="entity == 'dopu'" round>
                    DOPU
                </v-btn>
            </v-card-text>
            <!-- <v-card-actions>
                <v-spacer />
                <v-btn round class="primary" @click="submit" :loading="loading">
                    <v-icon left>print</v-icon>
                    Print
                </v-btn>
                <v-spacer />
            </v-card-actions> -->
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
            entity: null,
            itemized: false
        }
    },
    methods: {
        print(entity) {
            this.entity = entity;
            this.$store.dispatch('printer/print', {
                transactionId: this.transaction.id,
                entity,
                formData: {
                    itemized: this.itemized
                }
            }).finally(() => {
                this.entity = null;
            });
        },
        close() {
            this.$emit('input', false);
            this.$emit('close', false);
        }
    },
    watch: {
        value(val) {
            if(val && this.transaction) {
                if(this.transaction.date_paid == null) {
                    this.itemized = false;
                } else {
                    this.itemized = true;
                }
            }
        },
        // entity(val) {
        //     if(val == 'claim-stub') {
        //         this.itemized = false;
        //     } else {
        //         this.itemized = true
        //     }
        // },
        // dopuSetup: {
        //     handler(val) {
        //         if(val == 'slave') {
        //             this.entity = 'dopu';
        //         }
        //     },
        //     deep: true,
        //     immediate: true
        // }
    },
    computed: {
        // loading() {
        //     return this.$store.getters['printer/isLoading'];
        // },
        dopuSetup() {
            return this.$store.getters.getDopuSetup
        },
        canPrintQRCode() {
            return this.dopuSetup == 'slave'
        }
    }
}
</script>
