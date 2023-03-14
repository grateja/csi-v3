<template>
    <v-dialog :value="value" max-width="400" persistent>
        <v-card>
            <v-card-title>
                <span class="title grey--text">Select option</span>
                <v-spacer></v-spacer>
                <v-btn icon @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-actions>
                <v-spacer />
                <v-btn round @click="$emit('continue', 'itemized')" :loading="loading == 'itemized'">Itemized</v-btn>
                <v-btn round @click="$emit('continue', 'simplified')" :loading="loading == 'simplified'">Simplified</v-btn>
                <v-spacer />
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: ['value', 'loading'],
    methods: {
        close() {
            this.$emit('input', false)
        }
    }
}
</script>