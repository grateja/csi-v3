<template>
    <div>
        <v-btn to="/new-transaction/services" class="translucent" active-class="primary" round>Per load</v-btn>
        <v-btn to="/new-transaction/per-kilo" class="translucent" active-class="primary" round>Per kilo</v-btn>
        <v-card class="translucent rounded-card">
            <v-card-title>
                <span class="title white--text">Wash and Dry per Kg</span>
            </v-card-title>
            <v-card-text>
                <v-card class="translucent pa-1 font-weight-bold text-xs-center">
                    <v-layout>
                        <v-flex xs4></v-flex>
                        <v-flex xs2>DELICATE</v-flex>
                        <v-flex xs2>WARM</v-flex>
                        <v-flex xs2>HOT</v-flex>
                        <v-flex xs2>SUPERWASH</v-flex>
                    </v-layout>
                </v-card>
                <v-layout v-for="row in result" :key="row.id">
                    <v-flex xs4>
                        <v-card class="translucent">
                            <v-card-text>{{row.name}}</v-card-text>
                        </v-card>
                    </v-flex>
                    <v-flex xs2>
                        <v-card class="pointer" :class="{'active' : isSelected(row, 'delicate')}" @click="selectItem(row, 'delicate', row.delicate_price)">
                            <v-card-text>P {{ parseFloat(row.delicate_price).toFixed(2)}}</v-card-text>
                        </v-card>
                    </v-flex>
                    <v-flex xs2>
                        <v-card class="pointer" :class="{'active' : isSelected(row, 'warm')}" @click="selectItem(row, 'warm', row.warm_price)">
                            <v-card-text>P {{ parseFloat(row.warm_price).toFixed(2)}}</v-card-text>
                        </v-card>
                    </v-flex>
                    <v-flex xs2>
                        <v-card class="pointer" :class="{'active' : isSelected(row, 'hot')}" @click="selectItem(row, 'hot', row.hot_price)">
                            <v-card-text>P {{ parseFloat(row.hot_price).toFixed(2)}}</v-card-text>
                        </v-card>
                    </v-flex>
                    <v-flex xs2>
                        <v-card class="pointer" :class="{'active' : isSelected(row, 'superwash')}" @click="selectItem(row, 'superwash', row.superwash_price)">
                            <v-card-text>P {{ parseFloat(row.superwash_price).toFixed(2)}}</v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-card-text>
            <v-card-text>
                <v-text-field label="Input KG" v-model="formData.kilo" outline type="number" min="5" :error-messages="kiloError" @input="kiloError = null"></v-text-field>
                <v-layout>
                    <v-flex xs6>
                        <v-text-field v-model="formData.rwLoad" label="Loads for 8KG Washer" outline type="number" :hint="getHint(formData.rwLoad, '/8Kg')" persistentHint></v-text-field>
                    </v-flex>
                    <v-flex xs6>
                        <v-text-field v-model="formData.rdLoad" label="Loads for 8KG Dryer" outline type="number" :hint="getHint(formData.rdLoad, '/8Kg')" persistentHint></v-text-field>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs6>
                        <v-text-field v-model="formData.twLoad" label="Loads for 12KG Washer" outline type="number" :hint="getHint(formData.twLoad, '/12Kg')" persistentHint></v-text-field>
                    </v-flex>
                    <v-flex xs6>
                        <v-text-field v-model="formData.tdLoad" label="Loads for 12KG Dryer" outline type="number" :hint="getHint(formData.tdLoad, '/12Kg')" persistentHint></v-text-field>
                    </v-flex>
                </v-layout>
                <v-layout>
                    <v-flex xs6>
                        <pre>{{kgCapacityWash}}</pre>
                    </v-flex>
                    <v-flex xs6>
                        <pre>{{kgCapacityDry}}</pre>
                    </v-flex>
                </v-layout>
                <v-text-field label="Total price" outline :value="`P ${totalPrice}`" readonly></v-text-field>
                <v-btn class="primary" round @click="submit">confirm</v-btn>
            </v-card-text>
        </v-card>
        <pre>{{selectedItem}}</pre>
        <pre>{{formData}}</pre>
    </div>
</template>

<script>
export default {
    data() {
        return {
            formData: {
                kilo: 0,
                rwLoad: 0,
                rdLoad: 0,
                twLoad: 0,
                tdLoad: 0,
            },
            kiloError: null,
            result: [],
            selectedItem: {
                id: null,
                washType: null,
                price: 0,
                name: null
            }
        }
    },
    methods: {
        load() {
            this.loading = true;
            axios.get('/api/services/per-kilo', {

            }).then((res, rej) => {
                this.result = res.data.result
            }).finally(() => {
                this.loading = false;
            });
        },
        isSelected(row, washType) {
            if(this.selectedItem == null) return false
            return this.selectedItem.id == row.id && washType == this.selectedItem.washType
        },
        selectItem(row, washType, price) {
            this.selectedItem.id = row.id
            this.selectedItem.washType = washType
            this.selectedItem.price = price
            this.selectedItem.name = row.name
            console.log(this.selectedItem)
        },
        recommendMachineLoads(val) {
            var totalKg = parseInt(val) | 0

            this.formData.twLoad = Math.ceil(totalKg / 12)
            this.formData.tdLoad = Math.ceil(totalKg / 12)
        },
        submit() {
            this.kiloError = null
            if(this.currentCustomer == null) {
                alert("No customer selected")
                return
            }
            if(!this.formData.kilo) {
                this.kiloError = "This field is required"
                return
            } else if(isNaN(this.formData.kilo)) {
                this.kiloError = "Not a valid number"
                return
            } else if(this.formData.kilo < 5) {
                this.kiloError = "Minimum of 5 Kg only"
                return
            }
            if(!this.selectedItem.id) {
                alert("No service selected")
                return
            }
        },
        kgPerLoad(load, kilo) {
            var _load = parseInt(load) | 0
            var _kilo = parseInt(kilo) | 0
            if(_load && _kilo) {
                return (_kilo / _load)
            }
            return 0
        },
        getHint(load, append) {
            var kgPerLoad = this.kgPerLoad(load, this.formData.kilo)
            if(kgPerLoad > 0) {
                return kgPerLoad.toFixed(1) + "Kg per load " + append
            }
        }
    },
    created() {
        this.load();
        this.formData.kilo = 8
    },
    computed: {
        currentCustomer() {
            return this.$store.getters['postransaction/getCurrentCustomer'];
        },
        currentTransaction() {
            return this.$store.getters['postransaction/getCurrentTransaction'];
        },
        totalPrice() {
            return Math.ceil(this.selectedItem.price * this.formData.kilo)
        },
        kgCapacityWash() {
            return (this.formData.rwLoad * 8) + (this.formData.twLoad * 12)
        },
        kgCapacityDry() {
            return (this.formData.rdLoad * 8) + (this.formData.tdLoad * 12)
        }
    },
    watch: {
        'formData.kilo' : function(val) {
            this.recommendMachineLoads(val)
        }
    }
}
</script>

<style lang="css">
    .active {
        background: #021e26a8!important;
        color: white!important;
    }
</style>