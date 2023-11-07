<template>
    <v-container>
        <AppPageHeader title="Incident detail page" />
        <v-row class="mb-3">
            <v-col class="v-col-12 mt-1 col-sm-12 py-0">
                <v-row class="mb-3">
                    <v-btn
                        :class="`${
                            isActive == 'details' ? 'tab-active' : ''
                        }  v-col-12 v-col-md-2 mx-2`"
                        @click="changeType('details')"
                        >Details</v-btn
                    >
                    <v-btn
                    v-if="isEdit"
                        :class="`${
                            isActive == 'attachment' ? 'tab-active' : ''
                        }   v-col-12 v-col-md-2 mx-2`"
                        @click="changeType('attachment')"
                        >Attachments</v-btn
                    >
                    <v-btn
                        v-if="
                            isEdit &&
                            (authStore.user.profile.role == 'facility' ||
                                authStore.user.profile.role == 'admin' ||
                                authStore.user.profile.role == 'superadmin' ||
                                authStore.user.profile.role ==
                                    'technical-operation')
                        "
                        :class="`${
                            isActive == 'facility' ? 'tab-active' : ''
                        }   v-col-12 v-col-md-2 mx-2`"
                        @click="changeType('facility')"
                        >Facility Team</v-btn
                    >
                    </v-row>
                <v-card v-if="isActive == 'details'">
                    <Form
                        as="v-form"
                        :validation-schema="validation"
                        v-slot="{ meta }"
                    >
                        <v-card-title class="my-3 ml-3 text-uppercase text-h6">
                            {{ props.headertitle }}</v-card-title
                        >
                        <v-card-text>
                            <v-row v-if="isEdit">
                                <div
                                    class="v-col-12 v-col-md-6 d-flex justify-space-between"
                                >
                                    <div class="font-weight-bold">
                                        SN: ISR-{{ pad(objData.id) }}
                                    </div>
                                    <div class="reported-by">
                                        REPORTED BY:
                                        {{ objData.profile?.display_name }}
                                    </div>
                                </div>
                                <div
                                    class="v-col-12 v-col-md-6 text-right font-weight-bold"
                                >
                                    STATUS: {{ objData.status?.title }}
                                </div>
                            </v-row>
                            <v-row>
                                <div class="v-col-12 v-col-md-3">
                                    <Field
                                        name="Type"
                                        v-slot="{ field, errors }"
                                        v-model="objData.type_id"
                                    >
                                        <v-select
                                            :items="typeList"
                                            v-model="objData.type_id"
                                            variant="outlined"
                                            density="compact"
                                            hide-details
                                            item-value="id"
                                            item-title="title"
                                            clearable
                                            label="Incident Type*"
                                            v-bind="field"
                                            :error-messages="errors"
                                        ></v-select>
                                    </Field>
                                </div>
                                <div class="v-col-12 v-col-md-3">
                                    <Field
                                        name="Urgency"
                                        v-slot="{ field, errors }"
                                        v-model="objData.urgency"
                                    >
                                        <v-select
                                            :items="urgencyList"
                                            v-model="objData.urgency"
                                            variant="outlined"
                                            density="compact"
                                            hide-details
                                            clearable
                                            label="Urgency*"
                                            item-value="id"
                                            item-title="title"
                                            v-bind="field"
                                            :error-messages="errors"
                                        ></v-select>
                                    </Field>
                                </div>
                                <div class="v-col-12 v-col-md-6 d-flex">
                                    <v-text-field
                                        v-model="objData.asset_code"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                        label="Asset Code"
                                    ></v-text-field>
                                    <v-icon
                                        @click="enableBarcodeFn"
                                        class="ml-2 my-auto"
                                        size="large"
                                        :icon="mdiBarcodeScan"
                                    ></v-icon>
                                </div>
                                <div class="v-col-12">
                                    <Field
                                        name="Title"
                                        v-slot="{ field, errors }"
                                        v-model="objData.title"
                                    >
                                        <v-text-field
                                            v-model="objData.title"
                                            variant="outlined"
                                            density="compact"
                                            hide-details
                                            label="Asset Name*"
                                            v-bind="field"
                                            :error-messages="errors"
                                        ></v-text-field>
                                    </Field>
                                </div>
                                <div class="v-col-12 v-col-md-12">
                                    <v-row>
                                        <div class="v-col-12 v-col-md-3">
                                            <v-row>
                                                <div class="v-col-12">
                                                    <Field
                                                        name="Company"
                                                        v-slot="{
                                                            field,
                                                            errors,
                                                        }"
                                                        v-model="
                                                            objData.company_id
                                                        "
                                                    >
                                                        <v-select
                                                            :items="companyList"
                                                            v-model="
                                                                objData.company_id
                                                            "
                                                            variant="outlined"
                                                            density="compact"
                                                            hide-details
                                                            item-value="id"
                                                            item-title="title"
                                                            clearable
                                                            label="Company*"
                                                            v-bind="field"
                                                            :error-messages="
                                                                errors
                                                            "
                                                        ></v-select>
                                                    </Field>
                                                </div>
                                                <div class="v-col-12">
                                                    <Field
                                                        name="Location"
                                                        v-slot="{
                                                            field,
                                                            errors,
                                                        }"
                                                        v-model="
                                                            objData.location_id
                                                        "
                                                    >
                                                        <v-select
                                                            :items="
                                                                locationList
                                                            "
                                                            v-model="
                                                                objData.location_id
                                                            "
                                                            variant="outlined"
                                                            density="compact"
                                                            hide-details
                                                            item-value="id"
                                                            item-title="title"
                                                            clearable
                                                            label="Location*"
                                                            v-bind="field"
                                                            :error-messages="
                                                                errors
                                                            "
                                                        ></v-select>
                                                    </Field>
                                                </div>
                                            </v-row>
                                        </div>
                                        <div class="v-col-12 v-col-md-9">
                                            <v-textarea
                                                variant="outlined"
                                                density="compact"
                                                label="Description"
                                                hide-details
                                                v-model="objData.description"
                                            ></v-textarea>
                                        </div>
                                    </v-row>
                                </div>
                            </v-row>
                            <v-divider class="my-3"></v-divider>
                            <v-row>
                                <div class="v-col-12 v-col-md-6">
                                    <v-btn
                                        size="small"
                                        color="primary"
                                        @click="saveData"
                                        :disabled="!meta.valid"
                                        :loading="loadingBtn"
                                        v-if="!isEdit || (isEdit && props.objectdata.profile_id == loggedID)"
                                        >Submit</v-btn>
                                        
                                </div>
                                <div class="v-col-12 v-col-md-3">
                                    DATE REPORTED:
                                    {{ useFormatDate(objData.created_at) }}
                                </div>
                                <div class="v-col-12 v-col-md-3">
                                    DATE CLOSED:
                                    {{
                                        date_closed
                                            ? useFormatDate(objData.date_closed)
                                            : ""
                                    }}
                                </div>
                            </v-row>
                        </v-card-text>
                    </Form>
                </v-card>
               
                <Attachment v-else-if="isEdit && isActive == 'attachment'" :incident-id="route.params.id" :files="objData.files" @deleted="DataUpdateEmit" :objectdata="props.objectdata"/>
                <Facility v-else-if="(loggedRole == 'superadmin' || loggedRole == 'admin'  || loggedRole == 'technical-operation' || loggedRole == 'facility') && 
                isEdit && isActive == 'facility'" :objectdata="props.objectdata" @saved="DataUpdateEmit"/>   
                
            </v-col>
        </v-row>
        <AppSnackBar :options="sbOptions" />

        <v-dialog width="500" v-model="enableBarcode">
            <v-card>
                <v-card-text>
                    <StreamBarcodeReader
                        @decode="onDecode"
                        @loaded="onLoaded"
                    ></StreamBarcodeReader>
                </v-card-text>
                <v-card-text v-if="!isScanLoaded">Please wait...</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text="Close" @click="enableBarcode = false"></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import AppPageHeader from "@/components/ApppageHeader.vue";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { useAuthStore } from "@/stores/auth";
import { useRoute, useRouter } from "vue-router";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
import { clientKey } from "@/services/axiosToken";
import { mdiBarcodeScan } from "@mdi/js";
import { StreamBarcodeReader } from "vue-barcode-reader";
import { useFormatDate } from "@/composables/formatDate.js";

import Attachment from "./Attachment.vue"
import Facility from "./Facility.vue"
 
const authStore = useAuthStore();
const props = defineProps({
    objectdata: {
        type: Object,
        default: {},
    },
    headertitle: {
        type: String,
        default: null,
    },
});
const objData = ref({});
const sbOptions = ref({}); 

const DataUpdateEmit = (v) => {
    sbOptions.value = {
        status: true,
        type: "success",
        text: v,
    };
}

const loggedID = ref(authStore.user.profile.id);
const typeList = ref([]);
const fetchTypes = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch-global/incident-types/active")
        .then((res) => {
            typeList.value = res.data;
        })
        .catch((err) => {});
};

const route = useRoute();
const router = useRouter();
const isActive = ref(route.query.type);
const urgencyList = ref([
    { id: 1, title: "1. Normal" },
    { id: 2, title: "2. Medium" },
    { id: 3, title: "3. High" },
]); 

let validation = yup.object({
    Type: yup.string().required(),
    Urgency: yup.string().required(),
    Title: yup.string().required(),
    Company: yup.string().required(),
    Location: yup.string().required(),
});

const enableBarcode = ref(false);
const enableBarcodeFn = () => {
    isScanLoaded.value = false;
    enableBarcode.value = true;
};

const onDecode = async (result) => {
    await clientKey(authStore.token)
        .get("/api/fetch/asset-info/by/asset-code/" + result)
        .then((res) => {
            objData.value.asset_code = res.data.asset_code;
            objData.value.title = res.data.asset_name;
            objData.value.company_id = res.data.company_id;
            objData.value.location_id = res.data.location_id;
            objData.value.asset_id = res.data.id;
            enableBarcode.value = false;
        })
        .catch((err) => {});
};

const isScanLoaded = ref(false);
const onLoaded = () => {
    isScanLoaded.value = true;
};

const searchURL = new URL(window.location);
const changeType = (type) => {
    isActive.value = type;
    pushStateFn("type", type);
};

const loadingBtn = ref(false);
const loggedRole = ref(authStore.user.profile.role);
const saveData = async () => {
    loadingBtn.value = true;
    sbOptions.value = {
        status: true,
        type: "info",
        text: "Please wait...",
    };
    objData.value.profile_id = authStore.user.profile.id;
    // /api/store-update/incident
    await clientKey(authStore.token)
        .post("/api/store-update/incident", objData.value)
        .then((res) => {
            sbOptions.value = {
                status: true,
                type: "success",
                text: res.data.message,
            };
            if (!route.params.id) {
                setTimeout(() => {
                   
                    router
                        .push({
                            name: "EditIncident",
                            params: {
                                id: res.data.id,
                            },
                            query: { type: "details" },
                        })
                        .catch((err) => {});
                        loadingBtn.value = false;
                }, 800);
            }else{
                loadingBtn.value = false;
            }
            
        })
        .catch((err) => {});
};

const btnLoading = ref(false);
const isEdit = ref(false);

const pushStateFn = (type, value) => {
    searchURL.searchParams.set(type, value);
    window.history.pushState({}, "", searchURL);
};

const companyList = ref([]);
const fetchCompanies = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch/companies")
        .then((res) => {
            companyList.value = res.data;
        })
        .catch((err) => {});
};

const locationList = ref([]);
const fetchLocations = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch/locations/non-paginated/data")
        .then((res) => {
            locationList.value = res.data;
        })
        .catch((err) => {});
};

const pad = (v, size = 6) => {
    let s = "00000" + v;
    return s.substring(s.length - size);
};

onMounted(() => {
    

    if(route.params.id) {
        isEdit.value = true;
    }

    if (!isActive.value) {
        isActive.value = "details";
        pushStateFn("type", "details");
    }
    fetchTypes().then(() => {
        fetchCompanies().then(() => {
            fetchLocations();
        });
    }); 
   

    if (isEdit.value) { 
        objData.value = props.objectdata;
        objData.value.asset_code = props.objectdata.asset?.asset_code;
        objData.value.title = props.objectdata.asset
            ? props.objectdata.asset.asset_name
            : props.objectdata.title;
        objData.value.company_id = props.objectdata.company_id;
        objData.value.location_id = props.objectdata.location_id;
        objData.value.asset_id = props.objectdata.id;
        objData.value.profile_id = props.objectdata.profile_id;
        objData.value.type_id = parseInt(props.objectdata.type_id);
        objData.value.urgency = parseInt(props.objectdata.urgency);
        objData.value.description = props.objectdata.description; 
    }
});
</script>

<style lang="scss" scoped>
.tab-active {
    background-color: #c6a92d;
    color: #ffffff;
}
</style>
