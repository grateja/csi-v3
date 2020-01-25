import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false,
    isUpdating: false
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
    // insertService(context, data) {
    //     context.commit('setSavingStatus', true);
    //     context.commit('clearErrors');
    //     return axios.post('/api/services/other-services/create', data.formData).then((res, rej) => {
    //         context.commit('setSavingStatus', false);
    //         return res;
    //     }).catch(err => {
    //         context.commit('setErrors', err.response.data.errors);
    //         context.commit('setSavingStatus', false);
    //         return Promise.reject(err);
    //     });
    // },
    updateMachineSettings(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/machines/${data.machineId}/update-settings`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    deleteUsage(context, data) {
        context.commit('clearErrors');
        return axios.post(`/api/machine-usages/${data.usageId}/delete-usage`).then((res, rej) => {
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
    isSaving(state) {
        return state.isSaving;
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
