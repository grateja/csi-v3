<template>
    <div>
        <h4 class="title grey--text">Transactions</h4>
        <v-divider class="my-3"></v-divider>
        <v-btn active-class="primary" class="ml-0" to="/reports/transactions/pos-transactions">POS Transactions</v-btn>
        <v-btn active-class="primary" to="/reports/transactions/rfid-transactions">RFID Services</v-btn>
        <v-btn active-class="primary" to="/reports/transactions/topup-transactions">RFID Top ups</v-btn>
        <router-view></router-view>
    </div>
</template>
