import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isUpdating: false,
};

const mutations = {
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
    updateServicePrice(context, data) {
        context.commit('setUpdatingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/service-prices/${data.servicePriceId}/update`, data.formData).then((res, rej) => {
            context.commit('setUpdatingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setUpdatingStatus', false);
            return Promise.reject(err);
        });
    }
};

const getters = {
    getErrors(state) {
        return state.errors;
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
