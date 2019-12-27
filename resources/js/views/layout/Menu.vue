<template>
    <div>
        <flash-message />
        <v-toolbar flat dark app color="red">
            <v-toolbar-side-icon v-if="!!user" @click="drawer = !drawer"></v-toolbar-side-icon>

            <v-toolbar-title v-if="!!user">
                <v-progress-circular v-if="loadingBranch" indeterminate></v-progress-circular>
                <span class="font-weight-light" v-else>
                    {{branchName}}
                </span>
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <template v-if="!!user">

                <span>{{user.roles[0] | uppercase}}</span>
                <v-btn flat fab small @click="logout" :loading="isLoggingOut">
                    <v-icon>exit_to_app</v-icon>
                </v-btn>
            </template>

            <template v-else>
                <v-btn flat router to="/login">Login</v-btn>
            </template>

        </v-toolbar>



        <v-navigation-drawer app v-model="drawer" :stateless="!needsToHide" fixed v-if="!!user" class="white">
            <v-card v-if="!!user" flat class="pa-1 my-2 text-xs-center white"  router to="/account">
                <v-responsive>
                    <v-avatar size="40" class="grey">
                        <img src="/img/Abstract.jpg" alt="">
                    </v-avatar>
                </v-responsive>

                <v-card-text>
                    <div class="text-xs-center">{{user.fullname}}</div>
                </v-card-text>

            </v-card>
            <v-divider></v-divider>

            <v-list>
            <template v-for="(link, i) in filterLinks">
                <v-list-group :key="i" v-if="link.childrenFiltered">
                    <v-list-tile slot="activator" :to="link.to" active-class="black--text darken-4" class="">
                        <v-list-tile-action><v-icon>{{link.icon}}</v-icon></v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{link.text}}</v-list-tile-title>
                        </v-list-tile-content>
                        <v-list-tile-action v-if="link.btn">
                            <v-tooltip right>
                                <v-btn fab flat slot="activator">
                                    <router-link :to="link.btn.to" tag="v-icon" v-text="link.btn.icon"></router-link>
                                </v-btn>
                                <span>{{link.btn.text}}</span>
                            </v-tooltip>
                        </v-list-tile-action>
                    </v-list-tile>

                    <v-list-tile v-for="(child_link, i) in link.childrenFiltered" :key="i" class="grey lighten-4 pl-4" color="#888" active-class="black--text grey lighten-3" :to="child_link.to">
                        <v-list-tile-action>
                            <v-icon small color="#555">{{child_link.icon}}</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{child_link.text}}</v-list-tile-title>
                        </v-list-tile-content>
                        <v-list-tile-action v-if="child_link.btn">
                            <v-tooltip right>
                                <v-btn fab flat small slot="activator">
                                    <router-link :to="child_link.btn.to" tag="v-icon" v-text="child_link.btn.icon"></router-link>
                                </v-btn>
                                <span>{{child_link.btn.text}}</span>
                            </v-tooltip>
                        </v-list-tile-action>

                    </v-list-tile>
                </v-list-group>

                <v-list-tile :key="i" v-else router :to="link.to" active-class="grey--text darken-4" class="black--text">
                    <v-list-tile-action><v-icon>{{link.icon}}</v-icon></v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>{{link.text}}</v-list-tile-title>
                    </v-list-tile-content>
                    <v-list-tile-action v-if="link.btn">
                        <v-tooltip right>
                            <v-btn fab flat small slot="activator">
                                <router-link :to="link.btn.to" tag="v-icon" v-text="link.btn.icon"></router-link>
                            </v-btn>
                            <span>{{link.btn.text}}</span>
                        </v-tooltip>
                    </v-list-tile-action>
                </v-list-tile>
            </template>
        </v-list>

        </v-navigation-drawer>
    </div>
</template>

