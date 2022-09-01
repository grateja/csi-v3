const state = {

};

const mutations = {

};

const actions = {
    deleteVideo(context, videoId) {
        return axios.post(`/api/events/delete-video/${videoId}`).then((res, rej) => {
            return res;
        });
    }
};

const getters = {

};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
