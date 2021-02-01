import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false,
    isActivating: false
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
    }
};

const getters = {
    getErrors(state) {
        return state.errors;
    },
    isSaving(state) {
        return state.isSaving;
    },
    isReactivating() {
        return state.isActivating;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
