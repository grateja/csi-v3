<template>
    <div class="main-container" ref="monitor">
        <div class="header-wrapper">
            <p>Lagoon Institutional Care</p>
        </div>
        <div class="time-wrapper">
            <div id="time">{{time}}</div>
            <div id="date">{{date}}</div>
        </div>
        <template v-if="action == 'clear'">
            <div class="board-wrapper">
                <div id="boardContent">
                    <!-- <div class="slider slider_circle_10">
                    </div> -->
                </div>
            </div>
        </template>
        <v-card v-if="!!transaction || !!customer" id="transaction" class="translucent rounded-card" width="60vw">
            <v-card-text>
                <v-card class="translucent" v-if="transaction">
                    <v-layout>
                        <v-flex xs4>Staff Name :</v-flex>
                        <v-flex xs8>{{transaction.staff_name}}</v-flex>
                    </v-layout>
                    <pre>{{transaction}}</pre>
                </v-card>
                <v-card class="translucent" v-else-if="customer">
                    <pre>{{customer}}</pre>
                </v-card>
            </v-card-text>
        </v-card>
        <div class="announcement-wrapper">
            <marquee behavior="" direction="">
                <span id="announcementContent"></span>
            </marquee>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            paidPosted: false,
            connection: null,
            lastTimeStamp: null,
            action: '',
            customer: null,
            transactionId: null,
            transaction: null,
            date: null,
            time: null
        }
    },
    methods: {
        startTimer() {
            this.timer = setTimeout(this.check, 1000);
        },
        check() {
            if(this.$refs.monitor) {
                axios.get('/jo-checker/').then((res, rej)=> {
                    console.log(res.data.job_order)
                    this.lastTimeStamp = res.data.updated_at;
                    this.action = res.data.action;
                    this.transactionId = res.data.transaction_id;
                    if(res.data.action == 'retreive' || res.data.action == 'add-service' || res.data.action == 'add-product' || res.data.action == 'add-lagoon' || res.data.action == 'add-scarpa') {
                        this.getJO(res.data.transaction_id);
                    } else if(res.data.action == 'create') {
                        this.getCustomer(res.data.token);
                        this.transaction = null
                    } else if(res.data.action == 'clear') {
                        this.transaction = null;
                        this.customer = null;
                    } else if(res.data.action == 'paid') {
                        this.transaction = null;
                        this.customer = null;
                        if(!this.paidPosted) {
                            this.$store.commit('setFlash', {
                                message: "Payment received",
                                color: 'success'
                            });
                            this.paidPosted = true;
                        }
                        axios.post('/api/pos-transactions/clear-monitor');
                    }
                    setTimeout(this.startTimer, 1000)
                    this.date = moment().format('MMM-DD-YY');
                    this.time = moment().format('h:mm A')
                }).catch(err => {
                    setTimeout(this.startTimer, 4000)
                });
            }
        },
        getJO(transactionId) {
            if(!!transactionId)
            axios.get(`/api/transactions/${transactionId}`).then((res, rej) => {
                this.transaction = res.data.transaction;
            })
        },
        getCustomer(customerId) {
            axios.get(`/api/customers/${customerId}`).then((res, rej) => {
                this.customer = res.data.customer;
            })
        }
    },
    created() {
        this.startTimer()
    },
    watch: {
        lastTimeStamp(current, prev) {
            console.log("prev", prev)
            console.log("current", current)
            if(prev && current) {
                
                let diff = moment(current).diff(prev, 'seconds')
                console.log(diff)
                // if(diff <= )

                this.getJO();
            }
        },
        action(val) {
            if(val && val == 'retreive') {
                this.getJO();
            }
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css?family=Orbitron');
body, html {
    padding: 0px;
    margin: 0px;
    height: 100vh;
    width: 100vw;
    overflow: hidden;
}
.main-container {
    position: relative;
    background-size: 100%;
    height: 100vh;
    background: url('/img/board-main-bg.png');
    margin-top: -70px;
}
.time-wrapper,
.board-wrapper {
    position: absolute;
}
.header-wrapper {
    top: 8vh;
    position: relative;
    margin-left: 280px;
    color: white;
    font-family: 'arial';
    font-style: italic;
    font-size: 3em;
}

.time-wrapper {
    right: 0px;
    top: 0px;
}

.board-wrapper {
    width: 100%;
    top: -40px;
}

.announcement-wrapper {
    position: absolute;
    bottom: 0px;
    font-size: 2em;
    padding: .1em 0;
    width: 100%;
    background: #355f4b;
    color: white;
    border-top: 5px solid #4cc3f8;
}
#boardContent .slider_circle_10 {
    position: relative;
    width: 99vw;
    height: 66.2vw;
    margin: 0 auto;
    user-select: none;
    /* top: -24vh; */
    top: -20%;
    transform: translateY(-20%);
}
#boardContent .slider_circle_10 div.active{
    top: 35%;
    left: 50%;
    margin-left: -36%;
    width: 72%;
    box-shadow: 0px 0px 100px rgba(0, 0, 0, 0.322);
    height: 60%;
    border: 10px solid rgba(0, 255, 255, 0.274);
}
#boardContent .slider_circle_10 div{
    background: none;
    border: 1px solid transparent;
    box-shadow: none;
}

#boardContent img {
    width: 100%;
    max-width: 100%;
}

#boardContent .next1 {
    margin-left: 20%;
}

.time-wrapper {
    padding: 1em;
    color: #4d6569;
}

#time, #date{
    font-family: 'orbitron';
    text-align: right;
    line-height: 1em;
    font-weight: 900;
    text-shadow: 2px 2px 0px white;
}

#time{
    font-size: 8vh;
}
#date {
    font-size: 3vh;
    letter-spacing: .36em;
}

#announcementContent {
    font-family: 'Segoe UI';
}

#transaction {
    margin: auto auto;
}
</style>