<template>
    <v-container>
        <h3 class="title white--text">Job orders</h3>
        <v-divider class="my-3"></v-divider>
        <!-- <v-btn to="/transaction-reports/by-job-orders" class="ml-0 translucent" active-class="primary" round>View by Job Orders</v-btn> -->
        <!-- <v-btn to="/transaction-reports/by-service-items" class="translucent" active-class="primary" round>View by service items</v-btn> -->
        <!-- <v-btn to="/transaction-reports/by-product-items" class="translucent" active-class="primary" round>View by product items</v-btn> -->
        <v-divider class="transparent my-4"></v-divider>
        <router-view></router-view>
    </v-container>
</template>
