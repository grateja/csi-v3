import FormHelper from '../../helpers/FormHelper.js';

const state = {
    errors: FormHelper,
    saving: false
};

const mutations = {
    setErrors(state, errors) {
        state.errors.errors = errors;
    },
    clearErrors(state, key) {
        state.errors.clear(key);
    },
    setSavingStatus(state, status) {
        state.saving = status;
    }
};

const actions = {
    insertEvent(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post('/api/events/create', data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setSavingStatus', false);
            context.commit('setErrors', err.response.data.errors);

            return Promise.reject(err);
        });
    },
    updateEvent(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/events/${data.eventId}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setSavingStatus', false);
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
        });
    },
    deleteEvent(context, eventId) {
        return axios.post(`/api/events/${eventId}/delete`).then((res, rej) => {
            return res;
        });
    },
    removeSlide(context, slideId) {
        return axios.post(`/api/events/remove-slide/${slideId}`);
    },
    setDefault(context, eventId) {
        return axios.post(`/api/events/set-default/${eventId}`);
    }
};

const getters = {
    getSavingStatus(state) {
        return state.saving;
    },
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
