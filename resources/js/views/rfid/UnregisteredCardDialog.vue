<template>
    <v-dialog :value="value" max-width="400" persistent>
        <v-card>
            <v-card-title class="title grey--text">Unregistered card</v-card-title>
            <v-progress-linear class="my-0" v-if="loading" indeterminate height="1"></v-progress-linear>
            <v-divider v-else></v-divider>
            <v-list>
                <v-list-tile v-for="item in items" :key="item.id" @click="select(item)">
                    <v-list-tile-content>
                        <v-list-tile-title>{{item.rfid}}</v-list-tile-title>
                        <div class="caption grey--text">Tapped from {{item.machine_name}}, {{moment(item.updated_at).fromNow()}}</div>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn close @click="close" round>close</v-btn>
                <v-spacer></v-spacer>
                <v-btn @click="clearAll" :loading="clearing" round>clear all</v-btn>
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
            clearing: false,
            items: []
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        select(rfid) {
            this.$emit('select', rfid);
            this.close();
        },
        load() {
            this.loading = true;
            axios.get('/api/rfid-cards/unregistered-cards').then((res, rej) => {
                this.items = res.data.result;
            }).finally(() => {
                this.loading = false;
            });
        },
        clearAll() {
            this.clearing = true;
            this.$store.dispatch('rfidcard/clearUnregisteredCard').then((res, rej) => {
                this.items = [];
                this.close();
            }).finally(() => {
                this.clearing = false;
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
