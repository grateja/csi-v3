<template>
    <div class="px-4">
        <v-layout class="mt-2">
            <v-flex>
                <h3 class="title gray--text">NEW CUSTOMERS</h3>
            </v-flex>
            <v-flex xs4 class="text-xs-right title">{{newCustomers}}</v-flex>
        </v-layout>
    </div>
</template>

<script>
export default {
    props: ['newCustomers']
}
</script>
