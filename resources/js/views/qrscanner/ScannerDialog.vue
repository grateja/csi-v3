<template>
    <v-dialog width="600px" :value="value" persistent>
        <v-card class="rounded-card">
            <v-card-title>
                <span class="title">Scan using camera</span>
                <v-spacer></v-spacer>
                <v-btn icon @click="close">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text class="text-xs-center">
                <div id="reader"></div>
                <p class="caption red--text" v-if="errorMessage && mode == 'camera' && !granted">{{errorMessage}}</p>
                <v-btn v-if="!granted && !loading && mode == 'camera'" @click="requestPermission">Enable camera permission</v-btn>
                <v-btn v-if="mode == 'file'" @click="browsePicture">Browse from device</v-btn>
                <v-progress-circular indeterminate v-if="loading" />
            </v-card-text>
            <v-card-text v-if="mode == 'camera'">
                <v-btn v-for="i in devices" :key="i.id" @click="start(i.id)" :class="{active: activeDevice!=null && i.id == activeDevice.id}">
                    {{i.label}}
                </v-btn>
            </v-card-text>
        </v-card>
        <input type="file" name="inputFile" id="inputFile" ref="inputFile" @change="setPicture" accept="image/*">
    </v-dialog>
</template>
<script>
import {Html5Qrcode} from "html5-qrcode"
export default {
    props: ['value', 'mode'],
    data() {
        return {
            devices: [],
            QRData: null,
            html5QrCode: null,
            activeDevice: null,
            loading: false,
            errorMessage: null,
            granted: false
        }
    },
    methods: {
        start(cameraId) {
            // if(this.html5QrCode == null) {
            //     this.html5QrCode = new Html5Qrcode("reader");
            // }
            if(this.html5QrCode.isScanning) {
                this.html5QrCode.stop();
            }
            this.html5QrCode.start(
            cameraId, 
            {
                fps: 10,    // Optional, frame per seconds for qr code scanning
                qrbox: { width: 250, height: 250 }  // Optional, if you want bounded box UI
            },
            (decodedText, decodedResult) => {
                this.$emit('success', decodedText)
                this.close();
            },
            (errorMessage) => {
                // parse error, ignore it.
            })
            .catch((err) => {
                this.errorMessage = err
            // Start failed, handle it.
            });
        },
        browsePicture() {
            this.$refs.inputFile.value = null;
            this.$refs.inputFile.click();
        },
        setPicture(e) {
            if (e.target.files.length == 0) {
                // No file selected, ignore 
                return;
            }

            // const imageFile = e.target.files[0];
            this.html5QrCode.scanFile(e.target.files[0], true)
                .then(decodedText => {
                    // success, use decodedText
                    console.log(decodedText);
                    this.$emit('success', decodedText)
                    this.close();
                })
                .catch(err => {
                    // failure, handle it.
                    alert("Invalid QR Code")
                    this.$refs.inputFile.value = null;
                    this.html5QrCode.clear()
                    console.log(`Error scanning file. Reason: ${err}`)
                });
        },
        loadCameras() {
            // if(this.html5QrCode == null) {
            //     this.html5QrCode = new Html5Qrcode("reader");
            // }
            if(this.devices.length > 0) return;
            this.loading = true;
            Html5Qrcode.getCameras().then(devices => {
                /**
                 * devices would be an array of objects of type:
                 * { id: "id", label: "label" }
                 */
                this.devices = devices
                if (devices && devices.length) {
                    this.activeDevice = devices[0];
                }
            }).catch(err => {
                // handle err
                // alert("Error loading cameras " + err)
                this.errorMessage = err.message;
            }).finally(() => {
                this.loading = false;
            });
        },
        stop() {
            if(this.html5QrCode != null && this.html5QrCode.isScanning) {
                this.html5QrCode.stop();
                console.log('stop camera')
            }
        },
        close() {
            this.$emit('input', false);
        },
        chekcPermission() {
            navigator.permissions.query({ name: "camera" }).then(res => {
                if(res.state == "granted"){
                    this.granted = true;
                    this.loadCameras();
                    this.errorMessage = "";
                } else {
                    this.errorMessage = "Camera permission not granted"
                    console.log("state", res.state)
                }
            });
        },
        requestPermission() {
            this.loading = true;
            navigator.mediaDevices.getUserMedia({
                video: true
            }).then((stream) => {
                this.granted = true
                this.errorMessage = "";
                stream.getVideoTracks().forEach(track => {
                    track.stop();
                    this.loadCameras();
                });
            }).catch((er) => {
                this.errorMessage = "Camera permission not granted"
            }).finally(() => {
                this.stop();
                console.log('permission requested')
                this.loading = false;
            });
        }
    },
    created() {
    },
    watch: {
        value(val) {
            if(val) {
                if(this.html5QrCode == null) {
                    this.html5QrCode = new Html5Qrcode("reader");
                }
                if(this.mode == 'camera') {
                    this.chekcPermission();
                } else {
                    this.browsePicture();
                }
                if(this.html5QrCode != null) {
                    this.html5QrCode.clear();
                }
            } else {
                this.stop();
                this.loading = false;
                this.activeDevice = null;
                this.devices = [];
                this.QRData = null;
            }
        },
        mode(val) {
            if(val == 'camera') {
                // this.loadCameras();
            } else {

            }
        },
        activeDevice(val) {
            if(val != null) {
                setTimeout(() => {
                    this.start(val.id);
                    this.loading = false;
                }, 1000);
            }
        }
    },
    beforeDestroy() {
        this.stop();
    }
}
</script>