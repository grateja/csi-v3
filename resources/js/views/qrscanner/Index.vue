<template>
    <div>
        <v-layout>
            <v-flex xs6>
                <div id="reader" width="300px"></div>
            </v-flex>
            <v-flex xs6>
                <v-list>
                    <v-list-tile v-for="i in devices" :key="i.id" @click="start(i)">
                        {{i.label}}
                    </v-list-tile>
                </v-list>
            </v-flex>
        </v-layout>
        <pre>{{QRData}}</pre>
        <v-btn @click="start">scan</v-btn>
    </div>
</template>

<script>
import {Html5Qrcode} from "html5-qrcode"
export default {
    data() {
        return {
            devices: [],
            activeDevice: null,
            QRData: null
        }
    },
    methods: {
        start(id) {
            const html5QrCode = new Html5Qrcode(/* element id */ "reader");
            html5QrCode.start(
            id, 
            {
                fps: 10,    // Optional, frame per seconds for qr code scanning
                qrbox: { width: 250, height: 250 }  // Optional, if you want bounded box UI
            },
            (decodedText, decodedResult) => {
                this.QRData = decodedText
            },
            (errorMessage) => {
                // parse error, ignore it.
            })
            .catch((err) => {
            // Start failed, handle it.
            });
        }
    },
    created() {
        // This method will trigger user permissions
        Html5Qrcode.getCameras().then(devices => {
        /**
         * devices would be an array of objects of type:
         * { id: "id", label: "label" }
         */
        this.devices = devices
            if (devices && devices.length) {
                this.activeDevice = devices[0];
                // .. use this to start scanning.
                console.log(this.cameraId)
            }
        }).catch(err => {
        // handle err
            alert(err)
        });
    }
}
</script>