import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false
};

const mutations = {
    setSavingStatus(state, status) {
        state.isSaving = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    }
};

const actions = {
    insertProductPurchase(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post('/api/product-purchases/create', data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateProduct(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/product-purchases/${data.productId}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    deleteProductPurchase(context, productId) {
        return axios.post(`/api/product-purchases/${productId}/delete-product-purchase`).then((res, rej) => {
            return res;
        }).catch(err => {
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
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
