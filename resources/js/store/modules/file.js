const state = {
    videoUploadProgress: 0,
    audioUploadProgress: 0,
    uploading: false
};

const mutations = {
    setVideoUploadProgress(state, value) {
        state.videoUploadProgress = value;
    },
    setAudioUploadProgress(state, value) {
        state.audioUploadProgress = value;
    },
    setUploadingState(state, value) {
        state.uploading = value;
    },
};

const actions = {
    uploadSlides(context, request) {
        return axios.post('/api/files/upload-slides/' + request.eventId,
            request.formData,
            // {
            //     headers: {
            //     'Content-Type': 'multipart/form-data'
            // }
            // }
        ).then((res, rej) => {
            return res;
            // console.log(res.data);
        });
    },
    uploadVideo(context, request) {
        context.commit('setUploadingState', true);
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
        }).finally(() => {
            context.commit('setUploadingState', false);
        });
    },
    changePicture(context, request) {
        return axios.post('/api/files/change-picture/' + request.slideId,
            request.formData,
            // {
            //     headers: {
            //     'Content-Type': 'multipart/form-data'
            // }
            // }
        ).then((res, rej) => {
            return res;
            // console.log(res.data);
        });
    },
    uploadAudio(context, request) {
        context.commit('setUploadingState', true);
        return axios.post('/api/files/upload-audio/' + request.eventId,
            request.formData,
            {
                headers: { 'Content-Type': 'multipart/form-data' },
                onUploadProgress: e => {
                    context.commit('setAudioUploadProgress', {
                        percent:Math.round(e.loaded/e.total*100, 2),
                        loaded: e.loaded,
                        total: e.total
                    });
                }
        }).then((res, rej) => {
            return res;
            // console.log(res.data);
        }).finally(() => {
            context.commit('setUploadingState', false);
        });
    }
};

const getters = {
    getVidUploadProgress(state) {
        return state.videoUploadProgress;
    },
    getUploadingState(state) {
        return state.uploading;
    }
};

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
