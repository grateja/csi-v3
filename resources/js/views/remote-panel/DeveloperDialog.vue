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
                <v-btn round @click="test" :loading="activating">test</v-btn>
                <v-btn round @click="cancel" v-if="activating">cancel</v-btn>
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
        test() {
            if(this.machine) {
                console.log('testing');
                this.activating = true;
                this.cancelSource = axios.CancelToken.source();

                axios.get(`http://${this.machine.ip_address}/activate?pulse=1&token=${Math.random().toString(36).substring(7)}`, {
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
