<template>
    <v-dialog :value="value" persistent max-width="720">
        <v-card>
            <v-card-title>
                <span class="title grey--text">Registered RFID Card</span>
                <v-spacer></v-spacer>
                <v-btn icon @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-data-table :headers="headers" :items="items" :loading="loading" hide-actions>
                    <template v-slot:items="props">
                        <td>{{ props.index + 1 }}</td>
                        <td>{{ moment(props.item.enrolled).format('LLL') }}</td>
                        <td>{{ props.item.rfid }}</td>
                        <td>{{ props.item.card_type == 'u' ? 'Unlimited' : 'P ' + parseFloat(props.item.balance).toFixed(2) }}</td>
                    </template>
                </v-data-table>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'customer'
    ],
    data() {
        return {
            items: [],
            loading: false,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Date enrolled',
                    sortable: false
                },
                {
                    text: 'RFID',
                    sortable: false
                },
                {
                    text: 'Balance',
                    sortable: false
                }
            ]

        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        load() {
            console.log(this.customer)
            axios.get('/api/rfid-cards', {
                params: {
                    keyword: this.customer.name
                }
            }).then((res, rej) => {
                this.items = res.data.result.data;
            }).finally(() => {
                this.loading = false;
            });
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.load();
            }
        }
    }
}
</script>
