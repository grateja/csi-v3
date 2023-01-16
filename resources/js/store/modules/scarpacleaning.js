import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    isSaving: false
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
    insertService(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post('/api/services/scarpa-cleanings/create', data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateService(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/services/scarpa-cleanings/${data.serviceId}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    setPicture(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        console.log(data)
        return axios.post(`/api/services/scarpa-cleanings/${data.serviceId}/set-picture`,
            data.formData, {
                headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    removePicture(context, serviceId) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/services/scarpa-cleanings/${serviceId}/remove-picture`).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    deleteService(context, serviceId) {
        return axios.post(`/api/services/scarpa-cleanings/${serviceId}/delete-service`).then((res, rej) => {
            return res;
        }).catch(err => {
            return Promise.reject(err);
        });
    },
    insertVariation(context, data) {
        context.commit('clearErrors');
        return axios.post(`/api/services/scarpa-cleanings/variations/${data.serviceId}/add`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    updateVariation(context, data) {
        context.commit('clearErrors');
        return axios.post(`/api/services/scarpa-cleanings/variations/${data.serviceId}/${data.variationId}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setErrors', err.response.data.errors);
            context.commit('setSavingStatus', false);
            return Promise.reject(err);
        });
    },
    deleteVariation(context, scarpaVariationId) {
        return axios.post(`/api/services/scarpa-cleanings/variations/${scarpaVariationId}/delete`);
    }
};

const getters = {
    getErrors(state) {
        return state.errors;
    },
    isSaving(state) {
        return state.isSaving;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
