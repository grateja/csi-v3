<template>
    <div>
        <h3 class="white--text mt-5">Shoe cleanings</h3>
        <v-progress-linear v-if="loading == 'categories'" height="2" class="my-0" indeterminate></v-progress-linear>
        <v-divider v-else></v-divider>

        <v-layout>
            <v-flex xs4>
                <template  v-for="service in services">
                    <div :key="service.id">
                        <v-hover v-slot:default="{ hover }">
                            <v-card class="ma-1 pointer rounded-card translucent" :elevation="hover ? 12 : 2" @click="activeService = service" :color="activeService == service ? 'white' : ''">
                                <v-card-text>
                                    <v-responsive v-if="service.img_path">
                                        <v-img height="100px" :src="service.img_path" max-height="100px" ></v-img>
                                    </v-responsive>
                                    <div class="text-xs-center ma-1">
                                        <div>
                                            {{service.name}}
                                        </div>
                                        <div class="font-italic grey--text">{{service.description}}</div>
                                    </div>
                                    <v-progress-linear v-if="service.isAdding" indeterminate height="4" class="my-0"></v-progress-linear>
                                    <v-divider v-else class="my-2"></v-divider>
                                </v-card-text>
                            </v-card>
                        </v-hover>
                    </div>
                </template>
            </v-flex>

            <v-flex xs8>
                <v-progress-linear v-if="loading == 'variations'" indeterminate height="4" class="my-2"></v-progress-linear>
                <v-divider v-else class="my-2 transparent"></v-divider>
                <v-card v-if="activeService && loading != 'variations'" class="transparent" flat>
                    <div>   
                        <v-card-title class="pb-0">
                            <h3 class="title white--text">Sizes</h3>
                            <v-spacer></v-spacer>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-btn v-for="(variation, keys) in variations" :key="keys" @click="selectSize(variation)" :outline="!((activeSize != null) && activeSize[0].action == keys)" color="white" class="translucent" round>
                            {{keys}}
                            <v-icon right v-if="((activeSize != null) && activeSize[0].action == keys)">done</v-icon>
                        </v-btn>
                    </div>

                    <v-card-title class="pb-0">
                        <h3 class="title white--text">Price</h3>
                        <v-spacer></v-spacer>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-btn v-if="activeColor" color="primary" round @click="addService" :loading="addingItem">
                        [ {{activeColor.action}} | {{activeColor.color}} ] P {{parseFloat(activeColor.selling_price).toFixed(2)}}
                        <v-icon right>done</v-icon>
                    </v-btn>
                </v-card>
            </v-flex>
        </v-layout>
    </div>
</template>

<script>
export default {
    data() {
        return {
            services: [],
            variations: [],
            loading: null,
            groupBy: 'action',
            activeService: null,
            activeSize: null,
            activeColor: null,
            addingItem: false
        }
    },
    methods: {
        load() {
            this.loading = 'categories';
            axios.get('/api/pos-transactions/scarpa-cleanings').then((res, rej) => {
                this.services = res.data.result;
            }).finally(() => {
                this.loading = null;
            });
        },
        viewVariations() {
            this.loading = 'variations';
            this.variations = [];
            this.activeSize = null;
            this.activeColor = null;
            axios.get(`/api/pos-transactions/scarpa-cleanings/${this.activeService.id}/variations/${this.groupBy}`).then((res, rej) => {
                this.variations = res.data.variations;
                if(this.variations) {
                    this.activeSize = Object.values(this.variations)[0];
                }
            }).finally(() => {
                this.loading = null;
            });
        },
        selectSize(action) {
            this.activeSize = action;
        },
        selectColor(color) {
            this.activeColor = color;
        },
        addService() {
            if(!this.activeColor) {
                alert('No selected variation');
                return;
            }

            if(!this.currentCustomer) {
                alert("No customer selected");
                return;
            }

            this.addingItem = true;
            this.$store.dispatch('postransaction/addShoeCleaning', {
                variationId: this.activeColor.id,
                transactionId: this.currentTransaction? this.currentTransaction.id : null,
                customerId: this.currentCustomer? this.currentCustomer.id : null
            }).finally(() => {
                this.addingItem = false;
            })
        }
    },
    created() {
        this.load();
    },
    watch: {
        activeService(val) {
            if(val) {
                this.viewVariations();
            }
        },
        activeSize(val) {
            if(val) {
                if(val.length) {
                    this.selectColor(val[0]);
                }
            }
        }
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
