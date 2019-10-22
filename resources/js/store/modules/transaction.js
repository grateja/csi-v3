import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false,
    isUpdating: false,
    isLoading: false,
    addingItem: false,
    products: [],
    services: [],
    summary: null,
    serviceSummary: null,
    transaction: null
};

const mutations = {
    setCurrentTransaction(state, transaction) {
        state.transaction = transaction;
    },
    setProducts(state, products) {
        state.products = products;
    },
    setServices(state, services) {
        state.services = services;
    },
    // setSummary(state, summary) {
    //     state.summary = summary;
    // },
    // setServiceSummary(state, serviceSummary) {
    //     state.serviceSummary = serviceSummary;
    // },
    setAddingItemStatus(state, status) {
        state.addingItem = status;
    },
    addProduct(state, product) {
        let products = state.products;
        let _product = products.find(p => p.productTransactionId == product.productTransactionId);
        if(_product) {
            // product is already in the list
            _product.quantity += product.quantity;
            _product.price += product.price;
        } else {
            state.products.push(product);
        }
    },
    addService(state, service) {
        let services = state.services;
        let _service = services.find(s => s.serviceTransactionId == service.serviceTransactionId);
        if(_service) {
            // service is already in the list
            _service.quantity += service.quantity;
            _service.price += service.price;
        } else {
            state.services.push(service);
        }
    },
    removeServiceItem(state, service) {
        state.services = state.services.filter(si => si.serviceTransactionId != service.id);
    },
    removeProductItem(state, product) {
        state.products = state.products.filter(o => o.productTransactionId != product.id);
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
    clearProducts(state) {
        state.products = [];
    },
    clearServices(state) {
        state.services = [];
    },
    clearTransaction(state) {
        state.transaction = null;
        console.log('transaction cleared');
    }
};

const actions = {
    loadOrders(context, data) {
        context.commit('clearErrors');
        context.commit('setLoadingStatus', true);
        return axios.get(`/api/transactions/unpaid/${data.customerId}`, {params: data.query}).then((res, rej) => {
            context.commit('setLoadingStatus', false);
            context.commit('setProducts', res.data.products);
            context.commit('setServices', res.data.services);
            context.commit('setCurrentTransaction', res.data.transaction);
            return res;
        }).catch(err => {
            context.commit('clearProducts');
            context.commit('clearServices');
            context.commit('clearTransaction');
            context.commit('setLoadingStatus', false);
            return Promise.reject(err);
        });
    },
    addProduct(context, data) {
        context.commit('clearErrors');
        context.commit('setAddingItemStatus', true);
        return axios.post(`/api/transactions/${data.transactionId}/add-order`, data.formData).then((res, rej) => {
            context.commit('setAddingItemStatus', false);
            context.commit('addProduct', res.data.productTransaction);
            context.commit('setCurrentTransaction', res.data.transaction);
            return res;
        }).catch(err => {
            context.commit('setAddingItemStatus', false);
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
        });
    },
    addService(context, data) {
        context.commit('clearErrors');
        context.commit('setAddingItemStatus', true);
        return axios.post(`/api/transactions/${data.transactionId}/add-service`, data.formData).then((res, rej) => {
            context.commit('setAddingItemStatus', false);
            context.commit('addService', res.data.serviceTransaction);
            context.commit('setCurrentTransaction', res.data.transaction);
            return res;
        }).catch(err => {
            context.commit('setAddingItemStatus', false);
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
        });
    },
    saveCurrentTransaction(context, data) {
        context.commit('clearErrors');
        context.commit('setLoadingStatus', true);
        return axios.post(`/api/transactions/${data.transactionId}/save-current-transaction`, data.formData).then((res, rej) => {
            context.commit('setLoadingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setLoadingStatus', false);
            return Promise.reject(err);
        });
    },
    removeServiceItem(context, data) {
        return axios.post(`/api/transactions/service-item/${data.serviceItemId}/remove`).then((res, rej) => {
            context.commit('removeServiceItem', res.data.serviceItem);
            if(res.data.cleared) {
                context.commit('clearTransaction');
            }
            return res;
        }).catch(err => {
            return Promise.reject(err);
        });
    },
    removeProductItem(context, data) {
        context.commit('clearErrors');
        return axios.post(`/api/transactions/product-item/${data.productItemId}/remove`).then((res, rej) => {
            context.commit('removeProductItem', res.data.productItem);
            if(res.data.cleared) {
                context.commit('clearTransaction');
            }
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
    },
    isUpdating(state) {
        return state.isUpdating;
    },
    isLoading(state) {
        return state.isLoading;
    },
    getProducts(state) {
        return state.products;
    },
    getServices(state) {
        return state.services;
    },
    getProductSummary(state) {
        if(state.products) {
            return {
                totalPrice: state.products.reduce((sum, item) => sum + parseFloat(item.price), 0),
                totalItems: state.products.reduce((sum, item) => sum + parseInt(item.quantity), 0)
            }
        }
    },
    getServiceSummary(state) {
        if(state.services) {
            return {
                totalPrice: state.services.reduce((sum, item) => sum + parseFloat(item.price), 0),
                totalItems: state.services.reduce((sum, item) => sum + parseInt(item.quantity), 0)
            }
        }
    },
    getCurrentTransaction(state) {
        return state.transaction;
    },
    addingItem(state) {
        return state.addingItem;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
