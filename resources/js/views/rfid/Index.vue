<template>
    <v-container>
        <h3 class="title grey--text">RFID</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn active-class="primary" round to="/rfid/transactions">Transactions</v-btn>
        <v-btn active-class="primary" round to="/rfid/rfid-cards">RFID Cards</v-btn>
        <v-btn active-class="primary" round to="/rfid/load-transactions">Load transactions</v-btn>

        <router-view></router-view>
    </v-container>
</template>
