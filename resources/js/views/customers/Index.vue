<template>
    <v-container>
        <h4 class="title grey--text">Customers</h4>
        <v-divider class="my-3"></v-divider>
        <v-btn class="primary ml-0" round @click="addCustomer">
            <v-icon small left>add</v-icon>
            Create new customer
        </v-btn>
        <form @submit.prevent="filter">
            <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
        </form>


        <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
            <template v-slot:items="props">
                <td>{{ props.item.name }}</td>
                <td>{{ props.item.contact_number }}</td>
                <td>{{ props.item.email }}</td>
                <td>{{ props.item.address }}</td>
                <td>{{ date(props.item.birthday) }}</td>
                <td>{{props.item.available_wash}}</td>
                <td>{{props.item.available_dry}}</td>
                <td>
                    <span v-if="props.item.earned_points">
                        {{props.item.earned_points.toFixed(2)}}
                    </span>
                </td>
                <td>
                    <v-tooltip top>
                        <v-btn slot="activator" small icon @click="editCustomer(props.item)">
                            <v-icon small>edit</v-icon>
                        </v-btn>
                        <span>Edit details</span>
                    </v-tooltip>
                    <v-tooltip top v-if="props.item.rfid_cards_count > 0">
                        <v-btn slot="activator" small icon :to="`/rfid/cards/c?customerId=${props.item.id}`">
                            <v-icon small>credit_card</v-icon>
                            {{props.item.rfid_cards_count}}
                        </v-btn>
                        <span>{{props.item.rfid_cards_count}} Registered RFID card(s)</span>
                    </v-tooltip>
                </td>
            </template>
        </v-data-table>
        <!-- <customer-dialog v-model="openCustomerDialog" :customer="activeCustomer" @save="editContinue"></customer-dialog> -->
    </v-container>
</template>

<script>
// import CustomerDialog from './CustomerDialog.vue';
import moment from 'moment';

export default {
    components: {
        // CustomerDialog
    },
    data() {
        return {
            keyword: this.$route.query.keyword,
            page: parseInt(this.$route.query.page) || 1,
            loading: false,
            totalPage: 0,
            items: [],
            activeCustomer: null,
            openCustomerDialog: false,
            headers: [
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Contact No.',
                    sortable: false
                },
                {
                    text: 'Email',
                    sortable: false
                },
                {
                    text: 'Address',
                    sortable: false
                },
                {
                    text: 'Birth day',
                    sortable: false
                },
                {
                    text: 'Available wash',
                    sortable: false
                },
                {
                    text: 'Available dry',
                    sortable: false
                },
                {
                    text: 'Earned points',
                    sortable: false
                },
                {
                    text: 'Actions',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        filter() {
            this.page = 1;
            this.load();
        },
        load() {
            if(this.loading) return;

            this.$router.push({
                query: {
                    keyword: this.keyword,
                    page: this.page
                }
            });

            this.loading = true;

            axios.get('/api/customers', {
                params: {
                    keyword: this.keyword,
                    page: this.page
                }
            }).then((res, rej) => {
                console.log(res.data);
                this.items = res.data.result.data;
                this.totalPage = res.data.result.last_page;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            });
        },
        navigate(page) {
            this.page = page;
            this.load();
        },
        editCustomer(customer) {
            this.activeCustomer = customer;
            this.openCustomerDialog = true;
        },
        editContinue(data) {
            if(data.mode == 'insert') {
                this.items.push(data.customer);
            } else {
                this.activeCustomer.name = data.customer.name;
                this.activeCustomer.contact_number = data.customer.contact_number;
                this.activeCustomer.email = data.customer.email;
                this.activeCustomer.address = data.customer.address;
                this.activeCustomer.birthday = data.customer.birthday;
            }
        },
        addCustomer() {
            this.activeCustomer = null;
            this.openCustomerDialog = true;
        },
        date(date) {
            let _date = moment(date);
            return _date.isValid() ? _date.format('MMM D, YY') : date;
        }
    },
    computed: {
        isAdmin() {
            let user = this.$store.getters.getCurrentUser;
            console.log('admin', user);
            if(user) {
                return user.roles.some(r => r == 'admin');
            }
        }
    },
    created() {
        this.load();
    }
}
</script>
