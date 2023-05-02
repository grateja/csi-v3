<template>
    <v-card v-if="Object.keys(washes).length || Object.keys(dries).length" class="translucent rounded-card mt-2">
        <v-card-title>
            <div>
                <h4 class="font-weight-bold title">{{title}}</h4>
                <h4 class="grey--text font-italic">(Including Job Orders created from previous days)</h4>
            </div>
            <v-divider></v-divider>
            <v-btn round small @click="simplified = !simplified">{{simplified ? 'EXPAND' : "SIMPLIFIED"}}</v-btn>
        </v-card-title>
        <v-card-text>
            <template v-if="Object.keys(washes).length > 0">
                <h3 class="font-weight-bold mt-3">Washes</h3>
                <processed-wash-dry :items="washes" @openJobOrder="id => $emit('openJobOrder', id)" :simplified="simplified" />
            </template>
            <template v-if="Object.keys(dries).length > 0">
                <h3 class="font-weight-bold mt-3">Dries</h3>
                <processed-wash-dry :items="dries" @openJobOrder="id => $emit('openJobOrder', id)" :simplified="simplified" />
            </template>
        </v-card-text>
    </v-card>
</template>
<script>
import ProcessedWashDry from './ProcessedWashDry.vue';

export default {
    components: {
        ProcessedWashDry
    },
    props:['washes', 'dries', 'title'],
    data() {
        return {
            simplified: true
        }
    }
}
</script>
