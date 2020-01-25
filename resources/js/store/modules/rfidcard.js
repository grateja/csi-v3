import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false,
    isUpdating: false,
    isLoading: false
};

const mutations = {
    setLoadingStatus(state, status) {
        state.isLoading = status;
    },
    setSavingStatus(state, status) {
        state.isSaving = status;
    },
    setUpdatingStatus(state, status) {
        state.isUpdating = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    }
};

const actions = {
    insertCard(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post('/api/rfid-cards/create', data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateCard(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/rfid-cards/${data.rfidCardId}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    clearUnregisteredCard(context, data) {
        return axios.post(`/api/rfid-cards/unregistered-cards/clear-all`).then((res, rej) => {
            return res;
        });
    },
    topUp(context, data) {
        context.commit('clearErrors');
        context.commit('setSavingStatus', true);
        return axios.post(`/api/rfid-cards/${data.rfidCardId}/top-up`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setSavingStatus', false);
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
        });
    },
    deleteLoadTransaction(context, rfidCardId) {
        context.commit('clearErrors');
        return axios.post(`/api/rfid-cards/load-transactions/${rfidCardId}/delete`).then((res, rej) => {
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
        });
    },
    deleteTransaction(context, rfidCardId) {
        context.commit('clearErrors');
        return axios.post(`/api/rfid-cards/tap-transactions/${rfidCardId}/delete`).then((res, rej) => {
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
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
    isUpdating(state) {
        return state.isUpdating;
    },
    isLoading(state) {
        return state.isLoading;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
