<template>
    <v-card flat>
        <v-card-text>
            <form @submit.prevent="filter">
                <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
            </form>
            <v-divider></v-divider>

            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                <template v-slot:items="props">
                    <td>{{ date(props.item.created_at) }}</td>
                    <td>{{ props.item.serviceName }}</td>
                    <td>{{ props.item.customerName }}</td>
                    <td>{{ props.item.jobOrder }}</td>
                    <td>{{ props.item.remarks }}</td>
                    <td>{{ props.item.userName }}</td>
                </template>
            </v-data-table>
        </v-card-text>

        <v-divider class="my-2"></v-divider>
        <v-card-actions>
            <v-pagination v-if="totalPage > 1" :length="totalPage" v-model="page" @input="navigate" total-visible="6"></v-pagination>
            <v-spacer></v-spacer>
            <!-- <v-btn @click="exportDownload">
                <v-icon left>archive</v-icon> Download
            </v-btn> -->
        </v-card-actions>
    </v-card>
</template>
<script>
import moment from 'moment';

export default {
    data() {
        return {
            keyword: this.$route.query.keyword,
            page: this.$route.query.page || 1,
            loading: false,
            totalPage: 0,
            items: [],
            headers: [
                {
                    text: 'Date',
                    sortable: false
                },
                {
                    text: 'Service name',
                    sortable: false
                },
                {
                    text: 'Customer name.',
                    sortable: false
                },
                {
                    text: 'Job order',
                    sortable: false
                },
                {
                    text: 'Remarks',
                    sortable: false
                },
                {
                    text: 'User',
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

            // this.$router.push({
            //     query: {
            //         keyword: this.keyword,
            //         page: this.page
            //     }
            // });

            this.loading = true;

            axios.get('/api/search/trashed/transaction-services', {
                params: {
                    keyword: this.keyword,
                    page: this.page //,
                    // transactionId: this.$route.query.transactionId ? this.$route.query.transactionId : null
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
        // exportDownload() {
        //     axios.get('/api/exports/pos-services/self', {
        //         params: {
        //             keyword: this.keyword,
        //             page: this.page,
        //             transactionId: this.$route.query.transactionId ? this.$route.query.transactionId : null
        //         },
        //         responseType: 'blob'
        //     }).then((res, rej) => {
        //         console.log(res.data);
        //         const downloadUrl = window.URL.createObjectURL(new Blob([res.data]));
        //         const link = document.createElement('a');
        //         link.href = downloadUrl;
        //         link.setAttribute('download', 'file.xls'); //any other extension
        //         document.body.appendChild(link);
        //         link.click();
        //         link.remove();
        //     }).catch(err => {
        //         this.loading = false;
        //     });
        // },
        date(date) {
            let _date = moment(date);
            return _date.isValid() ? _date.format('MMM D, YY') : date;
        // },
        // voidItem(service) {
        //     this.activeTransaction = service;
        //     this.openVoidService = true;
        // },
        // removeItem(serviceId) {
        //     this.items = this.items.filter(i => i.id != serviceId);
        }
    },
    created() {
        this.load();
    }
}
</script>
