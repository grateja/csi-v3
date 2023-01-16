<template>
    <v-dialog persistent max-width="420px" v-model="value">
        <v-card ref="checker">
            <v-card-title>
                <span class="title grey--text">Please tap the card</span>
                <v-spacer />
                <v-btn icon @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>{{rfid}}</v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: [
        'value'
    ],
    data() {
        return {
            rfid: null,
            id: null
        }
    },
    methods: {
        startTimer() {
            this.timer = setTimeout(this.get, 500);
        },
        get() {
            if(this.$refs.checker && this.value) {
                axios.get('/checker/rfid.php').then((res, rej)=> {
                    if(this.id != null && this.id != res.data.id) {
                        this.update(res.data.rfid);
                    }
                    this.id = res.data.id
                    setTimeout(this.startTimer, 300)
                }).catch(err => {
                    setTimeout(this.startTimer, 4000)
                });
            }
        },
        close() {
            this.$emit('input', false);
        },
        update(rfid) {
            this.rfid = rfid;
            this.$emit('select', rfid);
            this.close();
            console.log("rfid from terminal", rfid)
        }
    },
    watch: {
        value(val) {
            this.rfid = null;
            this.id = null;
            if(val) {
                this.startTimer();
            }
        }
    }
}
</script>