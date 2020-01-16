import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
};

const mutations = {
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
    }
};

const getters = {
    getErrors(state) {
        return state.errors;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
