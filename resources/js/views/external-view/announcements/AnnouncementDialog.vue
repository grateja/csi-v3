<template>
    <v-dialog :value="value" max-width="480" persistent>
        <form @submit.prevent="submit">
            <v-card>
                <v-card-title>Announcement info</v-card-title>
                <v-card-text>
                    <v-layout>
                        <v-flex>
                            <v-text-field v-model="formData.dateFrom" label="Date From" type="date" :error-messages="errors.get('dateFrom')" outline />
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.dateUntil" label="Date Until" type="date" :error-messages="errors.get('dateUntil')" outline />
                        </v-flex>
                    </v-layout>
                    <v-checkbox label="Marquee On" v-model="formData.marqueeOn"></v-checkbox>
                    <v-textarea v-model="formData.content" :error-messages="errors.get('content')" rows="3" label="Description" outline></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-btn class="primary" type="submit" round :loading="saving">Save</v-btn>
                    <v-btn @click="close" round>Close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value', 'announcement'
    ],
    data() {
        return {
            mode: 'insert',
            formData: {
                content: '',
                dateFrom: moment().format('YYYY-MM-DD'),
                dateUntil: moment().format('YYYY-MM-DD'),
                marqueeOn: false
            }
        }
    },
    methods: {
        close() {
            this.clear();
            this.$emit('input', false);
        },
        submit() {
            this.$store.dispatch(`announcement/${this.mode}Announcement`, {
                announcementId: this.announcement ? this.announcement.id : null,
                formData: this.formData
            }).then((res, rej) => {
                this.close();
                this.$emit('save', {
                    announcement: res.data.announcement,
                    mode: this.mode
                });
            });
        },
        clear() {
            this.$store.commit('announcement/clearErrors');
        }
    },
    computed: {
        errors() {
            return this.$store.getters['announcement/getErrors'];
        },
        saving() {
            return this.$store.getters['announcement/getSavingStatus'];
        }
    },
    watch: {
        announcement(val) {
            if(val) {
                this.mode = 'update';
                this.formData.dateFrom = val.date_from;
                this.formData.dateUntil = val.date_until;
                this.formData.content = val.content;
                this.formData.marqueeOn = val.marquee_on;
            } else {
                this.mode = 'insert';
                this.formData.dateFrom = moment().format('YYYY-MM-DD');
                this.formData.dateUntil = moment().format('YYYY-MM-DD');
                this.formData.content = '';
                this.formData.marqueeOn = false;
            }
        }
    }
}
</script>
