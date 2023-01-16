<template>
    <div>
        <flash-message />
        <v-hover v-slot:default="{ hover }">
            <v-toolbar flat app class="translucent" :style=" monitorView&&!hover ? 'opacity: 0' : 'opacity: 1' ">
                <!-- <v-toolbar-side-icon v-if="!!user" @click="drawer = !drawer"></v-toolbar-side-icon> -->
                <v-btn icon @click="drawer = !drawer" class="translucent">
                    <v-icon v-if="!drawer" color="white">keyboard_arrow_right</v-icon>
                    <v-icon v-else color="white">keyboard_arrow_left</v-icon>
                </v-btn>
                <v-toolbar-title v-if="!!user">
                    <v-btn large to="/" round class="translucent">
                        <!-- <v-icon left large>apps</v-icon> MENU -->
                        <v-img src="/img/dos-icons/menu.png" width="30" />
                        <span class="mx-3">menu</span>
                    </v-btn>
                </v-toolbar-title>

                <v-spacer></v-spacer>

                <v-btn icon @click="showRfidTerminal = true">
                    <v-icon>tap_and_play</v-icon>
                </v-btn>

                <template v-if="!!user">
                    <v-btn v-if="isOwner" @click="openShopPreferences = true" :icon="$vuetify.breakpoint.width < 720" class="translucent" round>
                        <!-- <v-icon :left="$vuetify.breakpoint.width > 720">store</v-icon> -->
                        <v-img src="/img/dos-icons/preferences.png" width="25" :left="$vuetify.breakpoint.width > 720" class="mx-2" />
                        <span v-if="$vuetify.breakpoint.width > 720">
                            shop preferences
                        </span>
                    </v-btn>
                    <v-btn to="/account" class="translucent" round>
                        <span>{{user.roles[0] | uppercase}}</span>
                    </v-btn>
                    <v-btn flat small @click="logout" :loading="isLoggingOut">
                        Logout<v-icon right>close</v-icon>
                    </v-btn>
                </template>

                <template v-else>
                    <v-btn flat router to="/login">Login</v-btn>
                </template>

                <!-- <v-btn icon class="translucent" @click="toggleMonitorView" v-if="!needsToHide" :to="monitorView ? '/' : '/monitor-view'">
                    <v-icon v-if="monitorView">phonelink_off</v-icon>
                    <v-icon v-else>phonelink</v-icon>
                </v-btn> -->

            </v-toolbar>
        </v-hover>

        <v-navigation-drawer app v-model="drawer" :stateless="!needsToHide" fixed v-if="!!user" :class="{'transparent' : $vuetify.breakpoint.width > 800}">
            <v-card v-if="!!user" flat class="pa-1 my-2 text-xs-center transparent"  router to="/account">
                <!-- <v-responsive>
                    <v-avatar size="40" class="grey">
                        <img src="/img/Abstract.jpg" alt="">
                    </v-avatar>
                </v-responsive> -->

                <v-card-text>
                    <div class="text-xs-center">{{user.fullname}}</div>
                </v-card-text>

            </v-card>
            <!-- <v-divider></v-divider> -->

            <!-- <v-list>
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
        </v-list> -->

        <v-divider class="my-4 transparent"></v-divider>
        <div v-for="(link, i) in filterLinks" :key="i" class="pl-4 ml-4">
            <v-btn round class="menu-iddle" :to="link.to" active-class="menu-active" flat>
                <!-- <v-responsive> -->
                    <!-- <v-avatar size="20" class="transparent mx-2"> -->
                        <img :src="`/img/dos-icons/${link.icon}.png`" alt="" width="20" class="mt-1 mx-2" />
                    <!-- </v-avatar> -->
                <!-- </v-responsive> -->
                <span class="mx-2">
                    {{link.text}}
                </span>
            </v-btn>
        </div>
        <div v-if="dopuSetup == 'master' && isOwner" class="pl-4 ml-4">
            <v-btn round class="menu-iddle" to="/lagoon-partners" active-class="menu-active" flat>
                <!-- <v-responsive> -->
                    <!-- <v-avatar size="20" class="transparent mx-2"> -->
                        <!-- <img :src="`/img/dos-icons/${link.icon}.png`" alt="" width="20" class="mt-1 mx-2" /> -->
                    <!-- </v-avatar> -->
                <!-- </v-responsive> -->
                <span class="mx-2">
                    Lagoon Partners
                </span>
            </v-btn>
        </div>

        </v-navigation-drawer>
        <!-- <div class="rfid-load-checker">
            <v-btn icon @click="showRfidTerminal = true" primary>
                <v-icon>tap_and_play</v-icon>
            </v-btn>
        </div> -->
        <shop-preferences-dialog v-model="openShopPreferences" />
        <rfid-terminal v-model="showRfidTerminal" @select="showRfidDetail" />
        <card-detail-dialog v-model="openCardDetail" :rfid="rfid" />
    </div>
</template>

