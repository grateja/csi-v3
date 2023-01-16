// import FormHelper from '../../helpers/FormHelper.js';

const state = {
    // errors: FormHelper,
    serviceTypes: []
};

const mutations = {
    setServiceTypes(state, serviceTypes) {
        state.serviceTypes = serviceTypes;
    },
    setLoadingStatus(state, status) {
        state.isLoading = status;
    },
    clearServiceTypes(state) {
        state.serviceTypes = [];
    }
};

const actions = {
    loadServiceTypes(context, data) {
        // only serviceTypes assigned to currently logged in user
        context.commit('setLoadingStatus', true);
        return axios.get('/api/all/service-types').then((res, rej) => {
            console.log('ServiceTypes loaded', res.data.serviceTypes);
            context.commit('setServiceTypes', res.data.serviceTypes);
            context.commit('setLoadingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setServiceTypes', []);
            context.commit('setLoadingStatus', false);
            return err;
        });
    }
};

const getters = {
    // getErrors(state) {
    //     return state.errors;
    // },
    isLoading(state) {
        return state.isLoading;
    },
    getServiceTypes(state) {
        return state.serviceTypes;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
