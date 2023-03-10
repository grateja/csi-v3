<template>
    <div>
        <v-hover v-slot:default="{ hover }">
            <v-card flat :elevation="hover ? 3 : 0" class="pointer" @click="openDialog = true">
                <div class="px-4">
                    <v-layout class="mt-2">
                        <v-flex>
                            <h3 class="title gray--text">NEW CUSTOMERS</h3>
                        </v-flex>
                        <v-flex xs4 class="text-xs-right title">{{newCustomers}}</v-flex>
                    </v-layout>
                </div>
            </v-card>
        </v-hover>
        <new-customers-dialog v-model="openDialog" :date="date" :until="until" />
    </div>
</template>

<script>
import NewCustomersDialog from '../../../shared/summary-preview/NewCustomersDialog.vue';

export default {
    props: ['newCustomers', 'date', 'until'],
    components: {
        NewCustomersDialog
    },
    data() {
        return {
            openDialog: false
        }
    }
}
</script>