<script>
import FlashMessage from './FlashMessage.vue';
import ShopPreferencesDialog from './ShopPreferencesDialog.vue';
import RfidTerminal from './RfidTerminal.vue';
import CardDetailDialog from './CardDetailDialog.vue';

export default {
    components: {
        FlashMessage,
        ShopPreferencesDialog,
        RfidTerminal,
        CardDetailDialog,
    },
    data() {
        return {
            rfid: '',
            monitorView: false,
            drawer: true,
            mini: false,
            needsToHide: true,
            openShopPreferences: false,
            showRfidTerminal: false,
            openCardDetail: false,
            links: [
                {
                    text: 'Clients',
                    icon: '',
                    roles: ['developer'],
                    color: '#a7a1e6',
                    to: '/client'
                },
                {
                    text: 'New Job Order',
                    icon: 'new-transaction',
                    roles: ['admin', 'staff'],
                    color: '#a7a1e6',
                    to: '/new-transaction/services'
                },
                {
                    text: 'Remote panel',
                    icon: 'remote-panel',
                    roles: ['admin', 'staff', 'developer'],
                    color: '#a7a1e6',
                    to: '/remote-panel'
                },
                {
                    text: 'Transaction report',
                    icon: 'sales-report',
                    roles: ['admin'],
                    color: '#a7a1e6',
                    to: '/sales-report/calendar-view'
                },
                {
                    text: 'Job Orders',
                    icon: 'job-orders',
                    roles: ['staff', 'admin'],
                    color: '#cfe6a1',
                    to: '/transaction-reports/by-job-orders'
                },
                {
                    text: 'Unpaid',
                    icon: 'unpaid-transactions',
                    roles: ['staff', 'admin'],
                    color: '#ace6a1',
                    to: '/unpaid-transactions'
                },
                {
                    text: 'Pending services',
                    icon: 'pending-services',
                    roles: ['staff', 'admin'],
                    color: '#ace6a1',
                    to: '/pending-services'
                },
                {
                    text: 'Customers',
                    icon: 'customers',
                    roles: ['staff', 'admin'],
                    color: '#a1e6d9',
                    to: '/customers'
                },
                {
                    text: 'Users',
                    icon: 'users',
                    roles: ['admin', 'developer'],
                    color: '#a1e6d9',
                    to: '/users'
                },
                {
                    text: 'Services',
                    icon: 'services',
                    roles: ['admin'],
                    color: '#95b9fb',
                    to: '/services/washing-services'
                },
                {
                    text: 'Products',
                    icon: 'products',
                    roles: ['staff', 'admin'],
                    color: '#83adfb',
                    to: '/products'
                },
                {
                    text: 'Inventory Log',
                    icon: 'product-purchases',
                    roles: ['staff', 'admin'],
                    color: '#83adfb',
                    to: '/product-purchases'
                },
                {
                    text: 'Expenses',
                    icon: 'expenses',
                    roles: ['staff', 'admin'],
                    color: '#95b9fb',
                    to: '/expenses'
                },
                {
                    text: 'RFID',
                    icon: 'rfid',
                    roles: ['staff', 'admin'],
                    color: '#83fba8',
                    to: '/rfid/transactions'
                },
                {
                    text: 'Machines',
                    icon: 'machines',
                    roles: ['admin', 'staff', 'developer'],
                    color: '#e4dfdd',
                    to: '/machines'
                },
                {
                    text: 'Reworks',
                    icon: 'reworks',
                    roles: ['admin'],
                    color: '#e4dfdd',
                    to: '/reworks'
                },
                {
                    text: 'Loyalty points',
                    icon: 'loyalty-points',
                    roles: ['admin'],
                    color: '#e4dfdd',
                    to: '/loyalty-points'
                },
                {
                    text: 'Discounts',
                    icon: 'discounts',
                    roles: ['admin'],
                    color: '#e4dfdd',
                    to: '/discounts'
                },
                {
                    text: 'Time Keeping',
                    icon: 'time-keeping',
                    roles: ['admin', 'staff'],
                    to: '/time-keeping'
                },
                // {
                //     text: 'Lagoon Partners',
                //     icon: 'lagoon-partners',
                //     roles: ['admin'],
                //     to: '/lagoon-partners'
                // },
                {
                    text: 'External View',
                    icon: '',
                    roles: ['admin'],
                    to: '/external-view/events'
                },
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
                    link.roles.some(role => (role == user.roles[0] || role == '*')) &&

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
        dopuSetup() {
            return this.$store.getters.getDopuSetup;
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
        },
        toggleMonitorView() {
            this.monitorView = !this.monitorView
            this.drawer = !this.monitorView
        },
        showRfidDetail(rfid) {
            this.rfid = rfid;
            this.openCardDetail = true;
            console.log("rfid from menu", rfid)
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

<style scoped>
.rfid-load-checker {
    z-index: 99999;
    position: fixed;
    top: 70px;
    right: 25px;
}
</style>