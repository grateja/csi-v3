<template>
    <div>
        <h3 class="title grey--text">Job order</h3>
        <v-divider class="my-3"></v-divider>
        <v-card max-width="480px">
            <v-card-title class="title grey--text">Job order format
                <v-spacer></v-spacer>
            </v-card-title>
            <v-progress-linear v-if="loading" indeterminate></v-progress-linear>
            <v-card-text v-if="jobOrder">
                <dl>
                    <dt class="caption grey--text font-weight-bold">Character count</dt>
                    <dd class="ml-3">{{jobOrder.char_count}}</dd>

                    <dt class="caption grey--text font-weight-bold">Prefix</dt>
                    <dd class="ml-3">{{jobOrder.prefix}}</dd>

                    <dt class="caption grey--text font-weight-bold">Next number</dt>
                    <dd class="ml-3">{{jobOrder.start_number}}</dd>

                    <dt class="caption grey--text font-weight-bold">Format</dt>
                    <dd class="ml-3">{{jobOrder.format}}</dd>

                    <dt class="caption grey--text font-weight-bold">Example</dt>
                    <dd class="ml-3">{{example}}</dd>
                </dl>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="edit">
                    <v-icon left small>edit</v-icon>
                    Edit
                </v-btn>
            </v-card-actions>
        </v-card>
        <edit-dialog v-model="openEditDialog" :jobOrder="jobOrder" @save="save"></edit-dialog>
    </div>
</template>

<script>
import EditDialog from './EditDialog.vue';

export default {
    components: {
        EditDialog
    },
    data() {
        return {
            jobOrder: null,
            mode: 'insert',
            loading: false,
            example: null,
            openEditDialog: false
        }
    },
    methods: {
        get() {
            this.loading = true;
            axios.get('/api/job-order/get').then((res, rej) => {
                this.mode = 'update';
                this.jobOrder = res.data.jobOrder;
                this.example = res.data.example;
            }).catch(err => {
                this.mode = 'insert';
            }).finally(() => {
                this.loading = false;
            });
        },
        save(data) {
            this.jobOrder = data.jobOrder;
            this.example = data.example;
        },
        edit() {
            this.openEditDialog = true;
        }
    },
    computed: {
        errors() {
            return this.$store.getters['joborder/getErrors'];
        },
        saving() {
            return this.$store.getters['joborder/isSaving'];
        }
    },
    created() {
        this.get();
    }
}
</script>
