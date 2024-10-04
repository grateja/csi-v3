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
                        <v-combobox :items="models" label="Model" :error-messages="errors.get('model')" v-model="formData.model" outline></v-combobox>
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
            models: []
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
        },
        loadModels() {
            axios.get(`/api/machines/elux/models/${this.machineType}`).then((res) => {
                this.models = res.data;
            });
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
            this.loadModels()
        }
    },
    created() {
        this.loadModels()
    }
}
</script>
