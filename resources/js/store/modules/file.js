const state = {
    videoUploadProgress: 0
};

const mutations = {
    setVideoUploadProgress(state, value) {
        state.videoUploadProgress = value;
    }
};

const actions = {
    uploadSlides(context, request) {
        return axios.post('/api/files/upload-slides/' + request.eventId,
            request.formData,
            {
                headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then((res, rej) => {
            return res;
            // console.log(res.data);
        });
    },
    uploadVideo(context, request) {
        return axios.post('/api/files/upload-video/' + request.eventId,
            request.formData,
            {
                headers: { 'Content-Type': 'multipart/form-data' },
                onUploadProgress: e => {
                    context.commit('setVideoUploadProgress', {
                        percent:Math.round(e.loaded/e.total*100, 2),
                        loaded: e.loaded,
                        total: e.total
                    });
                }
        }).then((res, rej) => {
            return res;
            // console.log(res.data);
        });
    }
};

const getters = {
    getVidUploadProgress(state) {
        return state.videoUploadProgress;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
