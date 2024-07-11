<template>
    <v-container>
        <AppPageHeader title="Stamp & Signature for Inbound-Outbound Request" />
        <v-row class="mb-3 mt-3">
            <v-col class="v-col-12 mt-1 col-sm-12 py-0">
                <div class="mb-3">
                    <v-btn
                        :class="`${
                            isActive == 'request' ? 'tab-active' : ''
                        }  v-col-12 v-col-md-3 mx-2`"
                        @click="changeType('request')"
                        >Request Asset</v-btn
                    >
                    <v-btn
                        :class="`${
                            isActive == 'transfer' ? 'tab-active' : ''
                        }   v-col-12 v-col-md-3 mx-2`"
                        @click="changeType('transfer')"
                        >Transfer Asset</v-btn
                    > 
                </div>
                <v-card>
                    <v-card-text> 
                        <v-row>
                            <v-col class="v-col-12 col-sm-12  pt-0 text-center my-5">
                                <h3>Stamp & Signature</h3>
                            </v-col>
                        </v-row>
                        <v-divider></v-divider>
                        <v-row class="mb-3 mt-3"> 
                            
                                    <div class="v-col-6 pt-0">
                                        <v-row>
                                             
                                            <div class="v-col-12 pt-5 pb-2 d-flex">
                                                <Studio
                                                    :options="{ multiSelect: false, type: studioFileTypez + '-stamp' }" 
                                                    @select="studioSelectResponse" 
                                                />
                                                <h3 class="ml-5 mt-2">UPLOAD COMPANY STAMP</h3>
                                            </div>
                                            </v-row>
                                            <v-row v-if="stampFiles.length > 0" class="px-1">
                                            <div
                                                v-for="(file, index) in stampFiles"
                                                :key="file.id"
                                                class="v-col-12 v-col-md-4 pa-2 mb-5"
                                                style="position: relative"
                                            >
                                                <v-btn
                                                style="position: absolute; top: 0; right: 0; z-index: 1"
                                                :icon="mdiClose"
                                                size="26px"
                                                color="error"
                                                @click="() => removeAttachment(file.id)"
                                                >
                                                </v-btn>
                                                <v-card @click="() => openAttachment(file.path)">
                                                <v-img :src="baseURL + '/file/' + file.path" cover aspect-ratio="1">
                                                </v-img>
                                                </v-card>
                                            </div> 
                                            </v-row>
                                            <v-row v-else>
                                            <div class="v-col-12 pt-0 pb-5 ">
                                                <v-sheet color="grey-lighten-4" class="pa-6 text-center">
                                                Attachment is empty at the moment.
                                                </v-sheet>
                                            </div>
                                            </v-row>
                                    </div>

                                    <div class="v-col-6 pt-0">
                                        <v-row>
                                             
                                            <div class="v-col-12 pt-5 pb-2 d-flex">  
                                                <Studio
                                                    :options="{ multiSelect: false, type: studioFileTypez + '-signature' }" 
                                                    @select="studioSelectResponse" 
                                                />
                                                <h3 class="ml-5 mt-2">UPLOAD SIGNATURE</h3>
                                            </div>
                                            </v-row>
                                            <v-row v-if="signatureFile.length > 0" class="px-1">
                                            <div
                                                v-for="(file, index) in signatureFile"
                                                :key="file.id"
                                                class="v-col-12 v-col-md-4 pa-2 mb-5"
                                                style="position: relative"
                                            >
                                                <v-btn
                                                style="position: absolute; top: 0; right: 0; z-index: 1"
                                                :icon="mdiClose"
                                                size="26px"
                                                color="error"
                                                @click="() => removeAttachment(file.id)"
                                                >
                                                </v-btn>
                                                <v-card @click="() => openAttachment(file.path)">
                                                <v-img :src="baseURL + '/file/' + file.path" cover aspect-ratio="1">
                                                </v-img>
                                                </v-card>
                                            </div> 
                                            </v-row>
                                            <v-row v-else>
                                            <div class="v-col-12 pt-0 pb-5 ">
                                                <v-sheet color="grey-lighten-4" class="pa-6 text-center">
                                                Attachment is empty at the moment.
                                                </v-sheet>
                                            </div>
                                            </v-row>
                                    </div>
                        </v-row>
                        <v-row  class="d-flex">
                            <div class="v-col-3">
                                <v-btn width="100%" color="primary" @click="saveData">Submit</v-btn>
                            </div>
                        </v-row>
                    </v-card-text>  
                </v-card>
            </v-col>
        </v-row>
        <v-dialog v-model="dialogAttachment" width="95%" max-width="900">
            <v-card class="bg-black"> 
                <div
                    style="height: 680px; width: 100%"
                    class="d-flex align-center justify-center"
                > 
                    <v-img :src="baseURL + '/file/' + filePath"></v-img>
                </div> 
            </v-card>
        </v-dialog>
        <AppSnackBar :options="sbOptions" />
    </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import AppPageHeader from "@/components/ApppageHeader.vue";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { useAuthStore } from "@/stores/auth";
import { mdiClose } from "@mdi/js";
import { useRouter, useRoute } from "vue-router";
import Studio from "@/studio/Studio.vue";
import { clientKey } from "@/services/axiosToken";
const emit = defineEmits(["saved"]);
const authStore = useAuthStore(); 

// base URL 
const baseURL = ref(window.location.origin);

const stampFiles = ref([]);
const signatureFile = ref([]);
const sbOptions = ref({});
 
const dataObj = ref({});  

const router = useRouter();
const route = useRoute();
const isActive = ref(route.params.type); 
const studioFileTypez = ref('location-'+isActive.value);
const mergeAttachFiles = ref([]);

const changeType = (type) => {
    isActive.value = type;
    studioFileTypez.value =  'location-'+isActive.value; 
    stampFiles.value = [];
    signatureFile.value = [];

    getData();
    router.push({ path: "/locations/" + route.params.location.toLowerCase() + '/stamp-signature/'+ type });
};

const btnLoading = ref(false); 

const saveData = () => {
    btnLoading.value = true;
    sbOptions.value = {
        status: true,
        type: "info",
        text: "Please wait...",
    }; 

    let data = mergeAttachFiles.value;
    const filteredArr = data.filter(item => item !== null).map(obj => obj.id);
 
    let submitData = { 
        stamp: filteredArr,  
        profile_id: authStore.user.profile.id,
        location: route.params.location
       
    }; 

    let apiURL = "/api/stamp-signature/add-update"; 

    clientKey(authStore.token)
        .post(apiURL, submitData)
        .then((res) => {
            sbOptions.value = {
                status: true,
                type: "success",
                text: "Data has been saved.",
            };

            btnLoading.value = false; 
        })
        .catch((err) => {

            sbOptions.value = {
                status: true,
                type: "error",
                text: "Error: Failed to save data.",
            };
            dataObj.value.loading = false;

        });
}; 

const studioSelectResponse = (v) => {   
    let fileExist = null;

    if(v && v.length > 0 && (v[0].type == "location-request-stamp" || v[0].type == "location-transfer-stamp")){
        stampFiles.value  = v; 
    }else if(v && v.length > 0 && ( v[0].type == "location-request-signature" || v[0].type == "location-transfer-signature")){
        signatureFile.value = v;
    }  
   
    fileExist = mergeAttachFiles.value.filter(obj => obj.type !== v[0].type);  
     
    mergeAttachFiles.value = [...fileExist, ...v]; 
};
// attachment slider
const dialogAttachment = ref(false);
 
const filePath = ref('');
const openAttachment = (file) => {
    filePath.value = file;
    dialogAttachment.value = true;
};
const removeAttachment = (theId) => {
  stampFiles.value = stampFiles.value.filter((f) => f.id !== theId);
  signatureFile.value = signatureFile.value.filter((f) => f.id !== theId);
  mergeAttachFiles.value = mergeAttachFiles.value.filter((f) => f.id !== theId); 
};

const checkAttachment = () => {
    if(mergeAttachFiles.value?.length > 0){
        mergeAttachFiles.value.map((o) => {
          
            if(o.type == "location-request-stamp" && isActive.value == 'request'){
                stampFiles.value = [o];
            }else if(o.type == "location-request-signature" && isActive.value == 'request'){
                signatureFile.value = [o];
            }else if(o.type == "location-transfer-stamp" && isActive.value == 'transfer'){
                stampFiles.value = [o];
            }else if(o.type == "location-transfer-signature" && isActive.value == 'transfer'){
                signatureFile.value = [o];
            }
        }); 
    
    }
}

const getData = async () => {
    
    await clientKey(authStore.token)
        .get(
            "/api/location/"+ route.params.location +"/stamp-signature"
        )
        .then((response) => { 
            mergeAttachFiles.value = Object.assign([], response.data.attachment);  
     
            checkAttachment();
        })
        .catch((err) => {});
};

onMounted(() => {
    getData();
});
</script>

<style lang="scss" scoped>
.tab-active {
    background-color: #c6a92d;
    color: #ffffff;
}
</style>
