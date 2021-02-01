<template>
    <v-dialog :value="value" persistent max-width="480">
        <v-card v-if="!!machine && !isToday(machine.time_activated)" class="rounded-card">
            <v-card-title>
                <span class="title gray--text">
                    Available only for today's load
                </span>
                <v-spacer></v-spacer>
                <v-btn icon small @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
        </v-card>
        <form @submit.prevent="submit" v-else>
            <v-card>
                <v-card-title>
                    <span class="title grey--text">Rework</span>
                    <v-spacer></v-spacer>
                    <v-btn icon small @click="close">
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-card-title>
                <v-divider></v-divider>
                <v-progress-linear indeterminate v-if="loading" class="my-0" height="2"></v-progress-linear>
                <wash-dry-info :machine="tempMachine" @loadingStart="loading = true" @load="load"></wash-dry-info>
                <template v-if="!!washDry">
                    <v-card-text>
                        <v-textarea ref="remarks" v-model="remarks" outline label="Reason for rework" :error-messages="errors.get('remarks')"></v-textarea>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-btn type="submit" class="primary" round :loading="saving">Save</v-btn>
                    </v-card-actions>
                </template>
            </v-card>
        </form>
        <!-- <pre>{{washDry}}</pre>
        <pre>{{machine}}</pre> -->
    </v-dialog>
</template>

<script>
import WashDryInfo from './WashDryInfo.vue';

export default {
    components: {
        WashDryInfo
    },
    props: [
        'value', 'machine'
    ],
    data() {
        return {
            remarks: '',
            loading: false,
            tempMachine: null,
            washDry: null
        }
    },
    methods: {
        submit() {
            this.$store.dispatch('remote/reWork', {
                action: this.machine.id,
                customerWashId: this.washDry.id,
                formData: {
                    remarks: this.remarks
                }
            }).then((res, rej) => {
                this.$emit('reworkConfirm', res.data);
                this.$emit('input', false);
            });
        },
        load(washDry) {
            this.washDry = washDry;
            this.loading = false;
            setTimeout(() => {
                this.$refs.remarks.$el.querySelector('textarea').select();
            }, 100);
        },
        failed() {
            this.loading = false;
        },
        close() {
            this.$emit('input', false);
        },
        isToday(someDate) {
            const today = new Date()
            var someDate = new Date(someDate);
            return someDate.getDate() == today.getDate() &&
                someDate.getMonth() == today.getMonth() &&
                someDate.getFullYear() == today.getFullYear()
        }
    },
    computed: {
        errors() {
            return this.$store.getters['remote/getErrors'];
        },
        saving() {
            return this.$store.getters['remote/isReactivating'];
        }
    },
    watch: {
        value(val) {
            if(val && !!this.machine) {
                this.tempMachine = this.machine;
            } else {
                this.washDry = null;
                this.tempMachine = null;
                this.remarks = null;
                this.$store.commit('remote/clearErrors');
            }
        }
    }
}
</script>
