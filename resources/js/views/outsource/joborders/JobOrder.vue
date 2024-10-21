<template>
    <v-container>
        <v-layout>
            <v-flex xs7 lg8>
                <v-layout row wrap>
                    <v-flex  xs6 sm4 lg3 xl2 v-for="linen in availableLinens" :key="linen.id">
                        <v-hover v-slot:default="{ hover }">
                            <v-card class="ma-1 rounded-card pointer translucent" :elevation="hover ? 12 : 2" @click="addLinen(linen)">
                                <v-card-text>
                                    <div class="text-xs-center ma-1">
                                        <div>
                                            {{linen.name}}
                                        </div>
                                        <div class="font-italic grey--text">{{linen.dry_weight}}kg</div>
                                    </div>
                                    <v-progress-linear v-if="linen.isAdding" indeterminate height="4" class="my-0"></v-progress-linear>
                                    <v-divider v-else class="my-2"></v-divider>
                                    <div class="text-xs-center title ma-2">
                                        P {{ parseFloat(linen.regular_price).toFixed(2)}}
                                    </div>
                                    <v-divider></v-divider>
                                    <div>
                                        <v-chip color="info">P {{ parseFloat(linen.with_stain_light).toFixed(2)}}</v-chip>
                                        <v-chip color="warning">P {{ parseFloat(linen.with_stain_medium).toFixed(2)}}</v-chip>
                                        <v-chip color="red">P {{ parseFloat(linen.with_stain_heavy).toFixed(2)}}</v-chip>
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-hover>
                    </v-flex>
                </v-layout>
            </v-flex>
            <v-flex xs5 lg4>
                <v-card class="rounded-card">
                    <v-card-text v-if="tempJobOrder">
                        <span>JOB ORDER #:</span>
                        <span>{{ tempJobOrder.job_order_number }}</span>
                        <br>
                        <span>Date:</span>
                        <span>{{ moment(tempJobOrder.created_at).format('MMM DD, YYYY - hh:MM a') }}</span>
                        <br>
                        <template v-if="tempJobOrder.user">
                            <span>Created by:</span>
                            <span>{{ tempJobOrder.user.name }}</span>
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
                                <v-layout class="pa-1 pointer transaction-item" :key="i + 'services'" @click="removeLinen(item)">
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
                </v-card>
                <v-btn @click="save" round v-if="jobOrder == null && loading == null">create</v-btn>
            </v-flex>
        </v-layout>
        <QuantityDialog
            v-if="jobOrder"
            v-model="openQuantityDialog"
            :jobOrderId="jobOrder.id"
            :linen="activeLinen"
            @save="updateItems"
        />
        <!-- <pre>{{ jobOrderId }}</pre>
        <pre>{{ jobOrder }}</pre>
        <pre>{{ jobOrderNumber }}</pre> -->
        <!-- <pre>{{ jobOrder }}</pre> -->
    </v-container>
</template>

<script>
import QuantityDialog from './QuantityDialog.vue'

export default {
    components: {
        QuantityDialog
    },
    data() {
        return {
            openQuantityDialog: false,
            loading: null,
            jobOrder: null,
            activeLinen: null,
            // activeJobOrderLinen: null,
            jobOrderNumber: null,
            availableLinens: []
        }
    },
    methods: {
        getJobOrder(jobOrderId) {
            this.loading = 'get-job-order'
            axios.get(`/api/out-source/job-orders/${this.outSourceId}/${jobOrderId}/show`).then(res => {
                this.jobOrder = res.data.jobOrder
                this.jobOrderNumber = res.data.jobOrderNumber
            }).finally(err => {
                this.loading = null
            })
        },
        getAvailableLinens(outSourceId){
            axios.get(`/api/out-source/linens/${outSourceId}`).then(res => {
                this.availableLinens = res.data.result;
            });
        },
        save() {
            this.loading = 'save-job-order'
            axios.post(`/api/out-source/job-orders/${this.outSourceId}/create`).then(res => {
                this.$router.replace(`/out-source/job-orders/${this.$route.params.outSourceId}/${res.data.jobOrder.id}`);
            }).finally(err => {
                this.loading = null
            })
        },
        addLinen(item) {
            if(this.jobOrder == null) {
                this.save();
            }
            this.activeLinen = item;
            this.openQuantityDialog = true;
        },
        removeLinen(item) {
            if(confirm("Remove this item?")) {
                this.$store.dispatch('outsourcejoborder/deleteLinen', item.id).then(res => {
                    this.jobOrder.out_source_job_order_linens = this.jobOrder.out_source_job_order_linens.filter(i => i.id != item.id)
                })
            }
        },
        updateItems(data) {
            let linen = this.jobOrder.out_source_job_order_linens.find(i => i.id == data.linen.id)
            if(linen == null) {
                this.jobOrder.out_source_job_order_linens.push(data.linen)
            } else {
                linen.quantity = data.linen.quantity
            }
        }
    },
    computed: {
        jobOrderId() {
            return this.$route.params.jobOrderId
        },
        outSourceId() {
            return this.$route.params.outSourceId
        },
        tempJobOrder() {
            return this.jobOrder ? this.jobOrder : {
                job_order_number: this.jobOrderNumber,
                created_at: new Date()
            }
        },
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
        },
        outSourceId: {
            handler(val) {
                this.getAvailableLinens(val)
            },
            immediate: true
        }
    }
}
</script>
