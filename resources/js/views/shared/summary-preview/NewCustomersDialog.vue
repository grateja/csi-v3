<template>
    <v-dialog :value="value" max-width="640">
        <v-card>
            <v-card-title>
                <span class="title">New customers</span>
                <v-spacer></v-spacer>
                <v-btn icon @click="$emit('input', false)">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-card class="rounded-card translucent-table" style="overflow-y: auto; max-height: 70vh;">
                    <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                        <template v-slot:items="props">
                            <td>{{props.index + 1}}</td>
                            <td><div class="font-weight-bold">{{ props.item.name }}</div>
                                <div class="ml-4 mb-2">
                                    <div v-if="props.item.organization">{{props.item.organization}}</div>
                                    <div class="font-italic caption">{{ props.item.contact_number || '[no-contact-number]' }} / {{ props.item.email || '[no-email]' }}</div>
                                    <div>{{ props.item.address || '[no-address]' }}</div>
                                    <div>{{ props.item.remarks }}</div>
                                </div>
                            </td>
                            <!-- <td>{{ props.item.contact_number }}</td> -->
                            <!-- <td>{{ props.item.email }}</td> -->
                            <!-- <td>{{ props.item.address }}</td> -->
                            <td>{{ props.item.first_visit | simpleDate }}</td>
                            <td>{{ props.item.created_at | simpleDate }}</td>
                            <td>{{props.item.customer_washes_count}}</td>
                            <td>{{props.item.customer_dries_count}}</td>
                            <td>
                                <span v-if="props.item.earned_points">
                                    {{props.item.earned_points.toFixed(2)}}
                                </span>
                            </td>
                        </template>
                        <template slot="footer">
                            <tr v-if="totalResult">
                                <td colspan="10">
                                    <div class="font-italic">Showing <span class="font-weight-bold">{{items.length}}</span> item(s) out of <span class="font-weight-bold">{{totalResult}}</span> items(s)</div>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </v-card>
                <v-btn block @click="loadMore" :loading="loading" round class="translucent">Load more</v-btn>
                <v-card-actions>
                    <v-spacer />
                    <v-btn @click="excelExportContinue">
                        <v-img src="/img/excel-btn.png" width="30px" />
                        export
                    </v-btn>
                    <v-spacer />
                </v-card-actions>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'date', 'until',
    ],
    data() {
        return {
            items: [],
            loading: false,
            reset: true,
            cancelSource: null,
            page: 1,
            totalPage: 0,
            totalResult: 0,
            export: null,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Name',
                    sortable: false
                },
                {
                    text: 'Birthday',
                    sortable: false
                },
                {
                    text: 'First Visit',
                    sortable: false
                },
                {
                    text: 'Total wash',
                    sortable: false
                },
                {
                    text: 'Total dry',
                    sortable: false
                },
                {
                    text: 'Earned points',
                    sortable: false
                },
            ]
        }
    },
    methods: {
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();

            axios.get('/api/customers/new-customers', {
                params: {
                    page: this.page,
                    date: this.date,
                    until: this.until,
                    export: this.export
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                if(this.reset) {
                    this.items = res.data.result.data;
                    console.log('reset')
                } else {
                    console.log('not reset')
                    console.log('old items', this.items)
                    console.log('new items', res.data.result.data)
                    this.items = [...this.items, ...res.data.result.data]
                    console.log('merged', this.items);
                    // setTimeout(() => {
                    //     window.scrollTo({
                    //         top: document.body.scrollHeight,
                    //         behavior: 'smooth'
                    //     });
                    // }, 10);
                }
                this.totalResult = res.data.result.total;
                this.totalPage = res.data.result.last_page;
                this.loading = false;
            }).finally(() => {
                this.export = null;
            });
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        loadMore() {
            this.page += 1;
            this.reset = false;
            this.load();
        },
        excelExportContinue() {
            this.export = 'excel';
            this.$store.dispatch('exportdownload/download', {
                uri: 'new-customers',
                params: {
                    date: this.date,
                    until: this.until,
                }
            })
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.page = 1
                this.load();
            }
        }
    }
}
</script>