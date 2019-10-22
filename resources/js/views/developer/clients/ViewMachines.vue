<template>
    <div>
        <pre>{{machines}}</pre>
    </div>
</template>

<script>
export default {
    data() {
        return {
            machines: []
        }
    },
    methods: {
        get() {

            // branchId
            axios.get(`/api/machines/${this.$route.params.id}/view-all`).then((res, rej) => {
                console.log('machines', res.data);
                this.machines = res.data.result;
            });
        }
    },
    created() {
        this.get();
    }
}
</script>
