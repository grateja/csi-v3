import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false,
    isUpdating: false,
    settingMachine: false
};

const mutations = {
    setSavingStatus(state, status) {
        state.isSaving = status;
    },
    setUpdatingStatus(state, status) {
        state.isUpdating = status;
    },
    settingMachineStatus(state, status) {
        state.settingMachine = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    }
};

const actions = {
    insertUser(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post('/api/developer/create-user', data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateUser(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/developer/${data.userId}/update-user`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    setUpClient(context, data) {
        context.commit('setUpdatingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/developer/setup-client`, data.formData).then((res, rej) => {
            context.commit('setUpdatingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setUpdatingStatus', false);
            return Promise.reject(err);
        });
    },
    setUpMachines(context, data) {
        context.commit('settingMachineStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/developer/setup-machines`, data).then((res, rej) => {
            context.commit('settingMachineStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('settingMachineStatus', false);
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
    settingUpMachine(state) {
        return state.settingMachine;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
