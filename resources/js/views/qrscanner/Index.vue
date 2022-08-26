<template>
    <div>
        <v-layout>
            <v-card class="rounded-card" width="100px" @click="scan('camera')">
                <v-icon>camera</v-icon>
            </v-card>
            <v-card class="rounded-card" width="100px" @click="scan('file')">
                <v-icon>file</v-icon>
            </v-card>
        </v-layout>
        <pre>{{QRData}}</pre>
        <!-- <pre>{{errorMessage}}</pre> -->
        <!-- <pre>{{html5QrCode}}</pre> -->
        <!-- <v-btn @click="start">scan</v-btn> -->
        <!-- <input type="file" name="inputFile" id="inputFile" ref="inputFile" @change="setPicture" accept="image/*"> -->
        <scanner-dialog v-model="showScannerDialog" @success="decode" :mode="mode" />
    </div>
</template>

<script>

// import {Html5Qrcode} from "html5-qrcode"
import ScannerDialog from './ScannerDialog.vue';
export default {
    components: {
        ScannerDialog
    },
    data() {
        return {
            // devices: [],
            // activeDevice: null,
            QRData: null,
            // file: null,
            // errorMessage: null,
            // html5QrCode: null,
            mode: null,
            showScannerDialog: false
        }
    },
    methods: {
        scan(mode) {
            this.mode = mode;
            this.showScannerDialog = true;
        },
        decode(data) {
            this.QRData = data;
        },
        // browsePicture() {
        //     this.$refs.inputFile.click();
        // },
        // setPicture(e) {
        //     if(e.target.files.length == 1) {
        //         this.file = e.target.files[0];
        //     }
        // },
        // // start(id) {
        //     if(this.html5QrCode == null) {
        //         this.html5QrCode = new Html5Qrcode("reader");
        //     }
        //     if(this.html5QrCode.isScanning) {
        //         this.html5QrCode.stop();
        //     }
        //     this.html5QrCode.start(
        //     id, 
        //     {
        //         fps: 10,    // Optional, frame per seconds for qr code scanning
        //         qrbox: { width: 250, height: 250 }  // Optional, if you want bounded box UI
        //     },
        //     (decodedText, decodedResult) => {
        //         this.QRData = decodedText
        //     },
        //     (errorMessage) => {
        //         // parse error, ignore it.
        //     })
        //     .catch((err) => {
        //         this.errorMessage = err
        //     // Start failed, handle it.
        //     });
        // }
    },
    created() {
        // // This method will trigger user permissions
        // Html5Qrcode.getCameras().then(devices => {
        //     /**
        //      * devices would be an array of objects of type:
        //      * { id: "id", label: "label" }
        //      */
        //     this.devices = devices
        //     if (devices && devices.length) {
        //         this.activeDevice = devices[0];
        //         // .. use this to start scanning.
        //         console.log(this.cameraId)
        //     }
        // }).catch(err => {
        //     // handle err
        //     alert(err)
        // });
        // setTimeout(() => {
        //     this.html5QrCode = new Html5Qrcode("reader");
        // }, 500);
    },
    beforeDestroy() {
        // this.html5QrCode.stop();
    }
}
</script>