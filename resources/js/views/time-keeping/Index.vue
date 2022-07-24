<template>
    <v-container>
        <h3 class="title white--text">Time Keeping</h3>
        <v-divider class="my-3"></v-divider>

        <template v-if="isOwner">
            <div>

                <v-layout justify-center>
                    <v-flex style="max-width: 500px">
                        <v-text-field class="ml-1 translucent-input round-input" label="Search staff name" v-model="keyword" append-icon="search" @keyup="filter" outline></v-text-field>
                    </v-flex>
                </v-layout>
                <v-layout justify-center>
                    <v-flex style="max-width: 220px">
                        <v-text-field class="translucent-input round-input" label="Specify date" v-model="date" type="date" append-icon="date" @change="filter" outline></v-text-field>
                    </v-flex>
                    <v-flex style="max-width: 220px">
                        <v-combobox class="ml-1 translucent-input round-input" label="Sort by" v-model="sortBy" outline :items="['Staff name', 'Time in', 'Time out']" @change="filter"></v-combobox>
                    </v-flex>
                    <v-flex style="max-width: 220px">
                        <v-combobox class="ml-1 translucent-input round-input" label="Order" v-model="orderBy" outline :items="['asc', 'desc']" @change="filter"></v-combobox>
                    </v-flex>
                </v-layout>


                <v-card class="rounded-card translucent-table">
                    <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                        <template v-slot:items="props">
                            <tr>
                                <td>{{props.index + 1}}</td>
                                <td>{{ props.item.user_name }}</td>
                                <td>{{ moment(props.item.created_at).format('LLL') }}</td>
                                <td>{{ props.item.time_out ? moment(props.item.time_out).format('LLL') : '[N/A]' }}</td>
                            </tr>
                        </template>
                        <template slot="footer">
                            <tr>
                                <td colspan="10">
                                    <div class="font-italic">Showing <span class="font-weight-bold">{{items.length}}</span> item(s) out of <span class="font-weight-bold">{{total}}</span> result(s)</div>
                                </td>
                            </tr>
                        </template>
                    </v-data-table>
                </v-card>
                <v-btn block @click="loadMore" :loading="loading" round class="translucent">Load more</v-btn>


            </div>
        </template>

        <template v-else>
            <div>
                <v-card v-if="tito" class="translucent rounded-card" max-width="480px">
                    <v-card-text>
                        <div class="caption">Time in</div>
                        <v-divider class="mt-1 mb-2"></v-divider>
                        <div class="title">
                            {{moment(tito.created_at).format('LLLL')}}
                        </div>
                    </v-card-text>
                    <v-divider class="my-2 transparent"></v-divider>
                    <template v-if="tito.time_out == null">
                        <v-card-text>
                            <div class="caption">Server time</div>
                            <v-divider class="mt-1 mb-2"></v-divider>
                            <div class="title">
                                {{moment(sysDateTime).format('LLLL')}}
                            </div>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn round class="primary" @click="timeOut">Time out now</v-btn>
                        </v-card-actions>
                    </template>
                    <template v-else>
                        <v-card-text>
                            <div class="caption">Time out</div>
                            <v-divider class="mt-1 mb-2"></v-divider>
                            <div class="title">
                                {{moment(tito.time_out).format('LLLL')}}
                            </div>
                        </v-card-text>
                    </template>
                </v-card>
            </div>
        </template>
    </v-container>
</template>
<script>
export default {
    data() {
        return {
            sysDateTime: null,
            tito: null,
            cancelSource: null,
            keyword: null,
            sortBy: 'Time in',
            orderBy: 'desc',
            date: null,
            page: 1,
            reset: false,
            total: 0,
            items: [],
            loading: false,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Staff name',
                    sortable: false
                },
                {
                    text: 'Time in',
                    sortable: false
                },
                {
                    text: 'Time out',
                    sortable: false
                }
            ]
        }
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            console.log('admin', user);
            if(user) {
                return user.roles.some(r => r == 'admin');
            }
        }
    },
    methods: {
        getSystemDateTime() {
            axios.get('/api/developer/system-date-time').then((res, rej) => {
                this.sysDateTime = new Date(res.data);
            });
        },
        getTimeIn() {
            axios.get('/api/time-keeping/time-in').then((res, rej) => {
                this.tito = res.data.tito;
            });
        },
        timeOut() {
            if(confirm("Time out now?\nYou will not be able to Time out again")) {
                axios.post('/api/time-keeping/time-out').then((res, rej) => {
                    this.$store.dispatch('auth/logout').finally(() => {
                        this.$router.push('/login');
                    });
                });
            }
        },
        filter() {
            this.page = 1;
            this.reset = true;
            this.load();
        },
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();
            this.loading = true;
            axios.get('/api/time-keeping', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    date: this.date,
                    sortBy: this.sortBy,
                    orderBy: this.orderBy
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                if(this.reset) {
                    this.reset = false;
                    this.items = res.data.result.data;
                } else {
                    this.items = [...this.items, ...res.data.result.data];
                    setTimeout(() => {
                        window.scrollTo({
                            top: document.body.scrollHeight,
                            behavior: 'smooth'
                        });
                    }, 10);
                }
                this.total = res.data.result.total;
            }).finally(() => {
                this.loading = false;
            });
        },
        cancelSearch() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        },
        loadMore() {
            this.page += 1;
            this.load();
        }
    },
    created() {
        this.getSystemDateTime();
        this.getTimeIn();
        if(this.isOwner) {
            this.load();
        }
    }
}
</script>
