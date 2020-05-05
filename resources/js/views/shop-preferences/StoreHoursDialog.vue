<template>
    <v-dialog :value="value" persistent max-width="480px">
        <v-card>
            <v-card-title>
                <span class="title grey--text">Store hours</span>
            </v-card-title>
            <v-divider></v-divider>
            <v-card-text>
                <v-list>
                    <template v-for="(item, i) in result">
                        <v-list-tile :key="item.id" @click="edit(item)">
                            <v-list-tile-content>
                                <v-list-tile-title>
                                    {{item.day}}
                                </v-list-tile-title>
                                <div class="caption grey--text">
                                    {{item.display}}
                                </div>
                            </v-list-tile-content>
                        </v-list-tile>
                        <v-divider :key="i"></v-divider>
                    </template>
                </v-list>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn @click="close" round>close</v-btn>
            </v-card-actions>
        </v-card>
        <time-dialog v-model="openAddEdit" :storeHour="activeItem" @save="updateList" />
    </v-dialog>
</template>

<script>
import TimeDialog from './TimeDialog.vue';

export default {
    components: {
        TimeDialog
    },
    props: [
        'value'
    ],
    data() {
        return {
            result: [],
            activeItem: null,
            openAddEdit: false
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        get() {
            axios.get(`/api/admin/store-hours`).then((res, rej) => {
                this.result = res.data.result;
            });
        },
        edit(item) {
            this.activeItem = item;
            this.openAddEdit = true;
        },
        updateList(data) {
            if(data.mode == 'insert') {
                this.activeItem = data.storeHour;
                this.result.push(data.storeHour);
            } else {
                this.activeItem.opens_at = data.storeHour.opens_at;
                this.activeItem.closes_at = data.storeHour.closes_at;
                this.activeItem.display = data.storeHour.display;
            }
        }
    },
    watch: {
        value(val) {
            if(val) this.get();
        }
    }
}
</script>
