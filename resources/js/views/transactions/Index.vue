<template>
    <v-container>
        <h3 class="title grey--text">Transaction</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn to="/new-transaction/services" class="ml-0" active-class="primary" round>Services</v-btn>
        <v-btn to="/new-transaction/products" active-class="primary" round>Products</v-btn>
        <v-layout>
            <v-flex xs7 lg8>
                <router-view></router-view>
            </v-flex>
            <v-flex xs5 lg4>
                <customer-panel @selectCustomer="selectCustomer" />
                <transaction-items />
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
import CustomerPanel from './CustomerPanel.vue';
import TransactionItems from './TransactionItems.vue';
export default {
    components: {
        CustomerPanel,
        TransactionItems
    },
    methods: {
        selectCustomer(customer) {
            if(!!customer) {
                this.$store.dispatch('postransaction/checkCustomer', customer);
            }
        }
    }
}
</script>
