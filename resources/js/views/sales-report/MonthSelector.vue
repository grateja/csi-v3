<template>
    <v-dialog :value="value" persistent max-width="480px">
        <v-card>
            <v-card-title>
                2020
                <v-spacer></v-spacer>
                <v-btn icon small @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-layout row wrap>
                <v-flex v-for="(month, i) in months" :key="i" xs4>
                    <v-card @click="selectMonth(i)">
                        <v-card-title>
                            {{month}}
                        </v-card-title>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    props: [
        'value', 'monthIndex'
    ],
    data() {
        return {
            months: [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December',
            ]
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        selectMonth(index) {
            this.$emit('select', index);
            this.close();
        }
    }
}
</script>
