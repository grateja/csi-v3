<template>
    <v-dialog :value="value" max-width="500" persistent>
        <form @submit.prevent="submit">
            <v-card class="rounded-card">
                <v-card-title class="title grey--text">Machine tap card configuration</v-card-title>
                <v-card-text>
                    <template v-if="isDeveloper">
                        <pre>{{mt}}</pre>
                        <v-btn v-if="action == 'update'" round class="translucent" @click="deleteMachine" :loading="isDeleting">
                            <v-icon left>delete</v-icon> Delete
                        </v-btn>
                        <v-text-field outline label="Machine name" v-model="formData.machineName" :error-messages="errors.get('machineName')"></v-text-field>
                        <v-text-field outline label="IP address" v-model="formData.ipAddress" :error-messages="errors.get('ipAddress')"></v-text-field>
                    </template>
                    <v-text-field outline label="Initial price" v-model="formData.initialPrice" :error-messages="errors.get('initialPrice')"></v-text-field>
                    <v-text-field outline label="Additional price" v-model="formData.additionalPrice" :error-messages="errors.get('additionalPrice')" hint="Set to 0 to disable"></v-text-field>
                    <v-text-field outline label="Initial time" v-model="formData.initialTime" :error-messages="errors.get('initialTime')"></v-text-field>
                    <v-text-field outline label="Additional time" v-model="formData.additionalTime" :error-messages="errors.get('additionalTime')" hint="Set to 0 to disable"></v-text-field>
                    <v-checkbox :label="`Apply to all ${machineType}`" v-model="formData.applyToAll"></v-checkbox>
                    <div class="font-italic font-weight-bold caption" v-if="formData.applyToAll">This configuration will be applied to all {{machineType}}</div>
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
        'machine', 'value', 'mt'
    ],
    data() {
        return {
            formData: {
                machineName: null,
                initialPrice: 0,
                additionalPrice: 0,
                initialTime: 0,
                additionalTime: 0,
                ipAddress: null,
                applyToAll: false
            },
            action: null,
            isDeleting: false
        }
    },
    methods: {
        submit() {
            this.$store.dispatch(`machine/${this.action}Machine`, {
                machineId: this.machine ? this.machine.id : null,
                formData: this.formData,
                machineType: this.mt
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
                axios.post(`/api/machines/${this.machine.id}/delete`).then((res, rej) => {
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
            return this.$store.getters['machine/getErrors'];
        },
        saving() {
            return this.$store.getters['machine/isSaving'];
        },
        machineType() {
            if(this.machine) {
                return this.machine.machine_type[1] == 'w' ? 'washers' : 'dryers';
            }
        },
        isDeveloper() {
            return this.$store.getters.isDeveloper;
        }
    },
    watch: {
        value(val) {
            if(val && this.machine) {
                this.formData.machineName = this.machine.machine_name;
                this.formData.initialPrice = this.machine.initial_price;
                this.formData.additionalPrice = this.machine.additional_price;
                this.formData.initialTime = this.machine.initial_time;
                this.formData.additionalTime = this.machine.additional_time;
                this.formData.ipAddress = this.machine.ip_address;
                this.action = 'update';
            } else {
                this.formData.machineName = null;
                this.formData.initialPrice = 0;
                this.formData.additionalPrice = 0;
                this.formData.initialTime = 0;
                this.formData.additionalTime = 0;
                this.formData.applyToAll = false;
                this.formData.ipAddress = '192.168.210.';
                this.action = 'insert';
            }
        }
    }
}
</script>
