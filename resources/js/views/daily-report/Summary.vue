<template>
    <v-card class="translucent rounded-card">
        <v-card-title class="title font-weight-bold">SUMMARY</v-card-title>
        <v-card-text>
            <v-layout>
                <v-flex>
                    <v-card class="pa-3 ma-1">
                        {{ summary.customerCount }} New Customer(s)
                    </v-card>
                </v-flex>
                <v-flex>
                    <v-card class="pa-3 ma-1">
                        {{ summary.jobOrdersToday.created }} New JobOrder(s)
                        <div>
                            <v-layout>
                                <v-flex>Unpaid</v-flex>
                                <v-flex class="text-xs-right">{{ summary.jobOrdersToday.created - summary.jobOrdersToday.paid.count }}</v-flex>
                            </v-layout>
                            <v-layout>
                                <v-flex>Partial Payment Only</v-flex>
                                <v-flex class="text-xs-right">{{ summary.jobOrdersToday.created - summary.jobOrdersToday.paid.count }}</v-flex>
                            </v-layout>
                        </div>
                    </v-card>
                </v-flex>
            </v-layout>
            <ul>
                <li>
                    {{ summary.customerCount }} New Customer(s)
                </li>
                <li>
                    <ul>
                        <li>
                            {{ summary.jobOrdersToday.paid.count }} Paid
                            <ul>
                                <li>

                                </li>
                            </ul>
                        </li>
                        <li>
                            {{ summary.jobOrdersToday.created - summary.jobOrdersToday.paid.count }} Unpaid
                        </li>
                    </ul>
                </li>
            </ul>
            <div>
            </div>
        </v-card-text>
        <pre>{{ summary }}</pre>
    </v-card>
</template>

<script>
export default {
    props: ['summary']
}
</script>
