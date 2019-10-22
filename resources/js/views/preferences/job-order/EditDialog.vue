<template>
    <v-dialog :value="value" persistent max-width="640">
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title class="title grey--text">Edit job order format</v-card-title>
                <v-divider class="py-3"></v-divider>

                <v-card-text>
                    <v-text-field v-model="formData.characterCount" :error-messages="errors.get('characterCount')" label="Character count"></v-text-field>
                    <v-text-field v-model="formData.prefix" :error-messages="errors.get('prefix')" label="Prefix"></v-text-field>
                    <v-text-field v-model="formData.nextNumber" :error-messages="errors.get('nextNumber')" label="Next number"></v-text-field>
                </v-card-text>

                <v-card-actions>
                    <v-btn type="submit" class="primary" :loading="loading">
                        save
                    </v-btn>
                    <v-btn @click="cancel">
                        Cancel
                    </v-btn>
                </v-card-actions>
            </v-card>
        </form>

    </v-dialog>
</template>

<script>
export default {
    props: [
        'value',
        'jobOrder'
    ],
    data() {
        return {
            formData: {
                characterCount: 0,
                prefix: null,
                nextNumber: 0
            }
        }
    },
    methods: {
        cancel() {
            this.$store.commit('joborder/clearErrors');
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`joborder/update`, {
                formData: this.formData
            }).then((res, rej) => {
                this.$emit('save', res.data);
                this.$emit('input', false);
            });
        }
    },
    computed: {
        loading() {
            return this.$store.getters['joborder/isSaving'];
        },
        errors() {
            return this.$store.getters['joborder/getErrors'];
        }
    },
    watch: {
        jobOrder(val) {
            if(val) {
                this.formData.characterCount = val.char_count;
                this.formData.prefix = val.prefix;
                this.formData.nextNumber = val.start_number;
            } else {
                this.formData.name = '';
                this.formData.discountType = null;
                this.formData.percent = 0;
            }
        }
    }
}
</script>
