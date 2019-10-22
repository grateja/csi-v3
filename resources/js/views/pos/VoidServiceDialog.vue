<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <v-card>
            <v-card-title class="title grey--text">Void service item</v-card-title>
            <v-card-text v-if="!!completedServiceItems && completedServiceItems.length">
                <v-list multiple v-model="selectedItems">
                    <v-list-tile v-for="item in completedServiceItems" :key="item.id">
                        <v-list-tile-action>

                        </v-list-tile-action>
                        <v-list-tile-content>
                            {{item.name}}
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
                <pre>{{completedServiceItems}}</pre>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="ok" class="primary">ok</v-btn>
                <v-btn @click="cancel">Cancel</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    props: [
        'value',
        'serviceTransactionId'
    ],
    data() {
        return {
            completedServiceItems: [],
            selectedItems: []
        }
    },
    methods: {
        ok() {
        },
        cancel() {
            this.$emit('input', false);
        },
        get() {
            axios.get(`/api/void-transaction-item/${this.serviceTransactionId}`).then((res, rej) => {
                console.log(res.data);
                this.completedServiceItems = res.data.completedServiceItems;
            });
            console.log('get')
        }
    },
    watch: {
        value(val) {
            if(val && this.serviceTransactionId) {
                this.get();
            }
        }
    }
}
</script>
