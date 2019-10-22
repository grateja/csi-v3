<template>
    <v-container>
        <h4 class="title grey--text">POS</h4>
        <v-divider class="my-3"></v-divider>
        <v-btn @click="goToProducts($event)" class="ml-0 white" active-class="primary" flat to="/pos/products" :loading="productsLoading">Products</v-btn>
        <v-btn @click="goToServices($event)" class="ml-0 white" active-class="primary" flat to="/pos/services" :loading="servicesLoading">Services</v-btn>
        <v-layout row wrap>
            <v-flex xs12 sm7 lg8>
                <v-card>
                    <v-card-text>
                        <form @submit.prevent="filter">
                            <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
                        </form>
                    </v-card-text>
                </v-card>

                <router-view></router-view>
            </v-flex>
            <v-flex xs12 sm5 lg4>
                <customer-transaction class="ml-2"></customer-transaction>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>

import BranchBrowser from '../shared/BranchBrowser.vue';
import CustomerTransaction from './CustomerTransaction.vue';

export default {
    components: {
        BranchBrowser,
        CustomerTransaction
    },
    data() {
        return {
            keyword: this.$route.query.keyword,
            page: this.$route.query.page || 1,
            transactionType: this.$route.path.substr(5)
        }
    },
    methods: {
        goToProducts(e) {
            e.stopPropagation();
            this.transactionType = 'products';
            this.keyword = '';
            this.page = 1;
            this.go();
            this.load();
        },
        goToServices(e) {
            e.stopPropagation();
            this.transactionType = 'services';
            this.keyword = '';
            this.page = 1;
            this.go();
            this.load();
        },
        go() {
            this.$router.push({
                path: `/pos/${this.transactionType.toLowerCase()}`,
                query: {
                    keyword: this.keyword,
                    page: this.page
                }
            });
        },
        filter() {
            this.go();
            this.load();
        },
        load() {
            let query = {
                keyword: this.keyword,
                page: this.page,
                branchId: this.activeBranch.id
            };

            if(this.transactionType == 'services') {
                this.$store.dispatch(`service/loadServices`, {
                    query
                }).then((res, rej) => {
                    console.log(res.data);
                });
            } else if(this.transactionType == 'products') {
                this.$store.dispatch(`product/loadProducts`, {
                    query
                }).then((res, rej) => {
                    console.log(res.data);
                });
            }
        }
    },
    computed: {
        customer() {
            return this.$store.getters['customer/getCustomer'];
        },
        productsLoading() {
            return this.$store.getters['product/isLoading'];
        },
        servicesLoading() {
            return this.$store.getters['service/isLoading'];
        },
        activeBranch() {
            return this.$store.getters.getActiveBranch;
        }
    },
    mounted() {
        if(this.activeBranch) {
            this.load();
        }
    },
    watch: {
        activeBranch(val) {
            if(val) {
                this.load();
            }
        }
    }
}
</script>
