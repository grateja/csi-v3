<template>
    <div>
        <lagoon-per-kilo />
        <h3 class="white--text mt-5">Per Item</h3>
        <!-- <pre v-if="items">{{items.group((c => c.category))}}</pre> -->
        <v-progress-linear v-if="loading" height="2" class="my-0" indeterminate></v-progress-linear>
        <v-divider v-else></v-divider>
        <!-- <pre>{{groups}}</pre> -->
        <div v-for="(items, i) in groups" :key="i">
            <h3 class="white--text">{{i}}</h3>
            <v-layout row wrap>
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
                                    P {{ parseFloat(item.price).toFixed(2)}}
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-hover>
                </v-flex>
            </v-layout>
        </div>
        <span v-if="!loading && groups.length == 0">No data</span>
        <span v-else-if="loading">Loading...</span>
    </div>
</template>
<script>
import LagoonPerKilo from './LagoonPerKilo.vue'

export default {
    components: {
        LagoonPerKilo
    },
    data() {
        return {
            groups: [],
            loading: false
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get('/api/pos-transactions/lagoon', {

            }).then((res, rej) => {
                this.groups = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        addItem(item) {
            if(this.currentCustomer == null) {
                alert('Please specify customer first');
                return;
            }
            // if(this.items.find(i => i.isAdding)) {
            //     return;
            // }
            Vue.set(item, 'isAdding', true);
            this.$store.dispatch(`postransaction/addLagoon`, {
                itemId: item.id,
                transactionId: this.currentTransaction? this.currentTransaction.id : null,
                customerId: this.currentCustomer? this.currentCustomer.id : null
            }).then((res, rej) => {
                // item.current_stock = res.data.product.current_stock;
            }).finally(() => {
                Vue.set(item, 'isAdding', false);
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
