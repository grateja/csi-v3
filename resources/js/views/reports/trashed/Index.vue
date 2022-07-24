<template>
    <div>
        <h3 class="title grey--text">Trashed</h3>
        <v-divider class="my-3"></v-divider>

        <v-btn active-class="primary" class="ml-0" to="/reports/trashed/transactions">Transactions</v-btn>
        <v-btn active-class="primary" to="/reports/trashed/transaction-items">Items</v-btn>

        <v-divider class="my-3"></v-divider>

        <v-card>
            <v-card-text>
                <router-view></router-view>
            </v-card-text>
        </v-card>
    </div>
</template>
