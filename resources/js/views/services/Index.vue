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
        <template v-if="!scarpaOnly">
            <v-btn class="ml-0 translucent" active-class="primary" round to="/services/washing-services">Washing services</v-btn>
            <v-btn class="ml-0 translucent" active-class="primary" round to="/services/drying-services">Drying services</v-btn>
            <v-btn class="ml-0 translucent" active-class="primary" round to="/services/other-services">Other services</v-btn>
            <v-btn class="ml-0 translucent" active-class="primary" round to="/services/full-services">Full services</v-btn>
            <v-btn class="ml-0 translucent" active-class="primary" round to="/services/scarpa-cleaning">Scarpa</v-btn>
            <v-btn class="ml-0 translucent" active-class="primary" round to="/services/lagoon/per-kilo">Lagoon</v-btn>
        </template>
        <!-- <v-btn class="ml-0 translucent" active-class="primary" round to="/services/per-kilo">Per kilo services</v-btn> -->
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
    },
    computed: {
        scarpaOnly() {
            return this.$store.getters.getScarpaOnly;
        }
    },
    watch: {
        scarpaOnly: {
            handler(val) {
                console.log(val)
                if(val) {
                    this.$router.push('/services/scarpa-cleaning')
                }
            },
            deep: true,
            immediate: true
        }
    }
}
</script>
