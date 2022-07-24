<template>
    <v-dialog :value="value" max-width="400" persistent>
        <v-card class="rounded-card">
            <v-card-title>
                <span class="title">Select discount</span>
            </v-card-title>
            <v-progress-linear v-if="loading" indeterminate height="1"></v-progress-linear>
            <v-card-text v-else-if="!loading && discounts.length == 0">No discounts available</v-card-text>
            <v-divider v-else></v-divider>
            <v-card-text>
                <v-list>
                    <v-list-tile v-for="item in discounts" :key="item.id" @click="select(item)">
                        <v-list-tile-content>
                            <v-list-tile-title class="title">{{item.name}}</v-list-tile-title>
                            <v-list-tile-sub-title>{{item.percentage}} % Discount</v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </v-card-text>

            <v-card-actions>
                <v-btn @click="close" round>close</v-btn>
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
            discounts: [],
            loading: false
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        select(item) {
            this.$emit('setDiscount', item);
            this.close();
        }
    },
    created() {
        this.loading = true;
        axios.get('/api/discounts').then((res, rej) => {
            this.discounts = res.data.result;
            this.loading = false;
        }).catch(e => {
            this.loading = false;
        });
    }
}
</script>
