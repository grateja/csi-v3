<template>
    <v-dialog :value="value" max-width="720px" persistent>
        <v-card>
            <v-card-title class="title grey--text">Summary for {{moment(date).format('MMMM DD, YYYY')}}</v-card-title>
            <v-progress-linear class="my-0" height="1" v-if="loading" indeterminate />
            <v-divider v-else></v-divider>
            <v-card-text>
                <pre>{{result}}</pre>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="close">close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'date'
    ],
    data() {
        return {
            loading: false,
            result: null
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        load() {
            this.loading = true;
            axios.get(`/api/sales-report/${this.date}/summary`).then((res, reh) => {
                this.result = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        }
    },
    watch:{
        value(val) {
            if(val && this.date) {
                this.load();
            } else {
                this.result = null;
            }
        }
    }
}
</script>
