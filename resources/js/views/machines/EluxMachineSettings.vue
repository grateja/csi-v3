<template>
    <v-dialog :value="value" max-width="500" persistent>
        <form @submit.prevent="submit">
            <v-card class="rounded-card">
                <v-card-title class="title grey--text">Machine configuration</v-card-title>
                <v-card-text>
                    <v-text-field outline label="Machine name" v-model="formData.machineName" :error-messages="errors.get('machineName')"></v-text-field>
                    <template v-if="isDeveloper">
                        <v-btn v-if="action == 'update'" round class="translucent" @click="deleteMachine" :loading="isDeleting">
                            <v-icon left>delete</v-icon> Delete
                        </v-btn>
                        <v-combobox :items="['washer', 'dryer']" label="Service type" :error-messages="errors.get('machineType')" v-model="formData.machineType" outline></v-combobox>
                        <v-combobox :items="availableModels" label="Model" :error-messages="errors.get('model')" v-model="formData.model" outline></v-combobox>
                        <v-text-field outline label="IP address" v-model="formData.ipAddress" :error-messages="errors.get('ipAddress')"></v-text-field>
                    </template>
                    <v-text-field outline label="Stack Order" v-model="formData.stackOrder" :error-messages="errors.get('stackOrder')" :disabled="retreivingStackOrder" :loading="retreivingStackOrder"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn round type="submit" class="primary" :loading="saving">save</v-btn>
                    <v-btn round @click="close">close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'machine', 'value', 'machineType'
    ],
    data() {
        return {
            formData: {
                model: null,
                machineType: null,
                machineName: null,
                ipAddress: null,
                stackOrder: 0,
                applyToAll: false
            },
            action: null,
            isDeleting: false,
            retreivingStackOrder: false,
            models: [
                'WH6-6 - 6kg',
                'WH6-7 - 7kg',
                'WH6-8 - 8kg',
                'WH6-11 - 10.5kg',
                'WH6-14 - 13kg',
                'WH6-20 - 20KG',
                'WH6-27 - 27kg',
                'WH6-33 - 33kg',
                'WN6-8 - 8kg',
                'WS6-8 - 8kg',
                'WN6-9 - 9kg',
                'WS6-9 - 9kg',
                'WN6-11 - 10.5kg',
                'WS6-11 - 10.5kg',
                'WN6-14 - 13kg',
                'WS6-14 - 13kg',
                'WN6-20 - 20kg',
                'WS6-20 - 20kg',
                'WN6-28 - 25kg',
                'WS6-28 - 25kg',
                'WN6-35 - 33kg',
                'WS6-35 - 33kg',

                'TD6-6 - 6kg',
                'TD6-7 - 7kg',
                'TD6-8 - 8kg',
                'TD6-11 - 10.5kg',
                'TD6-14 - 13kg',
                'TD6-20 - 20KG',
                'TD6-27 - 27kg',
                'TD6-33 - 33kg',
            ]
        }
    },
    methods: {
        submit() {
            this.$store.dispatch(`eluxmachine/${this.action}Machine`, {
                machineId: this.machine ? this.machine.id : null,
                formData: this.formData,
                machineType: this.machineType
            }).then((res, rej) => {
                this.$emit('save', {
                    result: res.data.result,
                    applyToAll: this.formData.applyToAll,
                    action: this.action
                });
                this.close();
            });
        },
        close() {
            this.$emit('input', false);
        },
        deleteMachine() {
            if(confirm("Delete this machine?")) {
                this.isDeleting = true;
                axios.post(`/api/machines/elux/${this.machine.id}/delete`).then((res, rej) => {
                    this.close();
                    this.$emit('machineDeleted', this.machine);
                }).catch(err => {

                }).finally(r => {
                    this.isDeleting = false;
                });
            }
        }
    },
    computed: {
        errors() {
            return this.$store.getters['eluxmachine/getErrors'];
        },
        saving() {
            return this.$store.getters['eluxmachine/isSaving'];
        },
        // machineType() {
        //     if(this.machine) {
        //         return this.machine.machine_type[1] == 'w' ? 'washers' : 'dryers';
        //     }
        // },
        isDeveloper() {
            return this.$store.getters.isDeveloper;
        },
        availableModels() {
            if(this.machineType == 'washer') {
                return this.models.filter(function(item) {
                    return item[0] == 'W';
                });
            } else if(this.machineType == 'dryer') {
                return this.models.filter(function(item) {
                    return item[0] == 'T';
                });
            }
        }
    },
    watch: {
        value(val) {
            if(val && this.machine) {
                this.formData.machineType = this.machine.machine_type;
                this.formData.model = this.machine.model;
                this.formData.machineName = this.machine.machine_name;
                this.formData.ipAddress = this.machine.ip_address;
                this.formData.stackOrder = this.machine.stack_order;
                this.action = 'update';
            } else {
                this.formData.machineType = this.machineType;
                this.formData.model = null;
                this.formData.machineName = null;
                this.formData.ipAddress = '192.168.210.';

                this.action = 'insert';
                // get stack order
                this.retreivingStackOrder = true;
                axios.get(`/api/machines/elux/next-stack-order/${this.machineType}`).then((res, rej) => {
                    this.formData.stackOrder = res.data;
                }).finally(() => {
                    this.retreivingStackOrder = false
                });
            }
        }
    }
}
</script>
