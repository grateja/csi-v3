<template>
    <v-dialog :value="value" persistent max-width="480px">
        <v-card>
            <v-card-title>
                <v-btn flat large @click="openYearSelector = !openYearSelector" outline round>
                    {{year}}
                    <v-icon right>arrow_drop_up</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn icon small @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-divider></v-divider>
            <v-layout v-if="openYearSelector">
                <v-flex>
                    <v-list>
                        <v-list-tile @click="prev">
                            <v-spacer></v-spacer>
                            <v-icon>expand_less</v-icon>
                            <v-spacer></v-spacer>
                        </v-list-tile>
                        <v-divider></v-divider>
                        <v-list-tile v-for="i in 10" :key="i" @click="selectYear(i + parseInt(year) - offset)">
                            <v-spacer></v-spacer>
                            <div class="text-xs-center">
                                {{i + parseInt(year) - offset}}
                            </div>
                            <v-spacer></v-spacer>
                        </v-list-tile>
                        <v-divider></v-divider>
                        <v-list-tile @click="next">
                            <v-spacer></v-spacer>
                            <v-icon>expand_more</v-icon>
                            <v-spacer></v-spacer>
                        </v-list-tile>
                    </v-list>
                </v-flex>
            </v-layout>
            <v-layout row wrap v-else>
                <v-flex v-for="(month, i) in months" :key="i" xs4>
                    <v-card @click="selectMonth(i)" class="pointer">
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
        'value', 'monthIndex', 'year'
    ],
    data() {
        return {
            openYearSelector: false,
            offset: 5,
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
            this.openYearSelector = false;
        },
        selectMonth(index) {
            this.$emit('select', index);
            this.close();
        },
        selectYear(year) {
            this.$emit('selectYear', year);
            this.openYearSelector = false;
        },
        prev() {
            this.offset += 5;
        },
        next() {
            this.offset -= 5;
        }
    }
}
</script>
