<template>
    <v-container>
        <v-card class="transparent" flat>
            <v-card-title>
            <h3 class="title white--text">Services</h3>
            <v-spacer></v-spacer>
            <!-- <v-btn round>Import</v-btn> -->
            <v-btn @click="exportServices" round>Export</v-btn>
            </v-card-title>
        </v-card>
        <v-divider class="my-3"></v-divider>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/washing-services">Washing services</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/drying-services">Drying services</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/other-services">Other services</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/full-services">Full services</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/scarpa-cleaning">Scarpa</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/lagoon/per-kilo">Lagoon</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/services/elux">E-lux</v-btn>
        <router-view></router-view>
    </v-container>
</template>

<script>
export default {
    methods: {
        exportServices() {
            this.$store.dispatch('exportdownload/download', {
                uri: 'export-services'
            });
        }
    }
}
</script>
