<template>
    <v-container>
        <v-card flat class="transparent" max-width="1024" style="margin: 10px auto;">
            <v-card v-if="!!user" color="transparent" flat>
                <v-layout row wrap>
                    <v-flex xs4 sm3 md2 v-for="(link, i) in filterLinks" :key="i">
                        <v-hover v-slot:default="{ hover }">
                            <v-card :elevation="hover ? 12 : 0" class="ma-4 tile rounded-card translucent" @click="navigate(link.route)">
                                <div class="pa-4">
                                    <v-img :src="`/img/dos-icons/${link.icon}.png`" alt="" />
                                </div>
                            </v-card>
                        </v-hover>
                        <div class="text-xs-center my-2">{{link.text}}</div>
                    </v-flex>
                </v-layout>
            </v-card>
            <v-card v-else>
                unauthorized
            </v-card>
        </v-card>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            links: [
                {
                    text: 'New Job Order',
                    icon: 'new-transaction',
                    roles: ['staff', 'admin'],
                    color: '#33cbff',
                    route: '/new-transaction/services'
                },
                {
                    text: 'Remote panel',
                    icon: 'remote-panel',
                    roles: ['staff', 'admin', 'developer'],
                    color: '#fbf9e9',
                    route: '/remote-panel'
                },
                {
                    text: 'Transaction report',
                    icon: 'sales-report',
                    roles: ['admin'],
                    color: '#a7a1e6',
                    route: '/sales-report/calendar-view'
                },
                {
                    text: 'Job orders',
                    icon: 'job-orders',
                    roles: ['staff', 'admin'],
                    color: '#cfe6a1',
                    route: '/transaction-reports/by-job-orders'
                },
                {
                    text: 'Unpaid',
                    icon: 'unpaid-transactions',
                    roles: ['staff', 'admin'],
                    color: '#ace6a1',
                    route: '/unpaid-transactions'
                },
                {
                    text: 'Pending services',
                    icon: 'pending-services',
                    roles: ['staff', 'admin'],
                    color: '#c4eabd',
                    route: '/pending-services'
                },
                {
                    text: 'Customers',
                    icon: 'customers',
                    roles: ['staff', 'admin'],
                    color: '#a1e6d9',
                    route: '/customers'
                },
                {
                    text: 'Users',
                    icon: 'users',
                    roles: ['admin', 'developer'],
                    color: '#bcefe5',
                    route: '/users'
                },
                {
                    text: 'Inventory Log',
                    icon: 'product-purchases',
                    roles: ['staff', 'admin'],
                    color: '#83adfb',
                    route: '/product-purchases'
                },
                {
                    text: 'Expenses',
                    icon: 'expenses',
                    roles: ['staff', 'admin'],
                    color: '#95b9fb',
                    route: '/expenses'
                },
                {
                    text: 'Services',
                    icon: 'services',
                    roles: ['admin'],
                    color: '#fbf783',
                    route: '/services/washing-services'
                },
                {
                    text: 'Products',
                    icon: 'products',
                    roles: ['admin', 'staff'],
                    color: '#f6f783',
                    route: '/products'
                },
                {
                    text: 'RFID',
                    icon: 'rfid',
                    roles: ['staff', 'admin'],
                    color: '#83fba8',
                    route: '/rfid/transactions'
                },
                {
                    text: 'Machines',
                    icon: 'machines',
                    roles: ['admin', 'staff', 'developer'],
                    color: '#e4dfdd',
                    route: '/machines'
                },
                {
                    text: 'Reworks',
                    icon: 'reworks',
                    roles: ['admin'],
                    color: '#e4dfdd',
                    route: '/reworks'
                },
                {
                    text: 'Loyalty points',
                    icon: 'loyalty-points',
                    roles: ['admin'],
                    color: '#def794',
                    route: '/loyalty-points'
                },
                {
                    text: 'Discounts',
                    icon: 'discounts',
                    roles: ['admin'],
                    color: '#ddfb83',
                    route: '/discounts'
                },
                {
                    text: 'Time Keeping',
                    icon: 'time-keeping',
                    roles: ['admin', 'staff'],
                    color: '#ddfb83',
                    route: '/time-keeping'
                },
                {
                    text: 'Client',
                    icon: 'client',
                    roles: ['developer'],
                    route: '/client'
                }
            ]
        }
    },
    methods: {
        navigate(route) {
            this.$router.push(route);
        }
    },
    computed: {
        filterLinks() {
            let user = this.user;
            if(user && this.links.length) {
                let links = this.links.filter(link =>

                    // get only the links with specific roles
                    link.roles.some(role => role == user.roles[0] || role == '*') &&

                    // remove unnecessary roles
                    !link.roles.some(role => role == `!${user.roles[0]}`)).map(link => {

                        // only for links with sub tab
                        if(link.children){
                            link.childrenFiltered = link.children.filter(l => l.roles.some(role => role == user.roles[0] || role == '*'));
                        }
                        return link;
                    });

                return links;
            }
            return [];
        },
        user() {
            return this.$store.getters.getCurrentUser;
        }
    }
}
</script>

<style lang="css">
    .tile {
        cursor: pointer;
    }
</style>
