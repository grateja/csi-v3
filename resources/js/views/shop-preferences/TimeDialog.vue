<template>
    <v-dialog :value="value" persistent max-width="480">
        <form @submit.prevent="save">
            <v-card v-if="!!storeHour">
                <v-card-title class="grey--text title">{{storeHour.day}}</v-card-title>

                <v-card-text>
                    <v-layout>
                        <v-flex xs12>
                            <v-text-field outline v-model="formData.opensAt" label="Opens at" :error-messages="errors.get('opensAt')"></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field outline v-model="formData.closesAt" label="Closes at" :error-messages="errors.get('closesAt')"></v-text-field>
                        </v-flex>
                    </v-layout>
                </v-card-text>

                <v-card-actions>
                    <v-btn type="submit" class="primary" round :loading="saving">Save</v-btn>
                    <v-btn @click="close" round>close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'storeHour'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                opensAt: null,
                closesAt: null
            }
        }
    },
    methods: {
        close() {
            this.$emit('input', false);
        },
        save() {
            this.$store.dispatch(`storehour/${this.mode}StoreHour`, {
                storeHourId: this.storeHour ? this.storeHour.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    mode: this.mode,
                    storeHour: res.data.storeHour
                });
            });
        }
    },
    watch: {
        value(val) {
            if(val && this.storeHour) {
                this.formData.opensAt = this.storeHour.opens_at;
                this.formData.closesAt = this.storeHour.closes_at;
                this.mode = 'update';
            } else {
                this.formData.opensAt = null;
                this.formData.closesAt = null;
                this.mode = 'insert';
            }
        }
    },
    computed: {
        saving() {
            return this.$store.getters['storehour/isSaving'];
        },
        errors() {
            return this.$store.getters['storehour/getErrors'];
        }
    }
}
</script>
