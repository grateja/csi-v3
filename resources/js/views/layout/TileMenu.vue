<template>
    <v-container>
        <v-card v-if="!!user" color="transparent" flat>
            <v-layout row wrap>
                <v-flex xs6 sm4 md3 xl2 v-for="(link, i) in filterLinks" :key="i">
                    <v-card class="ma-2 tile" :color="link.color" @click="navigate(link.route)">
                        <v-card-text>
                            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Tempora quo nostrum dolores adipisci labore impedit debitis voluptas quia, voluptatem quas harum eaque laborum placeat vel dolorum aliquam quasi quod amet.
                            <div class="title text-xs-center ma-2">{{link.text}}</div>
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-card>
        <v-card v-else>
            unauthorized
        </v-card>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            links: [
                {
                    text: 'New transaction',
                    icon: '',
                    roles: ['staff', 'admin'],
                    color: '#33cbff',
                    route: '/new-transaction/services'
                },
                {
                    text: 'Remote panel',
                    icon: '',
                    roles: ['staff', 'admin'],
                    color: '#ff93ef',
                    route: '/remote-panel'
                },
                {
                    text: 'Sales report',
                    icon: '',
                    roles: ['admin'],
                    color: '#a7a1e6',
                    route: '/sales-report/pos-transactions'
                },
                {
                    text: 'Transactions',
                    icon: '',
                    roles: ['staff', 'admin'],
                    color: '#cfe6a1',
                    route: '/transaction-reports/by-job-orders'
                },
                {
                    text: 'Unpaid Transactions',
                    icon: '',
                    roles: ['staff', 'admin'],
                    color: '#ace6a1',
                    route: '/unpaid-transactions'
                },
                {
                    text: 'Pending services',
                    icon: '',
                    roles: ['staff', 'admin'],
                    color: '#c4eabd',
                    route: '/pending-services'
                },
                {
                    text: 'Customers',
                    icon: '',
                    roles: ['staff', 'admin'],
                    color: '#a1e6d9',
                    route: '/customers'
                },
                {
                    text: 'Users',
                    icon: '',
                    roles: ['admin'],
                    color: '#bcefe5',
                    route: '/users'
                },
                {
                    text: 'Product Purchases',
                    icon: '',
                    roles: ['staff', 'admin'],
                    color: '#83adfb',
                    route: '/product-purchases'
                },
                {
                    text: 'Expenses',
                    icon: '',
                    roles: ['staff', 'admin'],
                    color: '#95b9fb',
                    route: '/expenses'
                },
                {
                    text: 'Services',
                    icon: '',
                    roles: ['admin', 'staff'],
                    color: '#fbf783',
                    route: '/services/washing-services'
                },
                {
                    text: 'Products',
                    icon: '',
                    roles: ['admin', 'staff'],
                    color: '#f6f783',
                    route: '/products'
                },
                {
                    text: 'RFID',
                    icon: '',
                    roles: ['staff', 'admin'],
                    color: '#83fba8',
                    route: '/rfid/transactions'
                },
                {
                    text: 'Machines',
                    icon: '',
                    roles: ['admin', 'staff'],
                    color: '#e4dfdd',
                    route: '/machines'
                },
                {
                    text: 'Loyalty points',
                    icon: '',
                    roles: ['admin'],
                    color: '#def794',
                    route: '/loyalty-points'
                },
                {
                    text: 'Discounts',
                    icon: '',
                    roles: ['admin'],
                    color: '#ddfb83',
                    route: '/discounts'
                },
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
