<template>
    <v-container>
        <h3 class="title white--text">Scan QR code</h3>
        <v-divider class="my-3"></v-divider>
        <!-- <pre>{{isIncludeServices}}</pre> -->
        <!-- <pre>{{QRData}}</pre> -->
        <v-card v-if="QRData != null && !!QRData.cust" class="rounded-card">
            <v-layout>
                <v-flex xs5 class="text-xs-right mr-3">Job Order:</v-flex>
                <v-flex xs7 :class="{'red--text' : !!joConflict}">{{QRData.jo}}</v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs5 class="text-xs-right mr-3">Date:</v-flex>
                <v-flex xs7>{{moment().format('MMM D, yyyy')}}</v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs5 class="text-xs-right mr-3">Customer name:</v-flex>
                <v-flex xs7>{{QRData.cust.nam}}</v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs5 class="text-xs-right mr-3">CRN:</v-flex>
                <v-flex xs7>{{QRData.cust.crn}}</v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs5 class="text-xs-right mr-3">Address:</v-flex>
                <v-flex xs7>{{QRData.cust.adr}}</v-flex>
            </v-layout>
            <v-layout>
                <v-flex xs5 class="text-xs-right mr-3">Contact#:</v-flex>
                <v-flex xs7>{{QRData.cust.cn}}</v-flex>
            </v-layout>


            <v-card class="ma-1" v-if="QRData.sv && QRData.sv.length" flat>
                <v-card-title class="pa-0 teal white--text">
                    <VSpacer/>
                    <h4>SCARPA</h4>
                    <VSpacer/>
                </v-card-title>
                <v-layout>
                    <v-flex xs4>
                        <div class="pa-1 caption grey--text font-weight-bold">Name</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Unit Price</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-center">QTY</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Total Price</div>
                    </v-flex>
                </v-layout>
                <!-- <v-divider></v-divider> -->
                <template v-for="(item, i) in QRData.sv">
                    <v-layout :key="i + 'scarpa-cleaning'" class="px-1">
                        <v-flex xs4>
                            <div>
                                <!-- <v-btn icon small class="ma-0 red" outline @click="reduceShoeCleaning(item)" :loading="item.reducing">
                                    <v-icon small class="red--text">remove</v-icon>
                                </v-btn> -->
                                {{item.nam}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.up ? 'P ' + parseFloat(item.up).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                        <v-flex xs2>
                            <div class="text-xs-center">{{item.qty}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.up ? 'P ' + parseFloat(item.up * item.qty).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                    </v-layout>
                    <!-- <v-divider :key="i + '-div-scarpa-cleaning'"></v-divider> -->
                </template>
                <!-- <v-divider class="black"></v-divider> -->
                <!-- <v-layout class="pa-1 font-weight-bold" >
                    <v-flex xs4>
                        <div>Total</div>
                    </v-flex>
                    <v-flex xs3>
                    </v-flex>
                    <v-flex xs2>
                        <div class="text-xs-center">{{currentTransaction.posScarpaCleaningSummary.total_quantity}}</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="text-xs-right">P {{parseFloat(currentTransaction.posScarpaCleaningSummary.total_price).toFixed(2)}}</div>
                    </v-flex>
                </v-layout> -->
            </v-card>





            <v-card class="ma-1" v-if="QRData.lag && QRData.lag.length" flat>
                <v-card-title class="pa-0 teal white--text">
                    <VSpacer/>
                    <h4>LAGOON</h4>
                    <VSpacer/>
                </v-card-title>
                <v-layout>
                    <v-flex xs4>
                        <div class="pa-1 caption grey--text font-weight-bold">Name</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Unit Price</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-center">QTY</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Total Price</div>
                    </v-flex>
                </v-layout>
                <!-- <v-divider></v-divider> -->
                <template v-for="(item, i) in QRData.lag">
                    <v-layout :key="i + 'scarpa-cleaning'" class="px-1">
                        <v-flex xs4>
                            <div>
                                <!-- <v-btn icon small class="ma-0 red" outline @click="reduceShoeCleaning(item)" :loading="item.reducing">
                                    <v-icon small class="red--text">remove</v-icon>
                                </v-btn> -->
                                {{item.nam}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.up ? 'P ' + parseFloat(item.up).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                        <v-flex xs2>
                            <div class="text-xs-center">{{item.qty}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.up ? 'P ' + parseFloat(item.up * item.qty).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                    </v-layout>
                    <v-divider :key="i + '-div-scarpa-cleaning'"></v-divider>
                </template>
                <!-- <v-divider class="black"></v-divider> -->
                <!-- <v-layout class="pa-1 font-weight-bold" >
                    <v-flex xs4>
                        <div>Total</div>
                    </v-flex>
                    <v-flex xs3>
                    </v-flex>
                    <v-flex xs2>
                        <div class="text-xs-center">{{currentTransaction.posScarpaCleaningSummary.total_quantity}}</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="text-xs-right">P {{parseFloat(currentTransaction.posScarpaCleaningSummary.total_price).toFixed(2)}}</div>
                    </v-flex>
                </v-layout> -->
            </v-card>




            <v-card class="ma-1" v-if="QRData.lpk && QRData.lpk.length" flat>
                <v-card-title class="pa-0 teal white--text">
                    <VSpacer/>
                    <h4>LAGOON PER KILO</h4>
                    <VSpacer/>
                </v-card-title>
                <v-layout>
                    <v-flex xs4>
                        <div class="pa-1 caption grey--text font-weight-bold">Name</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Price per Kilo</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-center">Kilos</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Total Price</div>
                    </v-flex>
                </v-layout>
                <!-- <v-divider></v-divider> -->
                <template v-for="(item, i) in QRData.lpk">
                    <v-layout :key="i + 'scarpa-cleaning'" class="px-1">
                        <v-flex xs4>
                            <div>
                                <!-- <v-btn icon small class="ma-0 red" outline @click="reduceShoeCleaning(item)" :loading="item.reducing">
                                    <v-icon small class="red--text">remove</v-icon>
                                </v-btn> -->
                                {{item.nam}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.up ? 'P ' + parseFloat(item.up).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                        <v-flex xs2>
                            <div class="text-xs-center">{{item.qty}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.up ? 'P ' + parseFloat(item.up * item.qty).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                    </v-layout>
                    <v-divider :key="i + '-div-scarpa-cleaning'"></v-divider>
                </template>
                <!-- <v-divider class="black"></v-divider> -->
                <!-- <v-layout class="pa-1 font-weight-bold" >
                    <v-flex xs4>
                        <div>Total</div>
                    </v-flex>
                    <v-flex xs3>
                    </v-flex>
                    <v-flex xs2>
                        <div class="text-xs-center">{{currentTransaction.posScarpaCleaningSummary.total_quantity}}</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="text-xs-right">P {{parseFloat(currentTransaction.posScarpaCleaningSummary.total_price).toFixed(2)}}</div>
                    </v-flex>
                </v-layout> -->
            </v-card>



            <v-card class="ma-1" v-if="QRData.services && QRData.services.washing" flat>
                <v-card-title class="pa-0 teal white--text">
                    <VSpacer/>
                    <h4>WASHING SERVICES</h4>
                    <VSpacer/>
                </v-card-title>
                <v-layout>
                    <v-flex xs4>
                        <div class="pa-1 caption grey--text font-weight-bold">Name</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Unit Price</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-center">Quantity</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Total Price</div>
                    </v-flex>
                </v-layout>
                <template v-for="(item, i) in QRData.services.washing">
                    <v-layout :key="i + 'scarpa-cleaning'" class="px-1">
                        <v-flex xs4>
                            <div>
                                {{item.name}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                        <v-flex xs2>
                            <div class="text-xs-center">{{item.quantity}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price * item.quantity).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                    </v-layout>
                    <v-divider :key="i + '-div-scarpa-cleaning'"></v-divider>
                </template>
            </v-card>

            <v-card class="ma-1" v-if="QRData.services && QRData.services.drying" flat>
                <v-card-title class="pa-0 teal white--text">
                    <VSpacer/>
                    <h4>DRYING SERVICES</h4>
                    <VSpacer/>
                </v-card-title>
                <v-layout>
                    <v-flex xs4>
                        <div class="pa-1 caption grey--text font-weight-bold">Name</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Unit Price</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-center">Quantity</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Total Price</div>
                    </v-flex>
                </v-layout>
                <template v-for="(item, i) in QRData.services.drying">
                    <v-layout :key="i + 'scarpa-cleaning'" class="px-1">
                        <v-flex xs4>
                            <div>
                                {{item.name}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                        <v-flex xs2>
                            <div class="text-xs-center">{{item.quantity}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price * item.quantity).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                    </v-layout>
                    <v-divider :key="i + '-div-scarpa-cleaning'"></v-divider>
                </template>
            </v-card>
            <v-card class="ma-1" v-if="QRData.services && QRData.services.other" flat>
                <v-card-title class="pa-0 teal white--text">
                    <VSpacer/>
                    <h4>OTHER SERVICES</h4>
                    <VSpacer/>
                </v-card-title>
                <v-layout>
                    <v-flex xs4>
                        <div class="pa-1 caption grey--text font-weight-bold">Name</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Unit Price</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-center">Quantity</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Total Price</div>
                    </v-flex>
                </v-layout>
                <template v-for="(item, i) in QRData.services.other">
                    <v-layout :key="i + 'scarpa-cleaning'" class="px-1">
                        <v-flex xs4>
                            <div>
                                {{item.name}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                        <v-flex xs2>
                            <div class="text-xs-center">{{item.quantity}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price * item.quantity).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                    </v-layout>
                    <v-divider :key="i + '-div-scarpa-cleaning'"></v-divider>
                </template>
            </v-card>



            <v-card class="ma-1" v-if="QRData.products && QRData.products.length" flat>
                <v-card-title class="pa-0 teal white--text">
                    <VSpacer/>
                    <h4>PRODUCTS</h4>
                    <VSpacer/>
                </v-card-title>
                <v-layout>
                    <v-flex xs4>
                        <div class="pa-1 caption grey--text font-weight-bold">Name</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Unit Price</div>
                    </v-flex>
                    <v-flex xs2>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-center">Quantity</div>
                    </v-flex>
                    <v-flex xs3>
                        <div class="pa-1 caption grey--text font-weight-bold text-xs-right">Total Price</div>
                    </v-flex>
                </v-layout>
                <template v-for="(item, i) in QRData.products">
                    <v-layout :key="i + 'scarpa-cleaning'" class="px-1">
                        <v-flex xs4>
                            <div>
                                {{item.name}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                        <v-flex xs2>
                            <div class="text-xs-center">{{item.quantity}}
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <div class="text-xs-right">{{item.unit_price ? 'P ' + parseFloat(item.unit_price * item.quantity).toFixed(2) : 'FREE'}}</div>
                        </v-flex>
                    </v-layout>
                    <v-divider :key="i + '-div-scarpa-cleaning'"></v-divider>
                </template>
            </v-card>


            <v-card-actions class="title font-weight-bold">
                Total amount
                <v-spacer />
                P {{parseFloat(totalAmount).toFixed(2)}}
            </v-card-actions>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn v-if="!!joConflict" @click="showJoConflict = true">Show conflicting JO</v-btn>
                <v-progress-circular v-else-if="loading" indeterminate></v-progress-circular>
                <template v-else>
                    <v-btn @click="save" class="primary" :loading="isSaving">Confirm</v-btn>
                </template>
                <v-btn @click="QRData = null">Cancel</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>

        </v-card>
        <v-layout v-else-if="QRData == null">
            <v-card class="rounded-card ma-3" width="100px" @click="scan('camera')">
                <v-card-text class="text-xs-center pb-0">
                    <v-icon large>camera_alt</v-icon>
                    <p>Camera</p>
                </v-card-text>
            </v-card>
            <v-card class="rounded-card ma-3" width="100px" @click="scan('file')">
                <v-card-text class="text-xs-center pb-0">
                    <v-icon large>collections</v-icon>
                    <p>Browse</p>
                </v-card-text>
            </v-card>
        </v-layout>
        <!-- <pre>{{errorMessage}}</pre> -->
        <!-- <pre>{{html5QrCode}}</pre> -->
        <!-- <v-btn @click="start">scan</v-btn> -->
        <!-- <input type="file" name="inputFile" id="inputFile" ref="inputFile" @change="setPicture" accept="image/*"> -->
        <scanner-dialog v-model="showScannerDialog" @success="decode" :mode="mode" />
        <job-order-dialog v-if="joConflict" :transactionId="joConflict.id" v-model="showJoConflict" />
        <!-- <pre>{{joConflict}}</pre> -->
    </v-container>
</template>

<script>

// import {Html5Qrcode} from "html5-qrcode"
import ScannerDialog from './ScannerDialog.vue';
import JobOrderDialog from '../transaction-reports/TransactionDialog.vue';
export default {
    components: {
        ScannerDialog,
        JobOrderDialog
    },
    data() {
        return {
            loading: false,
            // devices: [],
            // activeDevice: null,
            QRData: null,
            // file: null,
            // errorMessage: null,
            // html5QrCode: null,
            mode: null,
            showScannerDialog: false,
            joConflict: null,
            joConflictId: null,
            showJoConflict: false
        }
    },
    methods: {
        scan(mode) {
            this.mode = mode;
            this.showScannerDialog = true;
            this.joConflict = null;
        },
        decode(data) {
            try {
                var _qrData = JSON.parse(data);
                _qrData.services = [];
                _qrData.products = [];
                this.QRData = _qrData;

                this.QRData.sv = this.QRData.sv.map(item => {
                    var _item = item.split('`')
                    return {
                        nam: _item[0],
                        qty: _item[1],
                        up: _item[2]
                    }
                })

                this.QRData.lag = this.QRData.lag.map(item => {
                    var _item = item.split('`')
                    return {
                        nam: _item[0],
                        qty: _item[1],
                        up: _item[2]
                    }
                })

                this.QRData.lpk = this.QRData.lpk.map(item => {
                    var _item = item.split('`')
                    return {
                        nam: _item[0],
                        qty: _item[1],
                        up: _item[2]
                    }
                })

                this.check()
            } catch(e) {
                this.QRData = null;
                alert(e.message)
            }
        },
        check() {
            if(!this.QRData.jo) {
                this.$store.commit('setFlash', {
                    message: 'Invalid Job Order Number',
                    color: 'error'
                })
                this.QRData = null;
                return;
            }
            this.loading = true;
            axios.get(`/api/transactions/${encodeURIComponent(this.QRData.jo)}`/*, {
                query: {
                    date: this.QRData.dt
                }
            }*/).then((res, rej) => {
                this.joConflict = res.data.transaction;
                console.log(res.data);
            }).finally(() => {
                this.loading = false;
                if(this.isIncludeServices) {
                    this.lookupBasicItems();
                }
            });
        },
        lookupBasicItems() {
            if(!this.QRData.jo) {
                this.$store.commit('setFlash', {
                    message: 'Invalid Job Order Number',
                    color: 'error'
                })
                this.QRData = null;
                return;
            }
            this.loading = true;
            axios.post(`/api/transactions/look-up-items`, {
                services: this.QRData.svc,
                products: this.QRData.prd
            }).then((res, rej) => {
                this.QRData.services = res.data.services;
                this.QRData.products = res.data.products;
            }).finally(() => {
                this.loading = false;
            });
        },
        save() {
            this.$store.dispatch('qrtransaction/save', {
                formData: this.QRData
            }).then((res, rej) => {
                this.QRData = null
            });
        },
        sum(items, key) {
            return items.reduce((accumulator, object) => {
                return accumulator + object[key];
            }, 0);
        }
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
    },
    computed: {
        isSaving() {
            return this.$store.getters['qrtransaction/isSaving'];
        },
        isIncludeServices() {
            return this.$store.getters.getDopuIncludeServices;
        },
        totalAmount() {
            if(this.QRData) {
                var lag = this.QRData.lag ? this.QRData.lag.reduce((a, o) => { return a + (o.qty * o.up ); }, 0) : 0;
                var lpk = this.QRData.lpk ? this.QRData.lpk.reduce((a, o) => { return a + (o.qty * o.up ); }, 0) : 0;
                var sv = this.QRData.sv ? this.QRData.sv.reduce((a, o) => { return a + (o.qty * o.up ); }, 0) : 0;
                var ws = this.QRData.services && this.QRData.services.washing ? this.QRData.services.washing.reduce((a, o) => { return a + (o.quantity * o.unit_price ); }, 0) : 0;
                var ds = this.QRData.services && this.QRData.services.drying ? this.QRData.services.drying.reduce((a, o) => { return a + (o.quantity * o.unit_price ); }, 0) : 0;
                var os = this.QRData.services && this.QRData.services.other ? this.QRData.services.other.reduce((a, o) => { return a + (o.quantity * o.unit_price ); }, 0) : 0;
                var p = this.QRData.products ? this.QRData.products.reduce((a, o) => { return a + (o.quantity * o.unit_price ); }, 0) : 0;
                return lag + lpk + sv + ws + ds + os + p
            }
            return 0;
        }
    }
}
</script>