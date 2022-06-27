<template>
    <v-container>
        <h3 class="title white--text">Services</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/washing-services">Washing services</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/drying-services">Drying services</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/other-services">Other services</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/full-services">Full services</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/scarpa-cleaning">Scarpa</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/lagoon">Lagoon</v-btn>
        <!-- <v-btn class="ml-0 translucent" active-class="primary" round to="/services/per-kilo">Per kilo services</v-btn> -->
        <router-view></router-view>
    </v-container>
</template>
