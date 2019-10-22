<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="save">
            <v-card>
                <v-card-title class="grey--text title">Edit service price</v-card-title>
                <v-divider></v-divider>
                <v-card-text v-if="service">
                    <h3 class="title grey--text">{{service.service.name}}</h3>
                    <v-text-field v-model="formData.fullServicePrice" label="Full service price" :error-messages="errors.get('fullServicePrice')"></v-text-field>
                    <v-text-field v-model="formData.selfServicePrice" label="Self service price" :error-messages="errors.get('selfServicePrice')"></v-text-field>
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
        'serviceType'
    ],
    data() {
        return {
            formData: {
                fullServicePrice: 0,
                selfServicePrice: 0
            }
        }
    },
    methods: {
        save() {
            this.$store.dispatch('service/updatePrice', {
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
                this.formData.fullServicePrice = this.service.full_service_price;
                this.formData.selfServicePrice = this.service.self_service_price;
            } else {
                this.formData.fullServicePrice = 0;
                this.formData.selfServicePrice = 0;
            }
        }
    }
}
</script>
