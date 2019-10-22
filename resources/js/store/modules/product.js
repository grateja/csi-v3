import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false,
    isUpdating: false,
    isLoading: false,
    products: []
};

const mutations = {
    setProducts(state, products) {
        state.products = products;
    },
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
    },
    deductProduct(state, data) {
        state.products.find(p => p.id == data.productId).available -= data.quantity;
    }
};

const actions = {
    insertProduct(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post('/api/products/self/create', data.formData).then((res, rej) => {
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
        return axios.post(`/api/products/${data.productId}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updatePrice(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/branch-products/${data.productId}/update-price`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    loadProducts(context, data) {
        context.commit('setLoadingStatus', true);
        return axios.get(`/api/search/products/self`, {
            params: data.query
        }).then((res, rej) => {
            context.commit('setLoadingStatus', false);
            context.commit('setProducts', res.data.result.data);
            return res;
        }).catch(err => {
            context.commit('setLoadingStatus', false);
            return Promise.reject(err);
        });
    },
    addStock(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/products/${data.productId}/add-stock`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
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
    getProducts(state) {
        return state.products;
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
