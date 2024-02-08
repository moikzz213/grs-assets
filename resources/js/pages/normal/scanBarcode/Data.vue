<template>
    <v-container>
        <v-row class="mb-3 justify-center">
            <div class="v-col-12 v-col-md-5 pb-0">
                <small >Recommended mobile / tablet used only</small>
                <v-card
                v-if="!isEnable"
                    @click="scanBarcodeFn"
                    color="primary"
                    height="50"
                    width="100%"
                    class="d-flex align-center justify-center rounded-lg"
                >
                    <div class="text-h6 text-capitalize text-center">
                        CLICK TO START SCAN
                        <v-icon
                            class="ml-2"
                            size="large"
                            :icon="mdiBarcodeScan"
                        ></v-icon>
                    </div>
                </v-card>
                <v-card
                    v-else-if="isEnable"
                    color="primary"
                    height="100%"
                    width="100%"
                    class="d-flex align-center justify-center rounded-lg"
                >
                <qrcode-stream @decode="onDecode"></qrcode-stream>
                    <!-- <StreamBarcodeReader
                        @decode="onDecode"
                        @loaded="onLoaded"
                    ></StreamBarcodeReader> -->
                </v-card>
            </div>
        </v-row>
        <v-row
            v-if="dataObj.asset_code"
            style="max-width: 600px"
            class="mx-auto"
        >
        <div class="v-col-4 py-1">STATUS:</div>
            <div class="v-col-8 py-1">
                {{ dataObj.status ? dataObj.status.title : "" }}
            </div>
            <div class="v-col-4 py-1">COMPANY:</div>
            <div class="v-col-8 py-1">
                {{ dataObj.company ? dataObj.company.title : "" }}
            </div>
            <div class="v-col-4 py-1">LOCATION:</div>
            <div class="v-col-8 py-1">
                {{ dataObj.location ? dataObj.location.title : "" }}
            </div>
            <div class="v-col-4 py-1">CATEGORY:</div>
            <div class="v-col-8 py-1">
                {{ dataObj.category ? dataObj.category.title : "" }}
            </div>
            <div class="v-col-4 py-1">ASSET CODE:</div>
            <div class="v-col-8 py-1">{{ dataObj.asset_code }}</div>
            <div class="v-col-4 py-1">ASSET NAME:</div>
            <div class="v-col-8 py-1">{{ dataObj.asset_name }}</div>
            <v-divider></v-divider>
            <div class="v-col-4 py-1">BRAND:</div>
            <div class="v-col-8 py-1"> {{ dataObj.brand }} </div>
            <div class="v-col-4 py-1">MODEL:</div>
            <div class="v-col-8 py-1"> {{ dataObj.model }} </div>
            <div class="v-col-4 py-1">SPECS:</div>
            <div class="v-col-8 py-1">{{ dataObj.specification }}</div>
            <div class="v-col-4 py-1">SERIAL NO:</div>
            <div class="v-col-8 py-1"> {{ dataObj.serial_number }} </div>
            <!-- <div class="v-col-4 py-1">PRICE:</div>
            <div class="v-col-8 py-1">{{ dataObj.price }}</div> -->
            <v-divider></v-divider>
            <div class="v-col-12 py-1 font-weight-bold">WARRANTY</div>
            <div class="v-col-4 py-1">VENDOR:</div>
            <div class="v-col-8 py-1">
                {{ warrantyData.vendor ? warrantyData.vendor.title : "" }}
            </div>
            <div class="v-col-4 py-1">START DATE:</div>
            <div class="v-col-8 py-1">
                {{ warrantyData.warranty_start_date }}
            </div>
            <div class="v-col-4 py-1">END DATE:</div>
            <div class="v-col-8 py-1">{{ warrantyData.warranty_end_date }}</div>
            <v-divider></v-divider>
            <div class="v-col-12 py-1 font-weight-bold">AMC</div>

            <div class="v-col-4 py-1">START DATE:</div>
            <div class="v-col-8 py-1">{{ warrantyData.warranty_end_date }}</div>
            <div class="v-col-4 py-1">END DATE:</div>
            <div class="v-col-8 py-1">
                {{ warrantyData.warranty_start_date }}
            </div>
            <v-divider></v-divider>
            <div class="v-col-12 py-1 font-weight-bold">REMARKS</div>

            <div class="v-col-12 py-1">{{ dataObj.remarks }}</div>

            <v-divider></v-divider>
            <div class="v-col-12 py-1 font-weight-bold">MAINTENANCE & INCIDENTS</div>
            <div
                class="v-col-12 py-4"
                style="background-color: #e0e0e0"
                v-if="dataObj.incidents.length > 0"
            >
                <v-row
                    v-for="(item, index) in dataObj.incidents"
                    :key="item.id"
                >
                    <div class="v-col-4 py-1">STATUS:</div>
                    <div class="v-col-8 py-1 text-uppercase">
                        {{ item.status?.title }}
                    </div>
                    <div class="v-col-4 py-1">SERVICE TYPE:</div>
                    <div class="v-col-8 py-1">{{ item.type?.title }}</div>
                    <div class="v-col-4 py-1">DATE RECEIVED:</div>
                    <div class="v-col-8 py-1">{{ useFormatDate(item.created_at) }}</div>
                    <div class="v-col-4 py-1">DATE CLOSED:</div>
                    <div class="v-col-8 py-1">{{ item.date_closed ? useFormatDate(item.date_closed) : '' }}</div>
                    <div class="v-col-4 py-1">REMARKS:</div>
                    <div class="v-col-8 py-1 pb-4">
                        <v-row>
                            <div class="v-col-12 py-1" v-for="rem in item.remarks" :key="rem.id">
                                - {{ rem.remarks }}
                            </div>
                        </v-row>
                    </div>
                    <v-divider></v-divider>
                </v-row>
            </div>
        </v-row>
        <v-row v-if="scanValue">
            <div class="v-col-12 text-center">
                Asset Code: {{ scanValue }} not in the system.
            </div>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref } from "vue";
import { clientKey } from "@/services/axiosToken";
import { mdiBarcodeScan } from "@mdi/js";
import { StreamBarcodeReader } from "vue-barcode-reader";
import { QrcodeStream } from 'qrcode-reader-vue3'
import { useAuthStore } from "@/stores/auth";
import { useFormatDate } from "@/composables/formatDate.js";
const authStore = useAuthStore();
const dataObj = ref({});
const warrantyData = ref({ warranty_start_date: "", warranty_end_date: "" });
const isEnable = ref(false);
const scanBarcodeFn = () => {
    dataObj.value = {};
    isEnable.value = true;
};

const scanValue = ref('');
const onDecode = async (result) => {
    scanValue.value = result;
    await clientKey(authStore.token)
        .get("/api/fetch/asset-info/by/asset-code/" + result)
        .then((res) => {
            dataObj.value = res.data;
            if(dataObj.value.asset_code){
                isEnable.value = false;
                scanValue.value = '';
            }
            if (res.data.pivot_warranties.length > 0) {
                warrantyData.value =
                    res.data.pivot_warranties[res.data.pivot_warranties.length - 1];
            }

        })
        .catch((err) => {

            if(err?.response?.status == 401){
                alert("Error: Something went wrong. page will be refresh.");
                window.location.href = window.location.href;
            }
        });
};

 
</script>