<script>
import FlashMessage from './FlashMessage.vue';
export default {
    components: {
        FlashMessage
    },
    data() {
        return {
            drawer: true,
            mini: false,
            needsToHide: true,
            links: [
                {
                    to: '/dashboard',
                    icon: 'dashboard',
                    text: 'Dashboard',
                    roles: ['admin']
                },
                {
                    to: '/pos/products',
                    icon: 'assessment',
                    text: 'POS',
                    roles: ['staff', 'oic']
                },
                {
                    to: '/remote-activation/remote-panel',
                    icon: 'settings_remote',
                    text: 'Remote activation',
                    roles: ['staff', 'oic']
                },
                {
                    to: '/reports',
                    icon: 'local_printshop',
                    text: 'Reports',
                    roles: ['*', '!developer'],
                    children: [
                        {
                            to: '/reports/transactions/pos-transactions',
                            icon: 'compare_arrows',
                            text: 'Transactions',
                            roles: ['*']
                        },
                        {
                            to: '/reports/inventories',
                            icon: 'dashboard',
                            text: 'Inventories',
                            roles: ['*']
                        },
                        {
                            to: '/reports/purchases',
                            icon: 'shopping_cart',
                            text: 'Purchases',
                            roles: ['admin', 'oic', 'staff']
                        },
                        {
                            to: '/reports/trashed/transactions',
                            icon: 'delete_sweep',
                            text: 'Trashed',
                            roles: ['admin']
                        }
                    ]
                },
                {
                    to: '/people',
                    icon: 'people',
                    text: 'People',
                    roles: ['*', '!developer'],
                    children: [
                        {
                            to: '/people/customers',
                            icon: 'people_outline',
                            text: 'Customers',
                            roles: ['*'],
                        },
                        {
                            to: '/people/users',
                            icon: 'supervisor_account',
                            text: 'Users',
                            roles: ['admin', 'oic']
                        }
                    ]
                },
                {
                    to: '/expenses',
                    icon: 'account_balance_wallet',
                    text: 'Expenses',
                    roles: ['admin', 'oic'],
                    children: [
                        {
                            to: '/expenses/expense-list',
                            icon: 'assignment',
                            text: 'Expenses list',
                            roles: ['*']
                        }
                    ]
                },
                {
                    to: '/items',
                    icon: 'assignment',
                    text: 'Items',
                    roles: ['*', '!developer'],
                    children: [
                        {
                            to: '/items/product-list',
                            icon: 'assignment',
                            text: 'Product list',
                            roles: ['*'],
                            btn: {
                                to: '/items/product/add',
                                text: 'Add',
                                icon: 'add'
                            }
                        },
                        {
                            to: '/items/service-list',
                            icon: 'work',
                            text: 'Services',
                            roles: ['*'],
                            btn: {
                                to: '/items/services/add',
                                text: 'Add',
                                icon: 'add'
                            }
                        }
                    ]
                },
                // {
                //     to: '/developer',
                //     icon: 'face',
                //     text: 'developer',
                //     roles: ['developer'],
                //     children: [
                //         {
                //             to: '/developer/clients',
                //             icon: 'recent_actors',
                //             text: 'Clients',
                //             roles: ['developer'],
                //             btn: {
                //                 to: '/developer/clients/add',
                //                 text: 'Add new client',
                //                 icon: 'add'
                //             }
                //         }
                //     ]
                // },
                {
                    to: '/rfid',
                    icon: 'credit_card',
                    text: 'RFID',
                    roles: ['!developer', '*'],
                    children: [
                        {
                            to: '/rfid/service-prices',
                            icon: '',
                            text: 'Service prices',
                            roles: ['!developer', '*']
                        },
                        {
                            to: '/rfid/cards/all',
                            icon: '',
                            text: 'RFID cards',
                            roles: ['!developer', '*']
                        },
                        {
                            to: '/rfid/transactions/services',
                            icon: 'compare_arrows',
                            text: 'Transactions',
                            roles: ['!developer', '*']
                        },
                        {
                            to: '/rfid/transactions/top-up',
                            icon: 'compare_arrows',
                            text: 'Top ups',
                            roles: ['!developer', '*']
                        }
                    ]
                },
                {
                    to: '/loyalty',
                    icon: '',
                    text: 'Loyalty',
                    roles: ['!developer', 'admin'],
                    children: [
                        {
                            to: '/loyalty/discounts',
                            text: 'Discounts',
                            icon: '',
                            roles: ['admin']
                        },
                        {
                            to: '/loyalty/points',
                            text: 'Points',
                            icon: '',
                            roles: ['admin']
                        }
                    ]
                },
                {
                    to: '/preferences',
                    icon: 'settings',
                    text: 'Preferences',
                    roles: ['!developer', 'admin'],
                    children: [
                        {
                            to: '/preferences/job-order',
                            text: 'Job order',
                            icon: 'receipt',
                            roles: ['admin']
                        }
                    ]
                }
            ]
        }
    },
    computed: {
        user() {
            return this.$store.getters.getCurrentUser;
        },
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
        isLoggingOut() {
            return this.$store.getters['auth/getLoggingOut'];
        },
        branchName() {
            let activeBranch = this.$store.getters['getActiveBranch'];
            if(activeBranch != null) {
                return activeBranch.name;
            } else if(!this.loadingBranch) {
                return 'Branch not set';
            }
        },
        loadingBranch() {
            return this.$store.getters['branch/isSelectingBranch'];
        }
    },
    methods: {
        onResize () {
            this.needsToHide = window.innerWidth < 800
        },
        logout() {
            this.$store.dispatch('auth/logout').then((res, rej) => {
                this.$router.push('/login');
            });
        }
    },
    beforeDestroy () {
        if (typeof window !== 'undefined') {
            window.removeEventListener('resize', this.onResize, { passive: true })
        }
    },
    mounted () {
        if(window.innerWidth <= 800) {
            this.drawer = false;
        }
        this.onResize()
        window.addEventListener('resize', this.onResize, { passive: true });
    }

}
</script>
