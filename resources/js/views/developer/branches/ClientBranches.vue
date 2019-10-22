<template>
    <v-card>
        <v-card-text>
            <form @submit.prevent="filter">
                <v-text-field label="Filter" v-model="keyword" append-icon="search"></v-text-field>
            </form>
        </v-card-text>
    </v-card>
</template>

<script>
export default {
    data() {
        return {
            keyword: this.$route.query.keyword,
            page: this.$route.query.page || 1
        }
    }
}
</script>
