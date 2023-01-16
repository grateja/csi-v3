<template>
    <v-card class="pa-3" v-if="!!event">
        <form method="post" @submit.prevent="submit">
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
                v-model="event.date"
                @change="menu1 = false"
            ></v-date-picker>
            </v-menu>
            <v-text-field v-model="event.title" label="Title" :error-messages="errors.get('title')"></v-text-field>
            <v-textarea v-model="event.description" rows="3" label="Description"></v-textarea>

            <v-btn class="primary flat ma-0" type="submit" :loading="loading">{{mode}}</v-btn>
        </form>
    </v-card>
</template>

<script>
import moment from 'moment';

export default {
    props: {
        value: {
            required: true
        }
    },
    data() {
        return {
            event: {
                date: new Date().toISOString().substr(0, 10)
            },
            menu1: false,
            mode: 'create'
        }
    },
    methods: {
        submit() {
            if(this.loading) return ;
            this.$emit('submit', {
                request: this.event,
                mode: this.mode
            });
        }
    },
    computed: {
        computedDateFormattedMomentjs () {
            return this.event.date ? moment(this.event.date).format('dddd, MMMM Do YYYY') : ''
        },
        loading() {
            return this.$store.getters['event/getSavingStatus'];
        },
        errors() {
            return this.$store.getters['event/getErrors'];
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.event = val;
                this.mode = 'update';
            }
        }
    },
    beforeDestroy() {
        this.$store.commit('event/clearErrors');
    }
}
</script>
