<template>
    <v-container>
        <h4 class="grey--text">View by</h4>
        <v-btn to="/developer/clients/by-clients" class="white" active-class="primary">By clients</v-btn>
        <v-btn to="/developer/clients/by-branches" class="white" active-class="primary">By branches</v-btn>
        <v-divider class="my-3"></v-divider>
        <router-view></router-view>
    </v-container>
</template>
