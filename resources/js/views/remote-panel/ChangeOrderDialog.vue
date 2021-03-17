<template>
    <v-dialog :value="value" persistent max-width="480">
        <v-card>
            <v-card-title>
                <span class="title grey--text">Change order</span>
                <v-spacer></v-spacer>
                <v-btn small icon @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    props: ['value', 'machine'],
    methods: {
        close() {
            this.$emit('input', false);
        }
    }
}
</script>
