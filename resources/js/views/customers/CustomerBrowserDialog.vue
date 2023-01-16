<template>
    <v-dialog :value="value" max-width="480" persistent>
        <v-card class="rounded-card">
            <v-card-title>
                <span class="title">Pre-registered customers</span>
                <v-spacer></v-spacer>
                <v-btn icon small @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-text-field label="Search" v-model="keyword" outline ref="rfid" @keyup="filter"></v-text-field>
                <v-list dense>
                    <v-list-tile v-for="item in items" :key="item.id" @click="selectCustomer(item)">
                        <v-list-tile-content>
                            <v-list-tile-title>
                                <h3>{{item.name}}</h3>
                            </v-list-tile-title>
                            <span class="grey--text">{{item.address}}</span>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="close" round>Close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value'
    ],
    data() {
        return {
            loading: false,
            cancelSource: null,
            keyword: null,
            items: []
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        filter() {
            this.cancelSearch();
            this.cancelSource = axios.CancelToken.source();
            if(!this.keyword) {
                this.items = [];
                return;
            }

            this.loading = true;
            axios.get(`/api/customers/pre-registered`, {
                params: {
                    keyword: this.keyword
                },
                cancelToken: this.cancelSource.token
            }).then((res, rej) => {
                this.items = res.data.data;
            }).finally(() => {
                this.loading = false;
            });
        },
        selectCustomer(customer) {
            this.$emit('selectCustomer', customer);
        },
        cancelSearch(){
            if(this.cancelSource){
                this.cancelSource.cancel();
            }
        }
    },
    computed: {
        shopId() {
            return this.$store.getters.getShopId;
        }
    }
}
</script>