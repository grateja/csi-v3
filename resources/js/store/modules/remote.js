import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false,
    isActivating: false,
    isTesting: false,
    cancelSource: null,
    ping: {
        machine: null,
        active: false,
        responseTime: -1
    }
};

const mutations = {
    setSavingStatus(state, status) {
        state.isSaving = status;
    },
    setActivatingStatus(state, status) {
        state.isActivating = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    },
    setActivePing(state, data) {
        state.ping.machine = data.machine;
        state.ping.active = data.active;
        state.ping.responseTime = data.responseTime;
    }
};

const actions = {
    activateMachine(context, data) {
        context.commit('clearErrors');
        return axios.post(`/api/remote/machines/activate`, data.formData).then((res, rej) => {
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
        });
    },
    activateOsl(context, data) {
        context.commit('clearErrors');
        return axios.post(`/api/out-source/remote/activate`, data.formData).then((res, rej) => {
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
        });
    },
    forceStop(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/remote/machines/force-stop`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    reWork(context, data) {
        context.commit('clearErrors');
        context.commit('setActivatingStatus', true);
        // data.action = transfer|repeat
        return axios.post(`/api/re-works/${data.action}`, data.formData).then((res, rej) => {
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
        }).finally(() => {
            context.commit('setActivatingStatus', false);
        });
    },
    transfer(context, data) {
        context.commit('clearErrors');
        context.commit('setActivatingStatus', true);
        return axios.post(`/api/re-works/transfer/${data.from}/${data.to}`, data.formData).then((res, rej) => {
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
        }).finally(() => {
            context.commit('setActivatingStatus', false);
        });
    },
    ping(context, machine) {
        let startTime = new Date().getTime();
        let responseTime = -1;
        context.commit('setActivePing', {
            machine,
            active: true,
            responseTime
        });
        return axios.get(`http://${machine.ip_address}`).then((res, rej) => {
            responseTime = new Date().getTime() - startTime;
            context.commit('setActivePing', {
                machine,
                active: false,
                response: res.data
            });
        }).catch(err => {
            responseTime = new Date().getTime() - startTime;
        }).finally(() => {
            context.commit('setActivePing', {
                machine,
                active: false,
                responseTime
            });
        })
    }
};

const getters = {
    getErrors(state) {
        return state.errors;
    },
    isSaving(state) {
        return state.isSaving;
    },
    isReactivating(state) {
        return state.isActivating;
    },
    getActivePing(state) {
        return state.ping;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
