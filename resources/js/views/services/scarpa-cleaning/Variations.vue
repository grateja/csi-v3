<template>
    <v-card>
        <v-data-table :headers="headers" :items="variations" :loading="loading" hide-actions class="transparent">
            <template v-slot:items="props">
                <tr>
                    <td>{{props.item.action}}</td>
                    <td v-if="!props.item.selling_price">
                        FREE
                    </td>
                    <td v-else>P {{ parseFloat(props.item.selling_price).toFixed(2) }}</td>
                    <td>
                        <template v-if="isOwner">
                            <v-btn small @click="edit(props.item)" class="mx-0" round outline>
                                <v-icon left small>edit</v-icon> edit
                            </v-btn>
                            <v-btn small @click="deleteVariation(props.item)" class="mx-0" outline :loading="props.item.isDeleting" round>
                                <v-icon left small>delete</v-icon> delete
                            </v-btn>
                        </template>
                    </td>
                </tr>
            </template>
        </v-data-table>
    </v-card>
</template>

<script>
export default {
    props: [
        'serviceId', 'variations'
    ],
    data() {
        return {
            loading: false,
            headers: [
                {
                    text: 'Action',
                    sortable: false
                },
                {
                    text: 'Price',
                    sortable: false
                },
                {
                    text: '',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        edit(variation) {
            this.$emit('edit', variation)
        },
        deleteVariation(variation) {
            this.$emit('deleteVariation', variation);
        }
    },
    computed: {
        isOwner() {
            let user = this.$store.getters.getCurrentUser;
            return (!!user && user.roles[0] == 'admin');
        }
    }
}
</script>
