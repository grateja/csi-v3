<template>
    <v-container>
        <h3 class="title white--text">External View</h3>
        <v-divider class="my-3"></v-divider>

        <v-btn class="ml-0 translucent" active-class="primary" round to="/external-view/events">Events</v-btn>
        <v-btn class="ml-0 translucent" active-class="primary" round to="/external-view/announcements">Announcements</v-btn>
        <router-view></router-view>
    </v-container>
</template>