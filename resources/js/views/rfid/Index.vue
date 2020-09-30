<template>
    <v-container>
        <h3 class="title white--text">RFID</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn active-class="primary" class="translucent ml-0" round to="/rfid/transactions">Transactions</v-btn>
        <v-btn active-class="primary" class="translucent" round to="/rfid/rfid-cards">RFID Cards</v-btn>
        <v-btn active-class="primary" class="translucent" round to="/rfid/load-transactions">Load transactions</v-btn>

        <router-view></router-view>
    </v-container>
</template>
