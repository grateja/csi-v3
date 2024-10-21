<template>
    <v-dialog :value="value" max-width="480" persistent>
        <v-card class="rounded-card">
            <v-card-text v-if="jobOrder">
                <span>JOB ORDER #:</span>
                <span>{{ jobOrder.job_order_number }}</span>
                <br>
                <span>Date:</span>
                <span>{{ moment(jobOrder.created_at).format('MMM DD, YYYY - hh:MM a') }}</span>
                <br>
                <template v-if="jobOrder.user">
                    <span>Created by:</span>
                    <span>{{ jobOrder.user.name }}</span>
                </template>
            </v-card-text>
            <v-card-text v-if="jobOrder">
                <v-layout>
                    <v-flex xs4>
                        <div class="pa-1 caption grey--text font-weight-bold">Name</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Unit Price</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-center">QTY</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Total Price</div>
                    </v-flex>
                </v-layout>
                <v-divider></v-divider>
                <template v-for="(item, i) in jobOrder.out_source_job_order_linens">
                    <template>
                        <v-divider :key="i + '-div-services'"></v-divider>
                        <v-layout class="pa-1 transaction-item" :key="i + 'services'">
                            <v-flex xs4>
                                <div>{{item.name}} ({{ item.degree_of_soil }}) </div>
                            </v-flex>
                            <v-flex xs3>
                                <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</div>
                            </v-flex>
                            <v-flex xs2>
                                <div class="text-xs-center">{{item.quantity}}</div>
                            </v-flex>
                            <v-flex xs3>
                                <div class="text-xs-right">{{'P ' + parseFloat(item.unit_price * item.quantity).toFixed(2)}}</div>
                            </v-flex>
                        </v-layout>
                    </template>
                </template>
                <v-divider class="black"></v-divider>
                <template v-if="total != null">
                    <v-layout class="font-weight-bold">
                        <v-flex xs6>TOTAL</v-flex>
                        <v-flex xs6>P {{ parseFloat(total).toFixed(2) }}</v-flex>
                    </v-layout>
                </template>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="$emit('input', false)">close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'jobOrderId',
    ],
    data() {
        return {
            openQuantityDialog: false,
            loading: null,
            jobOrder: null,
        }
    },
    methods: {
        async getJobOrder(jobOrderId) {
            this.loading = 'get-job-order'
            axios.get(`/api/out-source/job-orders/${this.outSourceId}/${jobOrderId}/show`).then(res => {
                this.jobOrder = res.data.jobOrder
                this.jobOrderNumber = res.data.jobOrderNumber
            }).finally(err => {
                this.loading = null
            })
        },
    },
    computed: {
        total() {
            if(this.jobOrder) {
                return this.jobOrder.out_source_job_order_linens.reduce((a, b) => a + b.unit_price * b.quantity, 0)
            }
            return null
        }
    },
    watch: {
        jobOrderId: {
            handler(val) {
                this.getJobOrder(val);
            },
            immediate: true
        }
    }
}
</script>
