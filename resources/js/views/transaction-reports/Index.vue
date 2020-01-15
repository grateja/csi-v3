<template>
    <v-container>
        <h3 class="title grey--text">Transactions</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn to="/transaction-reports/by-job-orders" class="ml-0" active-class="primary" round>View by Job Orders</v-btn>
        <v-btn to="/transaction-reports/by-service-items" active-class="primary" round>View by service items</v-btn>
        <v-btn to="/transaction-reports/by-product-items" active-class="primary" round>View by product items</v-btn>
        <v-divider class="transparent my-2"></v-divider>
        <router-view></router-view>
    </v-container>
</template>
