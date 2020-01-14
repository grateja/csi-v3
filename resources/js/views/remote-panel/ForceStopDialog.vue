<template>
    <v-dialog :value="value" max-width="480px" persistent>
        <v-card>
            <v-card-title class="title grey--text">Force stop machine</v-card-title>
                <v-card-text>
                    <ul>
                        <li>"Force stop" is used for emergency purposes only.</li>
                        <li>Applying force stop does not actually stop the cycle of the machine.</li>
                        <li>Transaction will not be rolled back.</li>
                    </ul>

                    <v-textarea label="Remarks" v-model="remarks" :error-messages="errors.get('remarks')" outline></v-textarea>
                </v-card-text>
            <v-card-actions>
                <v-btn @click="forceStop" round :loading="saving" class="primary">Continue</v-btn>
                <v-btn @click="close" round>cancel</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'machine'
    ],
    data() {
        return {
            remarks: null
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        forceStop() {
            this.$store.dispatch('remote/forceStop', {
                formData: {
                    remarks: this.remarks,
                    machineId: this.machine.id
                }
            }).then((res, rej) => {
                this.$emit('forceStop', res.data);
                this.close();
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['remote/getErrors'];
        },
        saving() {
            return this.$store.getters['remote/isSaving'];
        }
    }
}
</script>
