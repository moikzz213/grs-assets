<template>
    <v-container v-if="pageAccessible" style="max-width: 90%" class="mt-0 pt-5 generate-file">
        <v-row class="mt-0 pt-0 no-print">
            <div>
                <v-btn color="primary" class="mx-1" size="small" @click="printFn"
                    >Print</v-btn
                > 
            </div>
        </v-row>
        <v-row class="mt-5  pb-2">
            <div class="v-col-4 v-col-md-4 font-weight-bold">
                <v-img v-if="dataObj.types == 'request'" :src="baseURL + '/assets/images/gallega.png?v='+getTime" width="120"/>
                <v-img v-else :src="baseURL + '/assets/images/grandiose-logo.jpg?v='+getTime" width="120"/>
            </div>
            <div class="v-col-4 v-col-md-4 mt-auto text-center font-weight-bold text-h5">
                PACKING LIST
            </div>
            <div class="v-col-4 v-col-md-4 mt-auto">
                
            </div>
        </v-row>
        <v-row class="pt-0 mt-0" v-if="dataObj.types == 'request'">
            <div class="v-col-6 v-col-md-6 mt-auto">            
                    <div class="mr-2 font-weight-bold">SHIPPING ADDRESS</div>
                    <div class="mr-6 pr-2 font-weight-bold">Grandiose Supermarket Single Proprietorship LLC</div>
                    <div class="mr-6 pr-2">Victory Heights, Plot No. 6827281, Sports City  Dubai, UAE</div>
                    <div class="mr-6 pr-2">TRN: 100296457300003</div>                 
            </div>
            <div class="v-col-6 v-col-md-6 mt-auto">
                <div class="mr-2 font-weight-bold">DELIVERY FROM</div>
                    <div class="mr-6 pr-2 font-weight-bold">Gallega Global Logistics</div>
                    <div class="mr-6 pr-2">Plot No. KHIA7-02A, Taweelah, Abu Dhabi, UAE</div>
                    <div class="mr-6 pr-2">TRN: 100223925700003</div>
            </div>
        </v-row>
        <v-row class="pt-0 mt-0" v-else>
            
            <div class="v-col-6 v-col-md-6 mt-auto">
                <div class="mr-2 font-weight-bold">SHIPPING ADDRESS</div>
                    <div class="mr-6 pr-2 font-weight-bold">Gallega Global Logistics</div>
                    <div class="mr-6 pr-2">Kizad - Khalifa Industrial Zone - Abu Dhabi, UAE</div>
                    <div class="mr-6 pr-2">TRN: 100223925700003</div>
            </div>
            <div class="v-col-6 v-col-md-6 mt-auto">            
                    <div class="mr-2 font-weight-bold">DELIVERY FROM</div>
                    <div class="mr-6 pr-2 font-weight-bold">Grandiose Supermarket Single Proprietorship LLC</div>
                    <div class="mr-6 pr-2">Victory Heights, Plot No. 6827281, Sports City  Dubai, UAE</div>
                    <div class="mr-6 pr-2">TRN: 100296457300003</div>                 
            </div>
        </v-row>
        <v-row class="pt-0 mt-4"> 
            <div class="v-col-2 py-1">Invoice No.</div> <div class="v-col-9 py-1"> SN-{{ dataObj?.types == "request" ? "5" : "3"
                }}{{ pad(dataObj?.id) }}</div>
            <div class="v-col-2 py-1">Invoice Date: </div> <div class="v-col-9 py-1">  {{ useFormatDateFull(invoiceDate) }}</div> 
        </v-row>
        <v-divider class="my-5"></v-divider>
        <v-row>
            <div class="v-col-5 v-col-md-5 py-1 font-weight-bold text-center my-auto">DESCRIPTION</div> 
            <div class="v-col-2 v-col-md-2 py-1 font-weight-bold text-center my-auto">ORDER QTY</div>
            <div class="v-col-2 v-col-md-2 py-1 font-weight-bold text-center my-auto">WEIGHT (KG)</div> 
            <div class="v-col-2 v-col-md-2 py-1 font-weight-bold text-center my-auto">ORIGIN</div>
            <div class="v-col-1 v-col-md-1 py-1 font-weight-bold text-center my-auto">HS CODE</div> 
        </v-row> 
        <v-divider class="my-5"></v-divider>
        <template v-for="(item, index) in dataObj?.items" :key="dataObj.id"> 
            <v-row v-if="item.is_available">
                <div class="v-col-5 v-col-md-5 py-1">{{ item.item_description }}</div> 
                <div class="v-col-2 v-col-md-2 py-1 text-center">{{ item.qty }}</div>
                <div class="v-col-2 v-col-md-2 py-1 text-center">{{ item.weight }}</div> 
                <div class="v-col-2 v-col-md-2 py-1 text-center">UAE</div> 
                <div class="v-col-1 v-col-md-1 py-1 text-center">NA</div>  
            </v-row>
        </template>
        <v-divider class="my-5"></v-divider> 
        
        <v-divider class="my-5"></v-divider> 
        <v-row> 
         
            <div class="v-col-6 v-col-md-6 py-1 text-right my-auto font-weight-bold">TOTAL GROSS WEIGHT</div>
            <div class="v-col-2 v-col-md-2 py-1 font-weight-bold text-center my-auto">{{ subTotal }} KG</div> 
            <div class="v-col-4 v-col-md-4 py-1 text-center my-auto"></div> 
        </v-row> 
        
        <v-divider class="my-5"></v-divider> 
        <v-row v-if="dataObj.types == 'request'">
            <div class="v-col-8 v-col-md-8 py-1 text-center my-auto"> 
                I/We confirm that we received above material in good conditions. 
            </div> 
            <div class="v-col-4 v-col-md-4 py-1 text-center" style="border-left:1px solid #ccc;border-right:1px solid #ccc;"> Authorized Signatory/Stamp 
                <div class="d-flex">
                <v-img v-if="signature" :src="baseURL + '/file/' + signature.path" style="max-height:150px"></v-img>
                <v-img v-if="stamp" :src="baseURL + '/file/' + stamp.path" style="max-height:150px"></v-img>
            </div>
            </div> 
        </v-row>
        <v-row v-else>
            <div class="v-col-8 v-col-md-8 py-1 my-auto text-center"> 
                I/We confirm that we received above material in good conditions. 
            </div> 
            <div class="v-col-4 v-col-md-4 py-1 text-center" style="border-left:1px solid #ccc;border-right:1px solid #ccc;"> Authorized Signatory/Stamp 
                <div class="d-flex">
                <v-img v-if="signature" :src="baseURL + '/file/' + signature.path" style="max-height:150px"></v-img>
                <v-img v-if="stamp" :src="baseURL + '/file/' + stamp.path" style="max-height:150px"></v-img>
            </div>
            </div> 
        </v-row>
        <v-divider class="my-5"></v-divider> 
        <v-row>
            <div class="v-col-12 v-col-md-12 text-center py-1 my-auto" v-if="dataObj.types == 'request'">
                Plot No. KHIA7-02A, Taweelah, Abu Dhabi, UAE | Phone: +971 4 8876231 - Fax: +971 4 8876231 | www.gallega.com 
            </div>
            <div class="v-col-12 v-col-md-12 text-center py-1 my-auto" v-else>
                Graniose Supermarket - Sole Proprietorship L.L.C | Commercial License No. CN - 2245809 <br/>
                Address: Ground Floor, Horizon Towers, Al Reem Island, Abu Dhabi, UAE | P.O. Box: 26739 | Tel: +971 2 207 2444
            </div>
        </v-row>
        
        <v-row class="mt-3">
            <div class="v-col-12 px-0 my-0">
             
                <v-divider></v-divider>
            </div>
        </v-row> 
    </v-container>
    <v-container v-else style="max-width: 90%" class="mt-5 pt-5 generate-file">
        <v-row>
            <v-col>
                <h3 class="text-center">Page not found.</h3>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useFormatDateFull } from "@/composables/formatDate.js";
