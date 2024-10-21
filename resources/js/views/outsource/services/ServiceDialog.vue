<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card class="rounded-card">
                <v-card-title class="title grey--text">Service details</v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <v-text-field dense outline label="Name" v-model="formData.name" :error-messages="errors.get('name')" ref="name"></v-text-field>
                    <v-text-field dense outline label="Description" v-model="formData.description"></v-text-field>
                    <v-text-field dense outline label="Pulse count" v-model="formData.pulse_count" type="number" :error-messages="errors.get('pulse_count')"></v-text-field>
                    <v-text-field dense outline label="Minutes" v-model="formData.minutes" type="number" :error-messages="errors.get('minutes')"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit" :loading="saving" round>Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'service'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                name: null,
                description: null,
                pulse_count: 0,
                minutes: 0
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.$store.commit('outsourceservice/clearErrors');
        },
        submit() {
            this.$store.dispatch(`outsourceservice/${this.mode}Service`, {
                serviceId: this.service ? this.service.id : null,
                formData: this.formData
            }).then((res, rej) => {
                // this.close();
                this.$emit('save', {
                    mode: this.mode,
                    service: res.data.service
                });
                this.close();
            });
        }
    },
    computed: {
        saving() {
            return this.$store.getters['outsourceservice/isSaving'];
        },
        errors() {
            return this.$store.getters['outsourceservice/getErrors'];
        }
    },
    watch: {
        value(val) {
            if(!!val && this.service) {
                this.mode = 'update';
                this.formData.name = this.service.name;
                this.formData.description = this.service.description;
                this.formData.pulse_count = this.service.pulse_count;
                this.formData.minutes = this.service.minutes;
            } else {
                this.mode = 'insert';
                this.formData.name = null;
                this.formData.pulse_count = null;
                this.formData.description = null;
                this.formData.minutes = 0;
            }
            setTimeout(() => {
                this.$refs.name.$el.querySelector('input').select();
            }, 500);
        }
    }
}
</script>
