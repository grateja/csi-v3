import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isLoading: false,
};

const mutations = {
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    },
    setLoadingStatus(state, status) {
        state.isLoading = status;
    }
};

const actions = {
    deleteServiceItem(context, serviceTransactionItemId) {
        return axios.post(`/api/transactions/service-items/${serviceTransactionItemId}/delete`).then((res, rej) => {
            context.dispatch('postransaction/refreshTransaction', null, {root: true});
            return res;
        }).catch(err => {
            return Promise.reject(err);
        });
    },
    disposeService(context, data) {
        return axios.post(`/api/pending-services/${data.serviceType}/${data.serviceId}/dispose-service`);
    },
    deleteTransaction(context, transactionId) {
        return axios.post(`/api/transactions/${transactionId}/delete-transaction`);
    },
    addRemarks(context, data) {
        context.commit('clearErrors');
        return axios.post(`/api/transaction-remarks/${data.transactionId}/add-remarks`, data.formData).then((res, rej) => {
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
        });
    },
    deleteRemarks(context, remarksId) {
        context.commit('clearErrors');
        return axios.post(`/api/transaction-remarks/${remarksId}/delete-remarks`).then((res, rej) => {
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
