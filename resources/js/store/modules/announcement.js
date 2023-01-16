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
    insertAnnouncement(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post('/api/announcements/create', data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setSavingStatus', false);
            context.commit('setErrors', err.response.data.errors);

            return Promise.reject(err);
        });
    },
    updateAnnouncement(context, data) {
        context.commit('setSavingStatus', true);
        context.commit('clearErrors');
        return axios.post(`/api/announcements/${data.announcementId}/update`, data.formData).then((res, rej) => {
            context.commit('setSavingStatus', false);
            return res;
        }).catch(err => {
            context.commit('setSavingStatus', false);
            context.commit('setErrors', err.response.data.errors);
            return Promise.reject(err);
        });
    },
    deleteEvent(context, announcementId) {
        return axios.post(`/api/announcements/${announcementId}/delete`).then((res, rej) => {
            return res;
        });
    },
    setDefault(context, announcementId) {
        return axios.post(`/api/announcements/set-default/${announcementId}`);
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
