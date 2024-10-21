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
    createJobOrder(context, data) {
        return axios.post(`/api/out-source/job-orders/create`, data.formData).then((res, rej) => {
            return res;
        }).catch(err => {
            return Promise.reject(err);
        });
    },
    deleteJobOrder(context, jobOrderId) {
        return axios.post(`/api/out-source/job-orders/${jobOrderId}/delete`).then((res, rej) => {
            return res;
        }).catch(err => {
            return Promise.reject(err);
        });
    },
    insertLinen(context, data) {
        return axios.post(`/api/out-source/job-orders/${data.jobOrderId}/add-item`, data.formData).then((res, rej) => {
            return res;
        }).catch(err => {
            return Promise.reject(err);
        });
    },
    updateLinen(context, data) {
        return axios.post(`/api/out-source/job-orders/${data.jobOrderLinenId}/update-item`, data.formData).then((res, rej) => {
            return res;
        }).catch(err => {
            return Promise.reject(err);
        });
    },
    deleteLinen(context, jobOrderLinenId) {
        return axios.post(`/api/out-source/job-orders/${jobOrderLinenId}/remove-item`).then((res, rej) => {
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
