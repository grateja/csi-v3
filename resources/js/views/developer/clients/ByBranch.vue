<template>
    <v-card>
        <v-card-text>
            <form @submit.prevent="filter">
                <v-text-field v-model="keyword" label="Search" append-icon="search"></v-text-field>
            </form>
            <v-data-table :headers="headers" :items="clients" hide-actions :loading="searching">
                <template v-slot:items="props">
                    <td>{{props.item.client.user.fullname}}</td>
                    <td>{{props.item.name}}</td>
                    <td>{{props.item.address}} ({{props.item.area}})</td>
                    <td>{{props.item.email}}</td>
                    <td>{{props.item.contact_no}}</td>
                    <td>{{props.item.machines_count}}
                        <v-btn small icon :to="`/developer/clients/${props.item.id}/view-machines`">
                            <v-icon small>list</v-icon>
                        </v-btn>
                    </td>
                    <td>
                        <v-btn small icon @click="edit(props.item)">
                            <v-icon small>edit</v-icon>
                        </v-btn>
                    </td>
                </template>
            </v-data-table>
        </v-card-text>

        <v-pagination v-if="totalPage > 1" v-model="page" :length="totalPage" @input="navigate" class="my-3"></v-pagination>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            clients: [],
            page: this.$route.query.page || 1,
            keyword: this.$route.query.keyword,
            clientId: this.$route.query.clientId,
            totalPage: 0,
            searching: false,
            headers: [
                {
                    text: 'Client owner',
                    sortable: false,
                },
                {
                    text: 'Shop name',
                    sortable: false
                },
                {
                    text: 'Address',
                    sortable: false
                },
                {
                    text: 'Email',
                    sortable: false
                },
                {
                    text: 'Contact number',
                    sortable: false
                },
                {
                    text: 'Number of machines',
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

            if(this.searching) return;

            this.$router.push({
                path: '/developer/clients/by-branches',
                query:{
                    keyword: this.keyword,
                    page: this.page,
                    clientId: this.clientId
                }
            });

            this.searching = true;
            axios.get('/api/search/clients/by-branches', {
                params: {
                    keyword: this.keyword,
                    page: this.page,
                    clientId: this.clientId
                }
            }).then((res, rej) => {
                console.log(res.data);
                this.searching = false;
                this.clients = res.data.result.data;
                this.totalPage = res.data.result.last_page;
            }).catch(err => {
                this.searching = false;
            });
        },
        navigate(page) {
            this.page = page;
            this.load();
        },
        edit(item) {
            this.$router.push(`/developer/clients/${item.id}`);
        }
    },
    created() {
        this.load();
    }
}
</script>
