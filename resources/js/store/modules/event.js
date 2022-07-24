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
    createEvent(context, request) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post('/api/events/create', request).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setSavingStatus', false);
            context.commit('setErrors', err.response.data.errors);

            return Promise.reject(err);
        });
    },
    updateEvent(context, request) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/events/${request.id}/update`, request).then((res, rej) => {
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
