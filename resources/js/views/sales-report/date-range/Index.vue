<template>
    <v-card class="transparent" flat>
        <v-btn to="/sales-report/calendar-view" class="ma-1 translucent" round>
            <v-icon left>
                chevron_left
            </v-icon>
            Calendar view
        </v-btn>
        <v-btn to="/sales-report/yearly-view" class="ma-1 translucent" round>
            <v-icon left>
                chevron_left
            </v-icon>
            Yearly view
        </v-btn>
        <h3 class="title white--text my-4">Custom date range</h3>
        <v-card class="transparent" flat>
            <v-card-text>
                <v-layout>
                    <v-flex>
                        <v-text-field class="ml-1 translucent-input round-input" label="Date from" v-model="dateFrom" type="date" outline></v-text-field>
                    </v-flex>
                    <v-flex>
                        <v-text-field class="ml-1 translucent-input round-input" label="Date to" v-model="dateTo" type="date" outline></v-text-field>
                    </v-flex>
                    <v-flex>
                        <v-btn round @click="open">Search</v-btn>
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card>
        <custom-range-dialog v-model="openDialog" :dateFrom="dateFrom" :dateTo="dateTo"></custom-range-dialog>
    </v-card>
</template>
<script>
import CustomRangeDialog from './CustomRangeDialog.vue';

export default {
    components: {
        CustomRangeDialog
    },
    data() {
        return {
            dateFrom: moment().format('YYYY-MM-DD'),
            dateTo:  moment().format('YYYY-MM-DD'),
            openDialog: false
        }
    },
    methods: {
        open() {
            if(this.dateFrom == null || this.dateTo == null) {
                alert('Please specify date')
                return;
            }
            this.openDialog = true;
        }
    }
}
</script>
