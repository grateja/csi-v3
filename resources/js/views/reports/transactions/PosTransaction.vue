<template>
    <div>
        <v-divider class="my-3"></v-divider>
        <v-card>
            <v-card-text>

                <div>
                    <p>View by:
                        <v-btn small flat outline to="/reports/transactions/pos-transactions/by-customers" class="grey" active-class="primary blue--text">Job order</v-btn>
                        <v-btn small flat outline to="/reports/transactions/pos-transactions/by-items" class="grey" active-class="primary blue--text">Items</v-btn>
                    </p>
                </div>

                <router-view></router-view>

                <!-- <form @submit.prevent="filter">
                    <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
                </form>
                <v-divider></v-divider>

                <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                    <template v-slot:items="props">
                        <td>{{ props.item.date }}</td>
                        <td>{{ props.item.customerName }}</td>
                        <td>{{ props.item.jobOrder }}</td>
                        <td>{{ props.item.datePaid }}</td>
                        <td>{{ props.item.paidTo }}</td>
                        <td>
                            <v-tooltip top>
                                <v-btn slot="activator" small icon @click="editCustomer(props.item)">
                                    <v-icon small>edit</v-icon>
                                </v-btn>
                                <span>Edit details</span>
                            </v-tooltip>
                            <v-tooltip top v-if="props.item.rfid_cards_count > 0">
                                <v-btn slot="activator" small icon :to="`/rfid/cards/c?keyword=${props.item.name}`">
                                    <v-icon small>credit_card</v-icon>
                                    {{props.item.rfid_cards_count}}
                                </v-btn>
                                <span>{{props.item.rfid_cards_count}} Registered RFID card(s)</span>
                            </v-tooltip>
                        </td>
                    </template>
                </v-data-table>

                <v-divider class="my-2"></v-divider>

                <v-pagination v-if="totalPage > 1" :length="totalPage" v-model="page" @input="navigate"></v-pagination> -->
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
// export default {
//     data() {
//         return {
//             keyword: this.$route.query.keyword,
//             page: this.$route.query.page || 1,
//             loading: false,
//             totalPage: 0,
//             items: [],
//             activeTransaction: null,
//             openTransactionDialog: false,
//             headers: [
//                 {
//                     text: 'Date',
//                     sortable: false
//                 },
//                 {
//                     text: 'Customer name.',
//                     sortable: false
//                 },
//                 {
//                     text: 'Job order',
//                     sortable: false
//                 },
//                 {
//                     text: 'Date paid',
//                     sortable: false
//                 },
//                 {
//                     text: 'Paid to',
//                     sortable: false
//                 },
//                 {
//                     text: 'Actions',
//                     sortable: false
//                 }
//             ]
//         }
//     },
//     methods: {
//         filter() {
//             this.page = 1;
//             this.load();
//         },
//         load() {
//             if(this.loading) return;

//             this.$router.push({
//                 query: {
//                     keyword: this.keyword,
//                     page: this.page
//                 }
//             });

//             this.loading = true;

//             axios.get('/api/search/transactions/pos/self', {
//                 params: {
//                     keyword: this.keyword,
//                     page: this.page
//                 }
//             }).then((res, rej) => {
//                 console.log(res.data);
//                 this.items = res.data.result.data;
//                 this.totalPage = res.data.result.last_page;
//                 this.loading = false;
//             }).catch(err => {
//                 this.loading = false;
//             });
//         },
//         navigate(page) {
//             this.page = page;
//             this.load();
//         }
//     },
//     computed: {
//         isAdmin() {
//             let user = this.$store.getters.getCurrentUser;
//             console.log('admin', user);
//             if(user) {
//                 return user.roles.some(r => r == 'admin');
//             }
//         }
//     },
//     created() {
//         this.load();
//     }
// }
</script>
