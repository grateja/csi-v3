<template>
    <div class="px-4">
        <v-layout class="mt-2">
            <v-flex>
                <h3 class="title gray--text font-weight-bold">TOTAL DEPOSIT</h3>
            </v-flex>
            <v-flex xs4 class="text-xs-right title font-weight-bold">{{deposit | peso}}</v-flex>
        </v-layout>
    </div>
</template>

<script>
export default {
    props: ['deposit']
}
</script>
