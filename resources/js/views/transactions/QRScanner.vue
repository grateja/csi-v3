<template>
    <div>
        {{$route.query.jo}}
    </div>
</template>

<script>
export default {
        watch: {
        "$route.query.jo": {
            handler(value) {
                this.$store.commit('qrscanner/setCacheQRData', value)
            },
            deep: true,
            immediate: true
        }
    },
    created() {
    }
}
</script>