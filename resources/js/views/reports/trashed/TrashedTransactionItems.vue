<template>
    <div>
        <v-card>
            <v-tabs v-model="active">
                <v-tab>Services</v-tab>
                <v-tab>Products</v-tab>

                <v-tab-item>
                    <services></services>
                </v-tab-item>
                <v-tab-item>
                    <products></products>
                </v-tab-item>
            </v-tabs>
        </v-card>
    </div>
</template>
<script>
import Products from './Products.vue';
import Services from './Services.vue';
export default {
    components: {
        Products,
        Services
    },
    data() {
        return {
            active: parseInt(this.$route.query.tab)
        }
    }
}
</script>
