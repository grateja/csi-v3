<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card class="rounded-card">
                <v-card-title class="title grey--text">Variation details</v-card-title>

                <v-card-text>
                    <v-text-field label="Action" v-model="formData.action" :error-messages="errors.get('action')" outline ref="action"></v-text-field>
                    <v-text-field label="Price" v-model="formData.sellingPrice" outline></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" round :loading="saving" type="submit">save</v-btn>
                    <v-btn round @click="close">close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>
<script>
export default {
    props: [
        'value', 'variation', 'serviceId'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                action: "Any",
                sellingPrice: 0
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
            this.$store.commit('scarpacleaning/clearErrors');
        },
        submit() {
            this.$store.dispatch(`scarpacleaning/${this.mode}Variation`, {
                serviceId: this.serviceId,
                variationId: this.variation ? this.variation.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('save', {
                    mode: this.mode,
                    variation: res.data.variation
                });
                this.close();
            });
        }
    },
    computed: {
        errors() {
            return this.$store.getters['scarpacleaning/getErrors'];
        },
        saving() {
            return this.$store.getters['scarpacleaning/isSaving'];
        }
    },
    watch: {
        value(val) {
            if(!!val && this.variation) {
                this.mode = 'update';
                this.formData.sellingPrice = this.variation.selling_price;
                this.formData.action = this.variation.action;
            } else {
                this.mode = 'insert';
                this.formData.sellingPrice = 0;
                this.formData.action = "Any";
            }
            setTimeout(() => {
                this.$refs.action.$el.querySelector('input').select();
            }, 500);
        },
        variation(val) {
            if(!!val) {
                this.mode = 'update';
            } else {
                this.mode = 'insert';
            }
        }
    }
}
</script>
