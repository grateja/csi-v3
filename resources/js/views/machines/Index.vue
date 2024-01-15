<template>
    <v-container>
        <h3 class="title white--text">Machines</h3>

        <v-btn to="/machines/lg" class="ml-0" active-class="primary" round>LG</v-btn>
        <v-btn to="/machines/elux" class="ml-0" active-class="primary" round>Electrolux</v-btn>

        <v-divider class="my-3"></v-divider>
        <router-view />
    </v-container>
</template>

<script>
export default {
    data() {
        return {
        }
    }
}
</script>
