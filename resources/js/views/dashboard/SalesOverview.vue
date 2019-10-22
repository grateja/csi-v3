<template>
    <div class="sales-overview-wrapper">
        <div class="sales-overview-pane">
            <ul class="amounts">
                <li v-for="i in amounts" :key="i.index">
                    <span>{{i.amount}}</span>
                </li>
                <li>
                    <ul class="months">
                        <li v-for="month in months" :key="month.index">
                            <v-btn class="btn-month ma-0" block flat :class="{'font-weight-bold blue--text': month == value}" @click="$emit('input', month)">
                                {{month.monthName.substring(0, 3)}}
                            </v-btn>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="progress-wrapper">
                <v-layout>
                    <v-flex width="80px" v-for="month in months" :key="month.index" class="month-div">
                        <v-card :height="maxAmount * 40" flat class="transparent" width="80px">
                            <div class="bars">
                                <div class="total-expenses" :style="`height:${(month.totalExpenses / (maxAmount * 1000)) * 100}%`">
                                </div>
                                <div class="total-income" :style="`height:${(month.totalIncome / (maxAmount * 1000)) * 100}%`">
                                    <!-- {{month.totalIncome}} -->
                                </div>
                            </div>
                        </v-card>
                    </v-flex>
                </v-layout>
            </div>
            <!-- <ul class="months">
                <li v-for="month in months" :key="month.index">
                    <div class="expenses">
                        {{month.totalExpenses}}
                    </div>
                    <div class="income">
                        {{month.totalIncome}}
                    </div>
                </li>
            </ul> -->
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'months',
        'maxAmount',
        'value'
    ],
    data() {
        return {
            amountsList: [],
            // value: null
        }
    },
    computed: {
        amounts() {
            this.amountsList = [];
            if(this.maxAmount) {
                for (let index = this.maxAmount; index >= 0; index--) {
                    this.amountsList.push({
                        index,
                        amount: index * 1000
                    });
                }
                return this.amountsList;
            }
        }
    }
}
</script>

<style scoped>
.bars {
    display: flex;
    height: 100%;
    justify-content: center;
    align-items: flex-end;
}

.bars div {
    width: 20px;
    /* border: 1px solid red; */
    height: 0%;
}
.bars div.total-expenses {
    background: #2196f3;
}

.bars div.total-income {
    background: #4caf50;
}

.sales-overview-wrapper {
    overflow-x: auto;
}
.sales-overview-pane {
    /* border: 2px solid rgb(197, 245, 252); */
    overflow: hidden;
    min-width: 720px;
    position: relative;
}
ul.amounts {
    padding: 0px;
    margin: 0px;
    position: relative;
    transform: translateY(-10px);
}

ul.amounts > li {
    list-style: none;
    /* padding: 10px; */
    height: 40px;
    margin: 0px;
    position: relative;
    transform: translateY(30px);
    color: grey;
    font-weight: bold;
}

ul.amounts > li span {
    width: 50px;
    padding-right: 10px;
    text-align: right;
    display: inline-block;
}

ul.amounts > li:last-child{
    border-bottom: none;
    transform: translateY(0%);
    padding: 0px;
}

ul.amounts > li:last-child:before{
    border-bottom: none;
}

ul.amounts > li:before {
    content: "";
    border-bottom: 1px dashed grey;
    display: block;
    position: absolute;
    width: 100%;
    height: 40px;
    transform: translate(50px, -25px);
}

.months-wrapper {
    position: absolute!important;
    top: 0px!important;
    /* border: 1px solid red!important; */
}
ul.months {
    display: flex;
    padding: 0px;
    padding-left: 60px;
    /* justify-content: flex-end; */
    align-items: stretch;
    /* border: 1px solid red; */
    height: 110%;
}
ul.months li {
    width: 80px;
    list-style: none;
    /* border: 1px solid grey; */
    color: grey;
    text-align: center;
    font-weight: bold;
    position: relative;
}
.btn-month {
    position: relative!important;
    bottom: 0px!important;
    width: 100%!important;
}

div.progress-wrapper {
    /* border: 1px solid red; */
    position: absolute;
    width: 100% - 50px;
    top: 20px;
    transform: translate(60px, 15px);
}
.month-div {
    border-left: 1px dotted rgb(233, 233, 233);
}
</style>
