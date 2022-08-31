import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false,
    isUpdating: false
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
    insertLagoonPartner(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post('/api/lagoon-partners/create', data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateLagoonPartner(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/lagoon-partners/${data.lagoonPartnerId}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    deleteLagoonPartner(context, lagoonPartnerId) {
        context.commit('clearErrors');
        return axios.post(`/api/lagoon-partners/${lagoonPartnerId}/delete`).then((res, rej) => {
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
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
