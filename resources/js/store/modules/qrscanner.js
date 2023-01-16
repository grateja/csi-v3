import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    cachedQRData: null
    // isSaving: false,
    // isUpdating: false
};

const mutations = {
    // setLoadingStatus(state, status) {
    //     state.isLoading = status;
    // },
    // setSavingStatus(state, status) {
    //     state.isSaving = status;
    // },
    // setUpdatingStatus(state, status) {
    //     state.isUpdating = status;
    // },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    },
    setCacheQRData(state, QRData) {
        state.cachedQRData = QRData
    }
};

const actions = {
    // insertPoint(context, data) {
    //     context.commit('setSavingStatus', true);
    //     context.commit('clearErrors');
    //     return axios.post('/api/loyalty-points/create', data.formData).then((res, rej) => {
    //         context.commit('setSavingStatus', false);
    //         return res;
    //     }).catch(err => {
    //         context.commit('setErrors', err.response.data.errors);
    //         context.commit('setSavingStatus', false);
    //         return Promise.reject(err);
    //     });
    // },
    // updatePoint(context, data) {
    //     context.commit('setSavingStatus', true);
    //     context.commit('clearErrors');
    //     return axios.post(`/api/loyalty-points/update`, data.formData).then((res, rej) => {
    //         context.commit('setSavingStatus', false);
    //         return res;
    //     }).catch(err => {
    //         context.commit('setErrors', err.response.data.errors);
    //         context.commit('setSavingStatus', false);
    //         return Promise.reject(err);
    //     });
    // }
};

const getters = {
    getCachedQRData(state) {
        return state.cachedQRData
    },
    getErrors(state) {
        return state.errors;
    },
    // isSaving(state) {
    //     return state.isSaving;
    // },
    // isUpdating(state) {
    //     return state.isUpdating;
    // }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
