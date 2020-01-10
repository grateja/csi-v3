<template>
    <v-container>
        <h3 class="title grey--text">Services</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn class="ml-0" active-class="primary" round to="/services/washing-services">Washing services</v-btn>
        <v-btn class="ml-0" active-class="primary" round to="/services/drying-services">Drying services</v-btn>
        <v-btn class="ml-0" active-class="primary" round to="/services/other-services">Other services</v-btn>
        <v-btn class="ml-0" active-class="primary" round to="/services/full-services">Full services</v-btn>
        <router-view></router-view>
    </v-container>
</template>
