var d = new Date();
const state = {
    months: [
        {
            text: "January",
            monthIndex: 1
        },
        {
            text: "February",
            monthIndex: 2
        },
        {
            text: "March",
            monthIndex: 3
        },
        {
            text: "April",
            monthIndex: 4
        },
        {
            text: "May",
            monthIndex: 5
        },
        {
            text: "June",
            monthIndex: 6
        },
        {
            text: "July",
            monthIndex: 7
        },
        {
            text: "August",
            monthIndex: 8
        },
        {
            text: "September",
            monthIndex: 9
        },
        {
            text: "October",
            monthIndex: 10
        },
        {
            text: "November",
            monthIndex: 11
        },
        {
            text: "December",
            monthIndex: 12
        },
    ],
    year: d.getFullYear(),
    monthIndex:  d.getMonth() + 1,
    day:  d.getDate()
};

const mutations = {
    // initMonths(state) {
    //     if(state.months.length == 0) {
    //         for(var i = 0; i < 12; i++) {
    //             var date = moment(new Date().setMonth(i));
    //             state.months.push({
    //                 // date: date,
    //                 text: date.format('MMMM'),
    //                 monthIndex: i + 1,
    //                 // current: date.format('LLLL') == moment().format('LLLL'),
    //                 // active: date.format('LLLL') == moment().format('LLLL')
    //             });
    //         }
    //     }
    // },
    setActiveMonth(state, data) {
        var d = new Date(state.year, data - 1);
        // d.setMonth(state.monthIndex)
        state.monthIndex = d.getMonth() + 1;
        state.year = d.getFullYear();

        // if(d.getFullYear() != state.year) {
        // }
        // var currentActive = state.months.find(m => m.active);
        // if(currentActive) {
        //     currentActive.active = false;
        // }
        // var active = state.months.find(m => m.monthIndex == data);
        // if(active) {
        //     active.active = true;
        // }
    },
    setActiveYear(state, data) {
        state.year = data;
    },
    disolve(state) {
        var d = new Date();
        state.year = d.getFullYear();
        state.monthIndex = d.getMonth() + 1;
        state.day = d.getDate();
        state.months = [];
    }
};

const actions = {
};

const getters = {
    getMonths(state) {
        return state.months;
    },
    getDay(state) {
        return state.day;
    },
    getMonthIndex(state) {
        return state.monthIndex;
    },
    getYear(state) {
        return state.year;
    },
    getDate(state) {
        return moment(state.year + '-' + state.monthIndex + '-' + state.day);
    },
    getActiveMonth(state) {
        return state.months.find(m => m.monthIndex == state.monthIndex);
    },
    // getCurrentMonth(state) {
    //     return state.months.find(m => m.current);
    // },
    // getActiveDay(state) {
    //     var m = state.months.find(m => m.active);
    //     if(m) return moment(m.date).format('D');
    // },
    // getActiveMonth(state) {
    //     return state.months.find(m => m.active);
    // },
    // getActiveMonthIndex(state) {
    //     var m = state.months.find(m => m.active);
    //     if(m) return m.monthIndex;
    // },
    // getActiveYear(state) {
    //     var m = state.months.find(m => m.active);
    //     if(m) return m.date.year();
    //     return 'balie';
    // },
    getDaysInMonth(state) {
        var _monthIndex = ('0' + state.monthIndex).slice(-2)
        var d = moment(state.year + '-' + _monthIndex + '-01');
        return d.daysInMonth() | 0;
    },
    getFirstDayOfMonth() {
        var month = ('0' + state.monthIndex).slice(-2)
        var d = moment(state.year + '-' + month + '-01');
        return d.isoWeekday() - 1;
    },
    getYearsFrom(state) {
        return (Math.ceil(state.year / 10) * (10)) - 9;
        // return this.years[0];
    },
    getYearsUntil(state) {
        return Math.ceil(state.year / 10) * (10);
        // return this.years[this.years.length - 1];
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
