<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="save">
            <v-card>
                <v-card-title class="grey--text title">Edit service</v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="service">
                    <h3 class="title grey--text">{{service.service.name}}</h3>
                    <v-text-field v-model="formData.minutesPerPulse" label="Minutes per pulse" :error-messages="errors.get('minutesPerPulse')"></v-text-field>
                    <v-text-field v-model="formData.pulseCount" label="Pulse count" :error-messages="errors.get('pulseCount')"></v-text-field>
                    <v-text-field v-model="formData.points" label="Points" :error-messages="errors.get('points')"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn type="submit" class="primary" :loading="saving">ok</v-btn>
                    <v-btn @click="cancel">cancel</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'service',
    ],
    data() {
        return {
            formData: {
                minutesPerPulse: '',
                pulseCount: '',
                points: 0
            }
        }
    },
    methods: {
        save() {
            this.$store.dispatch('service/updateBranchService', {
                serviceId: this.service.id,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('ok', res.data.service);
                this.$emit('input', false);
            });
        },
        cancel() {
            this.$emit('input', false);
        }
    },
    computed: {
        errors() {
            return this.$store.getters['service/getErrors'];
        },
        saving() {
            return this.$store.getters['service/isSaving'];
        }
    },
    watch: {
        service(val) {
            if(val) {
                this.formData.minutesPerPulse = this.service.minutes_per_pulse;
                this.formData.pulseCount = this.service.pulse_count;
                this.formData.points = this.service.points;
            } else {
                this.formData.minutesPerPulse = 0;
                this.formData.pulseCount = 0;
                this.formData.points = 0;
            }
        }
    }
}
</script>
