<template>
    <v-card class="rounded-card translucent">
        <v-card-actions>
            <v-btn icon @click="$emit('prev')" :loading="loading && action == 'prev'">
                <v-icon>chevron_left</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <!-- <v-btn round @click="$emit('browse')" outline class="primary"> -->
                <span class="title grey--text font-weight-bold">{{text}}</span>
                <!-- <v-icon right color="primary">
                    open_in_new
                </v-icon> -->
            <!-- </v-btn> -->
            <v-spacer></v-spacer>
            <v-btn icon @click="$emit('next')" :loading="loading && action == 'next'">
                <v-icon>chevron_right</v-icon>
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    props: [
        'text',
        'action',
        'loading'
    ]
}
</script>
