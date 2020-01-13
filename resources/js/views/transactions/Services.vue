<template>
    <div>
        <h3 class="grey--text mt-5">Washing services</h3>

        <v-progress-linear v-if="loading" height="2" class="my-0" indeterminate></v-progress-linear>
        <v-divider v-else></v-divider>

        <v-layout row wrap v-if="washingServices.length">
            <v-flex v-for="item in washingServices" :key="item.id" xs6 sm4 lg3 xl2>
                <v-hover v-slot:default="{ hover }">
                    <v-card class="ma-1 pointer" :elevation="hover ? 12 : 2" @click="addWashingService(item)">
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
        <span v-if="!loading && washingServices.length == 0">No data</span>
        <span v-else-if="loading">Loading...</span>

        <h3 class="grey--text mt-5">Drying services</h3>
        <v-progress-linear v-if="loading" height="2" class="my-0" indeterminate></v-progress-linear>
        <v-divider v-else></v-divider>
        <v-layout row wrap v-if="dryingServices.length">
            <v-flex v-for="item in dryingServices" :key="item.id" xs6 sm4 lg3 xl2>
                <v-hover v-slot:default="{ hover }">
                    <v-card class="ma-1 pointer" :elevation="hover ? 12 : 2" @click="addDryingService(item)">
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
        <span v-if="!loading && dryingServices.length == 0">No data</span>
        <span v-else-if="loading">Loading...</span>

        <h3 class="grey--text mt-5">Other services</h3>
        <v-progress-linear v-if="loading" height="2" class="my-0" indeterminate></v-progress-linear>
        <v-divider v-else></v-divider>
        <v-layout row wrap v-if="otherServices.length">
            <v-flex v-for="item in otherServices" :key="item.id" xs6 sm4 lg3 xl2>
                <v-hover v-slot:default="{ hover }">
                    <v-card class="ma-1 pointer" :elevation="hover ? 12 : 2" @click="addOtherService(item)">
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
        <span v-if="!loading && otherServices.length == 0">No data</span>
        <span v-else-if="loading">Loading...</span>

        <h3 class="grey--text mt-5">Full services</h3>
        <v-progress-linear v-if="loading" height="2" class="my-0" indeterminate></v-progress-linear>
        <v-divider v-else></v-divider>
        <v-layout row wrap v-if="fullServices.length">
            <v-flex v-for="item in fullServices" :key="item.id" xs6 sm4 lg3 xl2>
                <v-hover v-slot:default="{ hover }">
                    <v-card class="ma-1 pointer" :elevation="hover ? 12 : 2" @click="addFullService(item)">
                        <v-card-text>
                            <v-responsive v-if="item.img_path">
                                <v-img height="100px" :src="item.img_path" max-height="100px" ></v-img>
                            </v-responsive>
                            <div class="ma-1">
                                <div class="title">
                                    {{item.name}}
                                </div>
                                <ol class="font-italic">
                                    <li v-for="sItem in item.full_service_items" :key="sItem.id">{{sItem.name}}</li>
                                    <li v-for="pItem in item.full_service_products" :key="pItem.id">{{pItem.name}}</li>
                                </ol>
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
        <span v-if="!loading && fullServices.length == 0">No data</span>
        <span v-else-if="loading">Loading...</span>
    </div>
</template>


<script>
export default {
    data() {
        return {
            washingServices: [],
            dryingServices: [],
            otherServices: [],
            fullServices: [],
            loading: false
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get('/api/pos-transactions/services', {

            }).then((res, rej) => {
                this.washingServices = res.data.washingServices;
                this.dryingServices = res.data.dryingServices;
                this.otherServices = res.data.otherServices;
                this.fullServices = res.data.fullServices;
            }).finally(() => {
                this.loading = false;
            });
        },
        addWashingService(item) {
            this.addService('washing', item);
        },
        addDryingService(item) {
            this.addService('drying', item);
        },
        addOtherService(item) {
            this.addService('other', item);
        },
        addFullService(item) {
            this.addService('full', item);
        },
        addService(category, item) {
            if(this.currentCustomer == null) {
                alert('Please specify customer first');
                return;
            }
            Vue.set(item, 'isAdding', true);
            this.$store.dispatch(`postransaction/addService`, {
                itemId: item.id,
                category,
                transactionId: this.currentTransaction? this.currentTransaction.id : null,
                customerId: this.currentCustomer? this.currentCustomer.id : null
            }).then((res, rej) => {

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