const route = useRoute(); 

const dataObj = ref({}); 
const invoiceDate = ref(''); 
 
const printFn = () => {
    window.print();
};
const baseURL = ref(window.location.origin);

const pad = (v, size = 6) => {
    let s = "00000" + v;
    return s.substring(s.length - size);
};  

const subTotal = ref('0.00');
 
const signature = ref('');
const stamp = ref('');
const calculateTotal = () => {
    let sub = 0;
 
    dataObj.value?.items?.map((o) => { 
        if(o.is_available){
            sub += parseFloat(o.qty) * (parseFloat(o.weight)); 
        }
    });
    
    if(sub && !isNaN(sub)){ 
        subTotal.value = (sub).toFixed(2); 
    }
    
 
    if(dataObj.value?.transfer_from?.attachment?.length > 0){
        dataObj.value?.transfer_from?.attachment?.map((o) =>{
            if(o.type == "location-request-stamp"){
                stamp.value = o;
            }else if(o.type == "location-request-signature"){
                signature.value = o;
            }
        });
    }else{
        dataObj.value?.transfer_to?.attachment?.map((o) =>{
            if(o.type == "location-transfer-stamp"){
                stamp.value = o;
            }else if(o.type == "location-transfer-signature"){
                signature.value = o;
            }
        });
    }
}
const getTime = ref('');
const pageAccessible = ref(false);
onMounted(() => {
    let getGeneratedItems = localStorage.getItem('grs-generate-list');
    if(getGeneratedItems){
        pageAccessible.value = true;
    }
    dataObj.value = JSON.parse(atob(getGeneratedItems)); 
    //let date = new Date(dataObj.value.created_at); // replace with your date
    let date = new Date(); // replace with your date
    let dddate = new Date();
    getTime.value = dddate.getTime();
    invoiceDate.value = new Date(date.getTime() - 15 * 24 * 60 * 60 * 1000); 
    
    calculateTotal();
     
});
</script> 
<style >
@media print {
   /* All print related styles to be added here */
  .generate-file div {
      font-size: 10px !important;
   }
}
</style>
