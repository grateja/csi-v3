import Vuex from 'vuex';
import Vue from 'vue';
Vue.use(Vuex);

import auth from './modules/auth.js';
import client from './modules/client.js';
import branch from './modules/branch.js';
import account from './modules/account.js';
import user from './modules/user.js';
import product from './modules/product.js';
import service from './modules/service.js';
import customer from './modules/customer.js';
import transaction from './modules/transaction.js';
import servicetype from './modules/servicetype.js';
import payment from './modules/payment.js';
import remote from './modules/remote.js';
import rfidcard from './modules/rfidcard.js';
import cardtransaction from './modules/cardtransaction.js';
import expense from './modules/expense.js';
import discount from './modules/discount.js';
import point from './modules/point.js';
import voidtransaction from './modules/voidtransaction.js';
import joborder from './modules/joborder.js';
import exportdownload from './modules/exportdownload.js';
import printer from './modules/printer.js';
import serviceprice from './modules/serviceprice.js';
import washingservice from './modules/washingservice.js';
import dryingservice from './modules/dryingservice.js';
import perkiloservices from './modules/perkiloservices.js';
import otherservice from './modules/otherservice.js';
import scarpacleaning from './modules/scarpacleaning.js';
import lagoon from './modules/lagoon.js'
import lagoonperkilo from './modules/lagoonperkilo.js'
import fullservice from './modules/fullservice.js';
import postransaction from './modules/postransaction.js';
import fullserviceitem from './modules/fullserviceitem.js';
import fullserviceproduct from './modules/fullserviceproduct.js';
import productpurchase from './modules/productpurchase.js';
import machine from './modules/machine.js';
import transactionreport from './modules/transactionreport.js';
import lagoonpartner from './modules/lagoonpartner.js';

import storehour from './modules/storehour.js';
import event from './modules/event.js';
import announcement from './modules/announcement.js';
import video from './modules/video.js';
import file from './modules/file.js';
import qrscanner from './modules/qrscanner.js';
import qrtransaction from './modules/qrtransaction.js';

export default new  Vuex.Store({
    state: {
        currentUser: null,
        flashMessage: null,
        machineActivationMethod: null,
        dopuSetup: null,
        dopuIncludeServices: false,
        allowRework: false,
        allowTransfer: false,
        scarpaOnly: false
    },
    getters: {
        getCurrentUser(state) {
            return state.currentUser;
        },
        getFlashMessage(state) {
            return state.flashMessage;
        },
        isDeveloper(state) {
            if(state.currentUser) {
                return state.currentUser.roles[0] == 'developer';
            }
        },
        getMachineActivationMethod(state) {
            return state.machineActivationMethod;
        //    return localStorage.getItem('machineActivationMethod');
        },
        getScarpaOnly(state) {
            return state.scarpaOnly
        },
        getDopuSetup(state) {
            return state.dopuSetup
        },
        getDopuIncludeServices(state) {
            return state.dopuIncludeServices
        },
        getCanDownloadCustomers(state) {
            return state.canDownloadCustomers
        },
        getShopId(state) {
            return state.shopId;
        },
        isTransferAllowed(state) {
            return state.allowTransfer;
        },
        isReworkAllowed(state) {
            return state.allowRework;
        }
    },
    actions: {
        setAuth(context, data) {
            context.commit('setUser', data);
            if(data.retainToken) {
                localStorage.setItem('token', data.token.accessToken);
            } else {
                localStorage.removeItem('token');
            }
        }
    },
    mutations: {
        setUser(state, data) {
            // console.log('set user asfsdf sf a', data.user)
            state.currentUser = data.user;
            state.machineActivationMethod = data.machineActivationMethod;
            state.dopuSetup = data.dopuSetup;
            state.dopuIncludeServices = data.dopuIncludeServices;
            state.canDownloadCustomers = data.canDownloadCustomers;
            state.shopId = data.shopId;
            state.allowRework = data.allowRework;
            state.allowTransfer = data.allowTransfer;
            state.scarpaOnly = data.scarpaOnly;
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${data.token.accessToken}`;
        },
        updateEmail(state, data) {
            state.currentUser.email = data.email;
        },
        clearUser(state) {
            state.currentUser = null;
        },
        clearToken(state) {
            window.axios.defaults.headers.common['Authorization'] = null;
            localStorage.removeItem('token');
        },
        updateName(state, user) {
            state.currentUser.fullname = user.fullname;
        },
        setFlash(state, config) {
            state.flashMessage = config;
        }
    },
    modules: {
        auth,
        client,
        branch,
        account,
        user,
        product,
        service,
        customer,
        transaction,
        servicetype,
        payment,
        remote,
        rfidcard,
        cardtransaction,
        expense,
        discount,
        point,
        voidtransaction,
        joborder,
        exportdownload,
        printer,
        serviceprice,
        washingservice,
        dryingservice,
        perkiloservices,
        otherservice,
        scarpacleaning,
        lagoon,
        lagoonperkilo,
        postransaction,
        fullservice,
        fullserviceitem,
        fullserviceproduct,
        productpurchase,
        machine,
        storehour,
        transactionreport,
        lagoonpartner,
        event,
        announcement,
        video,
        file,
        qrscanner,
        qrtransaction
    }
});
