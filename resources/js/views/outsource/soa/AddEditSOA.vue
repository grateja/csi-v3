<template>
    <div>
        <v-card>
            <form @submit.prevent="save">
                <v-card-actions>{{ action }}</v-card-actions>
                <v-card-text>
                    <v-text-field dense outline label="SOA#" v-model="formData.soa_number" :error-messages="errors.get('soa_number')"></v-text-field>
                    <v-layout>
                        <v-flex xs-6>
                            <v-text-field type="date" dense outline label="Start date" v-model="formData.start_date" :error-messages="errors.get('start_date')"></v-text-field>
                        </v-flex>
                        <v-flex xs-6>
                            <v-text-field type="date" dense outline label="End date" v-model="formData.end_date" :error-messages="errors.get('end_date')"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-text-field dense outline label="Remarks" v-model="formData.remarks" :error-messages="errors.get('remarks')"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit">Save</v-btn>
                </v-card-actions>
            </form>
        </v-card>
        <v-card class="rounded-card translucent-table">
            <v-data-table v-if="soa" :headers="headers" :items="soa.out_source_job_orders" hide-actions class="transparent">
                <template v-slot:items="props">
                    <tr>
                        <td>{{props.index + 1}}</td>
                        <td>{{ moment(props.item.created_at).format('LL') }}</td>
                        <td>
                            <v-btn  @click="previewJobOrder(props.item)" round>
                                {{ props.item.job_order_number }}
                            </v-btn>
                        </td>
                        <td v-if="props.item.total_amount == null">(No items)</td>
                        <td v-else>P {{ parseFloat(props.item.total_amount).toFixed(2) }}</td>
                    </tr>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>

<script>
export default {
    data() {
        return {
            action: 'insert',
            formData: {
                soa_number: null,
                start_date: null,
                end_date: null,
                remarks: null
            },
            soa: null,
            headers: [
                {
                    text: '',
                    sortable: false
                },
                {
                    text: 'Date',
                    sortable: false
                },
                {
                    text: 'Job order number',
                    sortable: false
                },
                {
                    text: 'Amount',
                    sortable: false
                }
            ]
        }
    },
    methods: {
        getSOA(soaId, outSourceId) {
            axios.get(`/api/out-source/soa/prepare-or-edit/${soaId}/${outSourceId}`).then(res => {
                console.log(res.data)
                this.actions = res.data.action;
                this.soa = res.data.soa
                this.formData.soa_number = res.data.soa.soa_number;
                this.formData.start_date = res.data.start_date
                this.formData.end_date = res.data.end_date
                this.formData.remarks = res.data.soa.remarks
            }).catch(err => {
                this.formData.soa_number = null
            })
        },
        save() {
            if(this.soa.out_source_job_orders.length == 0) {
                alert("No jobrders")
                return;
            }
            this.formData.out_source_id = this.route.outSourceId
            this.$store.dispatch(`outsourcesoa/${this.action}Soa`, {
                soaId: this.soa ? this.soa.id : null,
                formData: this.formData
            }).then((res, rej) => {
                // this.close();
                this.$emit('save', {
                    mode: this.mode,
                    soa: res.data.soa
                });
                this.close();
            });
        }
    },
    computed: {
        route() {
            return this.$route.params
        },
        errors() {
            return this.$store.getters['outsourcesoa/getErrors'];
        }
    },
    watch: {
        route: {
            handler(newVal) {
                if(newVal) {
                    this.getSOA(newVal.soaId, newVal.outSourceId)
                }
            },
            immediate: true
        }
    }
}
</script>