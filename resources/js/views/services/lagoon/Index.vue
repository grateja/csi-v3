<template>
    <v-container>
        <h3 class="title white--text">Lagoon</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/lagoon/per-kilo">Per Kilo</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/lagoon/per-item">Per Item</v-btn>
        <router-view></router-view>

    </v-container>
</template>