<template>
    <v-dialog :value="value" persistent max-width="640px">
        <form method="post" @submit.prevent="submit">
            <v-card>
                <v-card-title class="title">Event details</v-card-title>
                <v-card-text>

                    <!-- <v-menu
                        v-model="menu1"
                        :close-on-content-click="false"
                        full-width
                        max-width="290"
                    >
                    <template v-slot:activator="{ on }">
                        <v-text-field
                            :value="computedDateFormattedMomentjs"
                            clearable
                            label="Date"
                            readonly
                            v-on="on"
                            :error-messages="errors.get('date')"
                        ></v-text-field>
                    </template>
                    </v-menu> -->
                    <!-- <v-date-picker
                        v-model="date"
                        range
                    ></v-date-picker> -->
                    <v-layout>
                        <v-flex>
                            <v-text-field v-model="formData.dateFrom" label="Date From" type="date" :error-messages="errors.get('dateFrom')" outline />
                        </v-flex>
                        <v-flex>
                            <v-text-field v-model="formData.dateUntil" label="Date Until" type="date" :error-messages="errors.get('dateUntil')" outline />
                        </v-flex>
                    </v-layout>
                    <v-text-field v-model="formData.title" label="Title" :error-messages="errors.get('title')" outline></v-text-field>
                    <v-textarea v-model="formData.description" rows="3" label="Description" outline></v-textarea>

                    <v-radio-group v-model="formData.eventTypeId" row>
                        <v-radio :value="1" label="Slide show"></v-radio>
                        <v-radio :value="2" label="Video"></v-radio>
                    </v-radio-group>
                </v-card-text>
                <v-card-actions class="pa-3">
                    <v-spacer></v-spacer>
                    <v-btn class="primary flat ma-0" type="submit" :loading="loading">{{mode}}</v-btn>
                    <v-btn @click="close">close</v-btn>
                </v-card-actions>
            </v-card>
        </form>
    </v-dialog>
</template>

<script>
import moment from 'moment';
export default {
    props: [
        'value',
        'event'
    ],
    data() {
        return {
            formData: {
                dateFrom: moment().format('YYYY-MM-DD'),
                dateUntil: moment().format('YYYY-MM-DD'),
                title: '',
                description: '',
                eventTypeId: 1
            },
            mode: 'insert'
        }
    },
    methods: {
        submit() {
            if(this.loading) return;

            // let data = {
            //     request: {
            //         id: this.event ? this.event.id : 0,
            //         date: this.date,
            //         title: this.title,
            //         description: this.description,
            //         event_type_id: this.eventTypeId
            //     }
            // }

            this.$store.dispatch(`event/${this.mode}Event`, {
                eventId: this.event ? this.event.id : null,
                formData: this.formData
            }).then((res, rej) => {
                if(this.mode == 'insert') {
                    this.$router.push('/external-view/events/' + res.data.id);
                } else {
                    this.close();
                }
            });
        },
        close() {
            this.$emit('input', false);
            this.$store.commit('event/clearErrors');
        }
    },
    computed: {
        computedDateFormattedMomentjs () {
            // return this.date ? moment(this.date).format('dddd, MMMM Do YYYY') : ''
        },
        loading() {
            return this.$store.getters['event/getSavingStatus'];
        },
        errors() {
            return this.$store.getters['event/getErrors'];
        }
    },
    watch: {
        event(val) {
            if(val) {
                this.mode = 'update';
                this.formData.title = val.title;
                this.formData.dateFrom = val.date_from;
                this.formData.dateUntil = val.date_until;
                this.formData.description = val.description;
                this.formData.eventTypeId = val.event_type_id;
            } else {
                this.mode = 'insert';
                this.formData.title = '';
                this.formData.dateFrom = moment().format('YYYY-MM-DD');
                this.formData.dateUntil = moment().format('YYYY-MM-DD');
                this.formData.description = '';
                this.formData.eventTypeId = 1;
            }
        }
    },
    beforeDestroy() {
        this.$store.commit('event/clearErrors');
    }
}
</script>
