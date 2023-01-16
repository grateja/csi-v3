<template>
    <v-dialog :value="value" max-width="520px" persistent>
        <form @submit.prevent="ok">
            <v-card v-if="rfidServicePrice">
                <v-card-title class="title">
                    Edit service price {{rfidServicePrice.machine_type.name}}
                </v-card-title>
                <v-card-text>
                    <v-text-field v-model="formData.name" label="Name" :error-messages="errors.get('name')"></v-text-field>
                    <v-text-field v-model="formData.price" label="Price" :error-messages="errors.get('price')"></v-text-field>
                    <v-text-field v-model="formData.minutes" label="Minutes per tap" :error-messages="errors.get('minutes')"></v-text-field>
                    <v-checkbox v-model="formData.enabled" label="Enabled"></v-checkbox>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="cancel">cancel</v-btn>
                    <v-btn class="primary" type="submit" :loading="updating">Ok</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'rfidServicePrice'
    ],
    components: {
    },
    data() {
        return {
            formData: {
                name: null,
                price: null,
                minutes: null,
                enabled: false
            }
        }
    },
    methods: {
        cancel() {
            this.$emit('input', false);
        },
        ok() {
            this.$store.dispatch('serviceprice/updateServicePrice', {
                servicePriceId: this.rfidServicePrice.id,
                formData: this.formData
            }).then((res, rej) => {
                console.log('data', res.data);
                this.$emit('save', res.data.servicePrice);
                this.$emit('input', false);
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['serviceprice/getErrors'];
        },
        updating() {
            return this.$store.getters['serviceprice/isUpdating'];
        }
    },
    watch: {
        value(val) {
            if(val) {
                if(this.rfidServicePrice) {
                    this.formData.name = this.rfidServicePrice.name;
                    this.formData.price = this.rfidServicePrice.price;
                    this.formData.minutes = this.rfidServicePrice.minutes;
                    this.formData.enabled = this.rfidServicePrice.enabled;
                }
            } else {
                this.formData.name = null;
                this.formData.price = null;
                this.formData.minutes = null;
                this.formData.enabled = false;
            }
        }
    }
}
</script>
