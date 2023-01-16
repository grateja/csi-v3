import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false,
    isUpdating: false,
    isLoading: false,
    isSelectingBranch: false,
    branches: []
};

const mutations = {
    setBranches(state, branches) {
        state.branches = branches;
    },
    setSavingStatus(state, status) {
        state.isSaving = status;
    },
    setUpdatingStatus(state, status) {
        state.isUpdating = status;
    },
    setLoadingStatus(state, status) {
        state.isLoading = status;
    },
    setSelectingBranchStatus(state, status) {
        state.isSelectingBranch = status;
    },
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    },
    clearBranches(state) {
        state.branches = [];
    }
};

const actions = {
    insertBranch(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/branches/create/${data.clientId}`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateBranch(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/branches/${data.branchId}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    loadBranches(context, data) {
        // only branches assigned to currently logged in user
        if(!context.state.branches.length)
        context.commit('setLoadingStatus', true);
        return axios.get('/api/all/branches/self').then((res, rej) => {
            console.log('branches loaded', res.data.branches);
            context.commit('setBranches', res.data.branches);
            context.commit('setLoadingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setLoadingStatus', false);
            context.commit('setBranches', []);
            return err;
        });
    },
    setDefaultBranch(context, branchId) {
        context.commit('setSelectingBranchStatus', true);
        return axios.post('/api/sys-defaults/set-branch/self', {branchId}).then((res, rej) => {
            context.commit('setSelectingBranchStatus', false);
            context.commit('setDefaultBranch', res.data.activeBranch, {root: true});
            return res;
        }).catch(err => {
            context.commit('setSelectingBranchStatus', false);
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
    getBranches(state) {
        return state.branches;
    },
    isSelectingBranch(state) {
        return state.isSelectingBranch;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
