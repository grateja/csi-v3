<template>
    <div class="px-4">
        <v-divider class="my-2"></v-divider>
        <v-layout>
            <v-flex xs6>
                <h3 class="title font-weight-bold">TOTAL SALES</h3>
            </v-flex>
            <v-flex xs6 class="text-xs-right">
                <h3 class="font-weight-bold title">
                    {{totalSales | peso}}
                </h3>
            </v-flex>
        </v-layout>
        <v-divider class="my-1 transparent"></v-divider>
    </div>
</template>

<script>
export default {
    props: [
        'totalSales'
    ]
}
</script>
