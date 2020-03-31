<template>
    <div>
        <flash-message />
        <v-toolbar flat dark app class="green lighten-1">
            <v-toolbar-side-icon v-if="!!user" @click="drawer = !drawer"></v-toolbar-side-icon>

            <v-toolbar-title v-if="!!user">
                <v-btn large to="/" flat class="ml-0 pl-0">
                    <v-icon left large>apps</v-icon> MENU
                </v-btn>
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <template v-if="!!user">
                <v-btn v-if="isOwner" @click="openShopPreferences = true" :icon="$vuetify.breakpoint.width < 580">
                    <v-icon :left="$vuetify.breakpoint.width > 580">store</v-icon>
                    <span v-if="$vuetify.breakpoint.width > 580">
                        shop preferences
                    </span>
                </v-btn>
                <v-btn to="/account">
                    <span>{{user.roles[0] | uppercase}}</span>
                </v-btn>
                <v-btn flat small @click="logout" :loading="isLoggingOut">
                    Logout<v-icon right>close</v-icon>
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
        <shop-preferences-dialog v-model="openShopPreferences" />
    </div>
</template>

<script>
import FlashMessage from './FlashMessage.vue';
import ShopPreferencesDialog from './ShopPreferencesDialog.vue';
export default {
    components: {
        FlashMessage,
        ShopPreferencesDialog
    },
    data() {
        return {
            drawer: true,
            mini: false,
            needsToHide: true,
            openShopPreferences: false,
            links: [
                {
                    text: 'Clients',
                    icon: '',
                    roles: ['developer'],
                    color: '#a7a1e6',
                    to: '/clients'
                },
                {
                    text: 'New transaction',
                    icon: 'computer',
                    roles: ['admin', 'staff'],
                    color: '#a7a1e6',
                    to: '/new-transaction/services'
                },
                {
                    text: 'Remote panel',
                    icon: 'cast_connected',
                    roles: ['admin', 'staff'],
                    color: '#a7a1e6',
                    to: '/remote-panel'
                },
                {
                    text: 'Sales report',
                    icon: 'event_note',
                    roles: ['admin'],
                    color: '#a7a1e6',
                    to: '/sales-report/pos-transactions'
                },
                {
                    text: 'Transactions',
                    icon: 'receipt',
                    roles: ['staff', 'admin'],
                    color: '#cfe6a1',
                    to: '/transaction-reports/by-job-orders'
                },
                {
                    text: 'Unpaid Transactions',
                    icon: 'announcement',
                    roles: ['staff', 'admin'],
                    color: '#ace6a1',
                    to: '/unpaid-transactions'
                },
                {
                    text: 'Pending services',
                    icon: 'format_list_numbered',
                    roles: ['staff', 'admin'],
                    color: '#ace6a1',
                    to: '/pending-services'
                },
                {
                    text: 'Customers',
                    icon: 'people',
                    roles: ['staff', 'admin'],
                    color: '#a1e6d9',
                    to: '/customers'
                },
                {
                    text: 'Users',
                    icon: 'people',
                    roles: ['admin'],
                    color: '#a1e6d9',
                    to: '/users'
                },
                {
                    text: 'Services',
                    icon: 'toc',
                    roles: ['staff', 'admin'],
                    color: '#95b9fb',
                    to: '/services/washing-services'
                },
                {
                    text: 'Products',
                    icon: 'list_alt',
                    roles: ['staff', 'admin'],
                    color: '#83adfb',
                    to: '/products'
                },
                {
                    text: 'Product Purchases',
                    icon: 'playlist_add',
                    roles: ['staff', 'admin'],
                    color: '#83adfb',
                    to: '/product-purchases'
                },
                {
                    text: 'Expenses',
                    icon: 'toc',
                    roles: ['staff', 'admin'],
                    color: '#95b9fb',
                    to: '/expenses'
                },
                {
                    text: 'RFID',
                    icon: 'phonelink_ring',
                    roles: ['staff', 'admin'],
                    color: '#83fba8',
                    to: '/rfid/transactions'
                },
                {
                    text: 'Machines',
                    icon: 'local_laundry_service',
                    roles: ['admin', 'staff'],
                    color: '#e4dfdd',
                    to: '/machines'
                },
                {
                    text: 'Loyalty points',
                    icon: 'star',
                    roles: ['admin'],
                    color: '#e4dfdd',
                    to: '/loyalty-points'
                },
                {
                    text: 'Discounts',
                    icon: 'loyalty',
                    roles: ['admin'],
                    color: '#e4dfdd',
                    to: '/discounts'
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
        shopName() {
            if(this.user) {
                return this.user.name;
            }
            // let activeBranch = this.$store.getters['getActiveBranch'];
            // if(activeBranch != null) {
            //     return activeBranch.name;
            // } else if(!this.loadingBranch) {
            //     return 'Branch not set';
            // }
        },
        loadingBranch() {
            return this.$store.getters['branch/isSelectingBranch'];
        },
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            if(user) {
                return user.roles.some(r => r == 'admin');
            }
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
