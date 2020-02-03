import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isLoading: false,
    claimStubLoading: false
};

const mutations = {
    setLoadingStatus(state, status) {
        state.isLoading = status;
    },
    setLoadingClaimStub(state, status) {
        state.claimStubLoading = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    }
};

const actions = {
    printClaimStub(context, data) {
        context.commit('setLoadingClaimStub', true);
        context.commit('clearErrors');
        return axios.get(`/api/transactions/${data.transactionId}/print-claim-stub`).then((res, rej) => {
            let w = window.open('about:blank', 'print', 'width=800,height=600');

            w.document.write(res.data);
            w.document.close();

            context.commit('setLoadingClaimStub', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setLoadingClaimStub', false);
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
    },
    claimStubLoading(state) {
        return state.claimStubLoading;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
