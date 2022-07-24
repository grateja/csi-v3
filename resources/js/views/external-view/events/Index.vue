<template>
    <v-container class="events">
        <v-card flat class="pa-4">
            <v-card-actions>
                <h3 class="title grey--text">Events</h3>
                <v-spacer></v-spacer>
            </v-card-actions>
            <hr class="white darken-3">
            <router-view></router-view>
        </v-card>
    </v-container>
</template>

<style>
.events {
    height: 100vh;
}
</style>
