<template>
    <v-container>
      <AppPageHeader title="Barcode Scan Page" />
      <v-row class="mb-3 justify-center">
        <div class="v-col-12 v-col-md-4 pb-0" >
          <v-card
          @click="scanBarcodeFn"
            color="primary"
            height="80"
            width="100%"
            class="d-flex align-center justify-center rounded-lg"
          > 
          <div class="text-h6 text-capitalize text-center">SCAN BARCODE <v-icon class="ml-2" size="large" :icon="mdiBarcodeScan" ></v-icon></div>
        </v-card>
        <StreamBarcodeReader v-if="isEnable"  @decode="onDecode" @loaded="onLoaded"></StreamBarcodeReader>
         
        </div>
      </v-row>
      <v-row v-if="dataObj.asset_code">
        <div class="v-col-6 py-1"> COMPANY: </div> <div class="v-col-6 py-1">{{ dataObj.company ? dataObj.company.title : '' }}</div>
        <div class="v-col-6 py-1"> LOCATION: </div> <div class="v-col-6 py-1">{{ dataObj.location ? dataObj.location.title : ''}}</div>
        <div class="v-col-6 py-1"> CATEGORY: </div> <div class="v-col-6 py-1">{{ dataObj.category ? dataObj.category.title : '' }}</div>
        <div class="v-col-6 py-1"> ASSET CODE: </div> <div class="v-col-6 py-1">{{ dataObj.asset_code }}</div>
        <div class="v-col-6 py-1"> ASSET NAME: </div> <div class="v-col-6 py-1">{{ dataObj.asset_name }}</div>
        <v-divider></v-divider>
        <div class="v-col-6 py-1"> BRAND: </div> <div class="v-col-6 py-1">{{ dataObj.brand ? dataObj.brand.title : ''  }}</div>
        <div class="v-col-6 py-1"> MODEL: </div> <div class="v-col-6 py-1">{{ dataObj.model ? dataObj.model.title : ''  }}</div>
        <div class="v-col-6 py-1"> SPECIFICATION: </div> <div class="v-col-6 py-1">{{ dataObj.specification  }}</div>
        <v-divider></v-divider>
        <div class="v-col-12 py-1"> WARRANTY </div>
        <div class="v-col-6 py-1"> START DATE: </div> <div class="v-col-6 py-1">{{  warrantyData.warranty_start_date }}</div>
        <div class="v-col-6 py-1"> END DATE: </div> <div class="v-col-6 py-1">{{ warrantyData.warranty_end_date }}</div>
        <v-divider></v-divider>
        <div class="v-col-12 py-1"> AMC VENDOR </div>
        <div class="v-col-6 py-1"> VENDOR </div> <div class="v-col-6 py-1">{{ warrantyData.vendor ? warrantyData.vendor.title : ''  }}</div>
        <div class="v-col-6 py-1"> START DATE: </div> <div class="v-col-6 py-1">{{ warrantyData.warranty_end_date }}</div>
        <div class="v-col-6 py-1"> END DATE: </div> <div class="v-col-6 py-1">{{ warrantyData.warranty_start_date}}</div>
      </v-row>
    </v-container>
  </template>
  
<script setup>
import { ref } from "vue";
import { clientKey } from "@/services/axiosToken";
import AppPageHeader from "@/components/AppPageHeader.vue";
import { mdiBarcodeScan } from "@mdi/js";
import { StreamBarcodeReader   } from "vue-barcode-reader";
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();
const dataObj = ref({});
const warrantyData = ref({warranty_start_date :'', warranty_end_date: ''});
const isEnable = ref(false);
const scanBarcodeFn = () => {
  isEnable.value = true;
}
const onDecode = async (result) => { 
  await clientKey(authStore.token)
        .get("/api/fetch/asset-info/by/asset-code/"+ result)
        .then((res) => {
            
             dataObj.value = res.data;
             if(res.data.warranty.length > 0){
              warrantyData.value = res.data.warranty[res.data.warranty.length - 1];
             }

             isEnable.value = false;
             
        })
        .catch((err) => {
            
        });
}

const onLoaded = () => {
  console.log(`Ready to start scanning barcodes`)
}


</script>  