<template>
    <v-dialog :value="value" persistent max-width="640px">
        <form method="post" @submit.prevent="submit">
            <v-card>
                <v-card-title class="title">Event details</v-card-title>
                <v-card-text>

                    <v-menu
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
                    <v-date-picker
                        v-model="date"
                        @change="menu1 = false"
                    ></v-date-picker>
                    </v-menu>
                    <v-text-field v-model="title" label="Title" :error-messages="errors.get('title')"></v-text-field>
                    <v-textarea v-model="description" rows="3" label="Description"></v-textarea>

                    <v-radio-group v-model="eventTypeId" row>
                        <v-radio :value="1" label="Slide show"></v-radio>
                        <v-radio :value="2" label="Video"></v-radio>
                        <v-radio :value="3" label="Text"></v-radio>
                        <v-radio :value="4" label="Youtube link"></v-radio>
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
            date: new Date().toISOString().substr(0, 10),
            title: '',
            description: '',
            menu1: false,
            mode: 'create',
            eventTypeId: 1
        }
    },
    methods: {
        submit() {
            if(this.loading) return ;

            let data = {
                request: {
                    id: this.event ? this.event.id : 0,
                    date: this.date,
                    title: this.title,
                    description: this.description,
                    event_type_id: this.eventTypeId
                }
            }

            this.$store.dispatch(`event/${this.mode}Event`, data.request).then((res, rej) => {
                this.$router.push('/events/' + res.data.id);
            });
        },
        close() {
            this.$emit('input', false);
            this.$store.commit('event/clearErrors');
        }
    },
    computed: {
        computedDateFormattedMomentjs () {
            return this.date ? moment(this.date).format('dddd, MMMM Do YYYY') : ''
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
                this.title = val.title;
                this.date = val.date;
                this.description = val.description;
                this.eventTypeId = val.event_type_id;
            } else {
                this.mode = 'create';
                this.title = '';
                this.date = new Date().toISOString().substr(0, 10);
                this.description = '';
                this.eventTypeId = 1;
            }
        }
    },
    beforeDestroy() {
        this.$store.commit('event/clearErrors');
    }
}
</script>
