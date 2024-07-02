import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false,
    isLoading: false,
    isAddingItem: false,
    currentCustomer: null,
    currentTransaction: null,
    customerRemarks: null
};

const mutations = {
    setCurrentCustomer(state, customer) {
        state.currentCustomer = customer;
    },
    setCurrentTransaction(state, transaction) {
        state.currentTransaction = transaction;
    },
    setAddingItemStatus(state, status) {
        state.isAddingItem = status;
    },
    removeCustomer(state) {
        state.currentCustomer = null;
        state.currentTransaction = null;
    },
    setLoadingStatus(state, status) {
        state.isLoading = status;
    },
    setSavingStatus(state, status) {
        state.isSaving = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    },
    clearTransaction(state) {
        state.currentTransaction = null;
    },
    setCustomerRemarks(state, remarks) {
        state.customerRemarks = remarks;
    }
};

const actions = {
    checkCustomer(context, customer) {
        context.commit('clearErrors');
        context.commit('setLoadingStatus', true);
        return axios.get(`/api/pos-transactions/current-transaction/${customer.id}`).then((res, rej) => {
            context.commit('setLoadingStatus', false);
            context.commit('setCurrentCustomer', customer);
            context.commit('clearTransaction');
            context.commit('setCurrentTransaction', res.data.transaction);
            context.commit('setCustomerRemarks', res.data.customerRemarks);
            console.log("customer remarks", res.data.customerRemarks)
            return res;
        }).catch(err => {
            context.commit('clearTransaction');
            context.commit('setLoadingStatus', false);
            return Promise.reject(err);
        });
    },
    refreshTransaction(context) {
        if(context.getters.getCurrentTransaction) {
            context.commit('setLoadingStatus', true);
            return axios.get(`/api/transactions/${context.getters.getCurrentTransaction.id}`).then((res, rej) => {
                context.commit('setCurrentTransaction', res.data.transaction);
            }).finally(() => {
                context.commit('setLoadingStatus', false);
            });
        }
    },
    addService(context, data) {
        if(context.getters.isBusy) {
            alert('Please wait...');
            return;
        }

        context.commit('setAddingItemStatus', true);
        return axios.post(`/api/pos-transactions/add-service/${data.category}`, {
            customerId: data.customerId,
            transactionId: data.transactionId,
            itemId: data.itemId
        }).then((res, rej) => {
            context.commit('setCurrentTransaction', res.data.transaction);
            context.commit('setAddingItemStatus', false);
            return res;
        }).catch(err => {
            context.commit('setAddingItemStatus', false);
            return Promise.reject(err);
        });
    },
    addProduct(context, data) {
        if(context.getters.isBusy) {
            alert('Please wait...');
            return;
        }

        context.commit('setAddingItemStatus', true);
        return axios.post(`/api/pos-transactions/add-product`, {
            customerId: data.customerId,
            transactionId: data.transactionId,
            itemId: data.itemId
        }).then((res, rej) => {
            context.commit('setCurrentTransaction', res.data.transaction);
            context.commit('setAddingItemStatus', false);
            return res;
        }).catch(err => {
            context.commit('setAddingItemStatus', false);
            return Promise.reject(err);
        });
    },
    reduceProduct(context, data) {
        return axios.post(`/api/pos-transactions/reduce-products`, data).then((res, rej) => {

        });
    },
    reduceShoeCleaning(context, data) {
        return axios.post(`/api/pos-transactions/scarpa-cleanings/reduce-scarpa-cleaning`, data).then((res, rej) => {

        });
    },
    reduceLagoon(context, data) {
        return axios.post(`/api/pos-transactions/reduce-lagoon`, data).then((res, rej) => {

        });
    },
    reduceLagoonPerKilo(context, data) {
        return axios.post(`/api/pos-transactions/reduce-lagoon-per-kilo`, data).then((res, rej) => {

        });
    },
    addShoeCleaning(context, data) {
        if(context.getters.isBusy) {
            alert('Please wait...');
            return;
        }
        context.commit('setAddingItemStatus', true);
        return axios.post(`/api/pos-transactions/scarpa-cleanings/${data.variationId}/add`, {
            customerId: data.customerId,
            transactionId: data.transactionId
        }).then((res, rej) => {
            context.commit('setCurrentTransaction', res.data.transaction);
            context.commit('setAddingItemStatus', false);
            return res;
        }).catch(err => {
            context.commit('setAddingItemStatus', false);
            return Promise.reject(err);
        });
    },
    addLagoon(context, data) {
        if(context.getters.isBusy) {
            alert('Please wait...');
            return;
        }

        context.commit('setAddingItemStatus', true);
        return axios.post(`/api/pos-transactions/add-lagoon`, {
            customerId: data.customerId,
            transactionId: data.transactionId,
            itemId: data.itemId
        }).then((res, rej) => {
            context.commit('setCurrentTransaction', res.data.transaction);
            context.commit('setAddingItemStatus', false);
            return res;
        }).catch(err => {
            context.commit('setAddingItemStatus', false);
            return Promise.reject(err);
        });
    },
    addLagoonPerKilo(context, data) {
        if(context.getters.isBusy) {
            alert('Please wait...');
            return;
        }

        context.commit('setAddingItemStatus', true);
        return axios.post(`/api/pos-transactions/add-lagoon-per-kilo`, {
            customerId: data.customerId,
            transactionId: data.transactionId,
            itemId: data.itemId,
            kg: data.kg
        }).then((res, rej) => {
            context.commit('setCurrentTransaction', res.data.transaction);
            context.commit('setAddingItemStatus', false);
            return res;
        }).catch(err => {
            context.commit('setAddingItemStatus', false);
            return Promise.reject(err);
        });
    },
    addEluxService(context, data) {
        if(context.getters.isBusy) {
            alert('Please wait...');
            return;
        }
        context.commit('setAddingItemStatus', true);
        return axios.post(`/api/pos-transactions/add-elux-service`, {
            customerId: data.customerId,
            transactionId: data.transactionId,
            itemId: data.itemId,
        }).then((res, rej) => {
            context.commit('setCurrentTransaction', res.data.transaction);
            context.commit('setAddingItemStatus', false);
            return res;
        }).catch(err => {
            context.commit('setAddingItemStatus', false);
            return Promise.reject(err);
        });
    },

    saveTransaction(context, transactionId) {
        context.commit('clearErrors');
        context.commit('setSavingStatus', true);
        return axios.post(`/api/pos-transactions/save-transaction/${transactionId}`).then((res, rej) => {
            context.commit('setSavingStatus', false);
            context.commit('setCurrentTransaction', res.data.transaction);
            return res;
        }).catch(err => {
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    cancelTransaction(context, transactionId) {
        return axios.post(`/api/pos-transactions/${transactionId}/cancel-transaction`);
    },
    voidTransaction(context, data) {
        return axios.post(`/api/pos-transactions/${data.transactionId}/void-transaction`, {
            remarks: data.remarks
        });
    }
};

const getters = {
    getErrors(state) {
        return state.errors;
    },
    getCurrentCustomer(state) {
        return state.currentCustomer;
    },
    getCurrentTransaction(state) {
        return state.currentTransaction;
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
    isBusy(state) {
        return state.isAddingItem && state.currentTransaction == null;
    },
    getCustomerRemarks(state) {
        return state.customerRemarks
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
