<template>
    <v-container>
        <h3 class="title white--text">Machine remarks</h3>
        <v-divider class="my-3"></v-divider>
        <v-btn round class="ml-0 translucent" to="/machines"><v-icon>chevron_left</v-icon> Machines usages</v-btn>

        <form @submit.prevent="filter">
            <v-text-field outline v-model="keyword" label="Search" append-icon="search" @input="filter" class="round-input translucent-input"></v-text-field>
        </form>

        <v-card class="rounded-card translucent-table">
            <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions class="transparent">
                <template v-slot:items="props">
                    <td>{{props.index + 1}}</td>
                    <td>{{props.item.title}}</td>
                    <td>{{props.item.remarks}}</td>
                    <td>{{props.item.user.name}}</td>
                    <td>{{props.item.machine.machine_name}}</td>
                    <td>{{props.item.remaining_time}}</td>
                    <td>{{moment(props.item.created_at).format('lll')}}</td>
                </template>
                <template slot="footer">
                    <tr v-if="!!summary">
                        <td colspan="10">
                            <div class="font-italic grey--text">Showing <span class="font-weight-bold">{{items.length}}</span> item(s) out of <span class="font-weight-bold">{{summary.total_items}}</span> result(s)</div>
                        </td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
        <v-btn block @click="loadMore" :loading="loading" round class="translucent">Load more</v-btn>

    </v-container>
</template>
<script>
import moment from 'moment';

export default {
    data() {
        return {
            reset: true,
            cancelSource: null,
            keyword: this.$route.query.keyword,
            page: parseInt(this.$route.query.page) || 1,
            loading: false,
            totalPage: 0,
            items: [],
            summary: null,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Title',
                    sortable: false
                },
                {
                    text: 'Remarks',
                    sortable: false
                },
                {
                    text: 'Staff',
                    sortable: false
                },
                {
                    text: 'Machine name',
                    sortable: false
                },
                {
                    text: 'Remaining time',
                    sortable: false
                },
                {
                    text: 'Date',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        filter() {
            this.page = 1;
            this.reset = true;
            this.load();
        },
        load() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();

            this.loading = true;
            axios.get('/api/machines/remarks', {
                params: {
                    keyword: this.keyword,
                    page: this.page
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                if(this.reset) {
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
                this.summary = res.data.summary;
                this.totalPage = res.data.result.last_page;
                this.loading = false;
            }).catch(err => {
                this.loading = false;
            });
        },
        date(date) {
            let _date = moment(date);
            return _date.isValid() ? _date.format('MMM D, YY') : date;
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
        }
    },
    created() {
        this.load();
    }
}
</script>
