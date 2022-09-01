<template>
    <v-dialog :value="value" persistent max-width="480px">
        <v-card v-if="machine">
            <v-card-title>
                <span class="title grey--text">{{machine.machine_name}}</span>
                <v-spacer></v-spacer>
                <v-btn small icon @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-actions>
                <template v-if="activating">
                    <v-btn round @click="cancel">cancel</v-btn>
                    <v-progress-circular indeterminate />
                </template>
                <template v-else-if="!!machine && machine.machine_type[1] == 'w'">
                    <v-btn round @click="test(1)" :loading="activating">delicate</v-btn>
                    <v-btn round @click="test(2)" :loading="activating">warm/cold</v-btn>
                    <v-btn round @click="test(3)" :loading="activating">hot</v-btn>
                    <v-btn round @click="test(4)" :loading="activating">super wash</v-btn>
                </template>
                <template v-else>
                    <v-btn round @click="test(1)" :loading="activating">10 min</v-btn>
                    <v-btn round @click="test(2)" :loading="activating">20 min</v-btn>
                    <v-btn round @click="test(3)" :loading="activating">30 min</v-btn>
                    <v-btn round @click="test(4)" :loading="activating">40 min</v-btn>
                </template>
            </v-card-actions>
            <v-card-text>
                <!-- <pre>{{machine}}</pre> -->
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'machine', 'value'
    ],
    data() {
        return {
            activating: false,
            cancelSource: null
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        test(pulse) {
            if(this.machine) {
                console.log('testing');
                this.activating = true;
                this.cancelSource = axios.CancelToken.source();

                axios.get(`http://${this.machine.ip_address}/activate?pulse=${pulse}&token=${Math.random().toString(36).substring(7)}`, {
                    params: {},
                    cancelToken: this.cancelSource.token
                }).then((res, rej) => {

                }).finally(() => {
                    this.activating = false;
                });

            }
        },
        cancel() {
            if(this.cancelSource) {
                this.cancelSource.cancel();
            }
        }
    }
}
</script>
