var d = new Date();
const state = {
    months: [],
    year: d.getFullYear(),
    monthIndex:  d.getMonth() + 1,
    day:  d.getDate()
};

const mutations = {
    initMonths(state) {
        if(state.months.length == 0) {
            for(var i = 0; i < 12; i++) {
                var date = moment(new Date().setMonth(i));
                state.months.push({
                    // date: date,
                    text: date.format('MMMM'),
                    monthIndex: i + 1,
                    // current: date.format('LLLL') == moment().format('LLLL'),
                    // active: date.format('LLLL') == moment().format('LLLL')
                });
            }
        }
    },
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
        var d = moment(state.year + '-' + state.monthIndex + '-' + state.day);
        return d.daysInMonth();
    },
    getFirstDayOfMonth() {
        var d = moment(state.year + '-' + state.monthIndex + '-01');
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
