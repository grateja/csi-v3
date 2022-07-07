<template>
    <div>
        <h3 class="white--text mt-5">Per Kilo</h3>
        <v-progress-linear v-if="loading" height="2" class="my-0" indeterminate></v-progress-linear>
        <v-divider v-else></v-divider>
        <v-layout row wrap v-if="items.length">
            <v-flex v-for="item in items" :key="item.id" xs6 sm4 lg3 xl2>
                <v-hover v-slot:default="{ hover }">
                    <v-card class="ma-1 pointer rounded-card translucent" :elevation="hover ? 12 : 2" @click="addItem(item)">
                        <v-card-text>
                            <v-responsive v-if="item.img_path">
                                <v-img height="100px" :src="item.img_path" max-height="100px" ></v-img>
                            </v-responsive>
                            <div class="text-xs-center ma-1">
                                <div>
                                    {{item.name}}
                                </div>
                                <div class="font-italic grey--text">{{item.description}}</div>
                            </div>
                            <v-progress-linear v-if="item.isAdding" indeterminate height="4" class="my-0"></v-progress-linear>
                            <v-divider v-else class="my-2"></v-divider>
                            <div class="text-xs-center title ma-2">
                                P {{ parseFloat(item.price_per_kilo).toFixed(2)}} /Kg
                            </div>
                        </v-card-text>
                    </v-card>
                </v-hover>
            </v-flex>
        </v-layout>
        <span v-if="!loading && items.length == 0">No data</span>
        <span v-else-if="loading">Loading...</span>
        <lagoon-per-kilo-dialog v-model="showDialog" :item="activeItem" @select="addContinue"/>
    </div>
</template>
<script>
import LagoonPerKiloDialog from './LagoonPerKiloDialog.vue'

export default {
    components: {
        LagoonPerKiloDialog
    },
    data() {
        return {
            items: [],
            loading: false,
            showDialog: false,
            activeItem: null
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get('/api/services/lagoon-per-kilo', {

            }).then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        addItem(item) {
            if(this.currentCustomer == null) {
                alert('Please specify customer first');
                return;
            }
            if(this.items.find(i => i.isAdding)) {
                return;
            }
            this.activeItem = item;
            this.showDialog = true;
        },
        addContinue(kg) {
            Vue.set(this.activeItem, 'isAdding', true);
            this.$store.dispatch(`postransaction/addLagoonPerKilo`, {
                itemId: this.activeItem.id,
                transactionId: this.currentTransaction? this.currentTransaction.id : null,
                customerId: this.currentCustomer? this.currentCustomer.id : null,
                kg
            }).then((res, rej) => {
                // this.activeItem.current_stock = res.data.product.current_stock;
            }).finally(() => {
                Vue.set(this.activeItem, 'isAdding', false);
            });
        }
    },
    created() {
        this.load();
    },
    computed: {
        currentCustomer() {
            return this.$store.getters['postransaction/getCurrentCustomer'];
        },
        currentTransaction() {
            return this.$store.getters['postransaction/getCurrentTransaction'];
        }
    }
}
</script>
