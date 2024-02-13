<template>
    <div>
        <v-dialog
            :width="warrantyAsset ? 1200 : 600"
            persistent
            v-model="popDialog"
            @keydown.esc="cancelAddNew"
            class="d-flex"
            style="overflow: auto"
        >
            <v-card class="py-3">
                <v-row>
                    <div :class="`${warrantyAsset ? 'v-col-4' : 'v-col-12'}`">
                        <Form
                            as="v-form"
                            :validation-schema="validation"
                            v-slot="{ meta }"
                        >
                            <v-card-title class="text-h6 d-flex"
                                >{{ title }}<v-spacer></v-spacer
                                ><small class="text-error">{{
                                    errorMessage
                                }}</small>
                                <v-spacer></v-spacer
                            ></v-card-title>
                            <v-card-text>
                                <v-row>
                                    <div
                                        class="v-col-12"
                                        v-for="(item, index) in freeForm"
                                        :key="index"
                                    >
                                        <Field
                                            :name="item.name"
                                            v-slot="{ field, errors }"
                                            v-model="dataObj[item.name]"
                                        >
                                            <v-text-field
                                                v-if="
                                                    item.type == 'text' ||
                                                    item.type == 'number' ||
                                                    item.type == 'date' ||
                                                    item.type == 'email'
                                                "
                                                :type="item.type"
                                                :autofocus="
                                                    index === 0 ? true : false
                                                "
                                                v-model="dataObj[item.name]"
                                                density="compact"
                                                variant="outlined"
                                                :label="`${item.label}${
                                                    item.required
                                                        ? '*'
                                                        : ' (Optional)'
                                                }`"
                                                hide-details
                                                v-bind="field"
                                                @keydown.enter="saveData"
                                                :error-messages="errors"
                                            ></v-text-field> 
                                        </Field>
                                        <v-autocomplete
                                                v-if="item.type == 'select'"
                                                :autofocus="
                                                    index === 0 ? true : false
                                                "
                                                v-model="dataObj[item.name]"
                                                density="compact"
                                                variant="outlined"
                                                :label="`${item.label}${
                                                    item.required ? '*' : ''
                                                }`"
                                                :items="item.data"
                                                hide-details
                                                item-value="id"
                                                item-title="title" 
                                            ></v-autocomplete>
                                            <v-textarea v-if="item.type == 'textarea'"
                                            :autofocus="
                                                index === 0 ? true : false
                                                "
                                                v-model="dataObj[item.name]"
                                                density="compact"
                                                variant="outlined"
                                                :label="`${item.label}${
                                                    item.required ? '*' : ' (Optional)'
                                                }`"
                                                :items="item.data"
                                                hide-details 
                                            ></v-textarea>
                                    </div>
                                </v-row>
                            </v-card-text>
                            <v-divider class="mt-2"></v-divider>
                            <div class="d-flex pa-3">
                                <v-spacer></v-spacer>
                                <v-btn
                                    size="small"
                                    text="cancel"
                                    @click="cancelAddNew"
                                    class="mx-1"
                                ></v-btn>

                                <v-btn
                                v-if="!props.isView"
                                    size="small"
                                    color="primary"
                                    @click="saveData"
                                    :disabled="!meta.valid"
                                    >Save</v-btn
                                >
                            </div>
                        </Form>
                    </div>

                    <div
                        class="v-col-4"
                        style="border-left: 1px solid #ccc; border-right: 1px solid #ccc;"
                        v-if="warrantyAsset"
                    >
                        <v-card-text>
                            <h5>LINK ASSET</h5>
                            <v-row class="mt-1">
                                <v-col>
                                    <v-autocomplete
                                        density="compact"
                                        label="Search asset name or code"
                                        variant="outlined"
                                        :items="assetObj"
                                        @update:search="searchAssetFn"
                                        item-value="id"
                                        item-title="new_title"
                                        v-model="searchAsset"
                                        hide-details
                                        clearable
                                        return-object
                                    ></v-autocomplete>
                                </v-col>
                            </v-row>
                            <v-row style="max-height: 460px; overflow: auto">
                                <div
                                    class="v-col-12 v-col-md-12 text-caption py-1"
                                    v-for="(
                                        item, index
                                    ) in dataObj.display_assets"
                                    :key="index"
                                >
                                    - {{ item.asset_name }}
                                    <b>({{ item.asset_code }})</b>
                                    <v-icon
                                        @click="deleteData(index)"
                                        class="ml-2 cursor-pointer"
                                        color="error"
                                        :icon="mdiTrashCan"
                                    ></v-icon>
                                </div>
                            </v-row>
                        </v-card-text>
                        <v-card-action
                            class="mb-5"
                            style="position: absolute; bottom: 0"
                            v-if="!props.isView"
                        >
                            Note: Dont forget to click the save button
                        </v-card-action>
                    </div>
                  
                    <div
                        class="v-col-4 text-center"
                        style="
                            border-left: 1px solid #ccc;
                            background-color: #e0e0e0;
                        "
                        v-if="warrantyAsset"
                    >
                        <v-card-text v-if="props.dataObject.type">
                            <div class="d-flex mb-5 justify-center" v-if="!props.isView">
                                <Studio
                                    
                                    :options="{
                                        multiSelect: true,
                                        type: 'warranty',
                                    }"
                                    @select="studioSelectResponse"
                                />
                                <v-btn
                                
                                    color="primary"
                                    @click="saveImage"
                                    :disabled="selectedFiles?.length == 0"
                                    >Save Files</v-btn
                                >
                            </div>
                            <v-row v-if="selectedFiles?.length > 0" class="px-1">
                                <div
                                    v-for="(file, index) in selectedFiles"
                                    :key="file.id"
                                    class="v-col-12 v-col-md-4 pa-2"
                                    style="position: relative"
                                >
                                    <v-btn
                                    v-if="!props.isView"
                                        style="
                                            position: absolute;
                                            top: 0;
                                            right: 0;
                                            z-index: 1;
                                        "
                                        :icon="mdiClose"
                                        size="20px"
                                        color="error"
                                        @click="() => removeAttachment(file.id)"
                                    >
                                    </v-btn>
                                  
                                    <v-card
                                        @click="() => openAttachment(file)"
                                    >
                                        <v-img
                                        v-if="file.mime == 'image/jpeg' || file.mime == 'image/png'"
                                            :src="
                                                baseURL + '/file/' + file.path
                                            "
                                            cover
                                            aspect-ratio="1"
                                        >
                                        </v-img>
                                        <v-img v-else
                                        :src="
                                                baseURL + '/assets/images/pdf-image.png'
                                            "
                                            cover
                                            aspect-ratio="1"
                                        >  
                                        </v-img>
                                    </v-card>
                                    <small style="font-size:10px;line-height: 0;">{{ limitText(file.title, 25) }}</small>
                                </div>

                                <v-dialog
                                    v-model="dialogAttachment"
                                    width="95%"
                                    max-width="900"
                                >
                                    <v-card class="bg-black">
                                        
                                                <div
                                                    :style="`${fileType == 'pdf' ? 'height: 980px;' : 'height: 680px;'} 
                                                        width: 100%;
                                                        `
                                                    "
                                                    class="d-flex align-center justify-center"
                                                >
                                                    <v-img
                                                    v-if="fileType == 'jpg' || fileType == 'jpeg' || fileType == 'png'"
                                                        :src="
                                                            baseURL +
                                                            '/file/' +
                                                            currentSlider.path
                                                        "
                                                    ></v-img>
                                                    <object v-if="fileType == 'pdf'" :data="baseURL +
                                                            '/file/' +
                                                            currentSlider.path" type="application/pdf" width="100%" height="800px"> 
                                                    </object>
                                                </div>
                                           
                                    </v-card>
                                </v-dialog>
                            </v-row>
                            <v-row>
                                <div
                                    class="mb-5"
                                    style="position: absolute; bottom: 0"
                                    v-if="!props.isView"
                                >
                                    Note: Dont forget to click the save button
                                    at the top
                                </div>
                            </v-row>
                        </v-card-text>
                        <v-card-text v-else > 
                          
                            You can upload files on update only.<br/><br/>
                            kindly create new warranty first then click update/edit.
                          
                        </v-card-text>
                    </div>
                </v-row>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
import Studio from "@/studio/Studio.vue";
import { clientKey } from "@/services/axiosToken";
import { useAuthStore } from "@/stores/auth";
import { mdiTrashCan, mdiClose } from "@mdi/js";
import { limitText } from "@/composables/generateRandomString.js";
const authStore = useAuthStore();
const title = ref("");

const props = defineProps({
    dataObject: {
        type: Object,
        default: {},
    },
    addNewDialog: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "",
    },
    isView: {
        type: Boolean,
        default: true,
    },
    freeForm: {
        type: Array,
        default: [],
    },
});

const selectedFiles = ref([]);
const studioSelectResponse = (v) => {
    let fileExist = null;
    v.map((sudioFile) => {
        fileExist = selectedFiles.value.find((f) => f.id == sudioFile.id);
        if (!fileExist) {
            selectedFiles.value.push(sudioFile);
        }
    });
};

const assetObj = ref([]);
const searchAsset = ref("");
const dialogAttachment = ref(false);
const currentSlider = ref(1);
const fileType = ref('image');
const openAttachment = (file) => {
    
    let mimeType = file.path.split(".");
    fileType.value = mimeType[mimeType.length - 1];
  
    currentSlider.value = file;
    dialogAttachment.value = true;
};
const removeAttachment = (theId) => {
    selectedFiles.value = selectedFiles.value.filter((f) => f.id !== theId);
};


const selectedFilesIds = computed(() => selectedFiles.value.map((sf) => sf.id));
const saveImage = () => {
    let formObj = {
        id: parseInt(currentID.value),
        file_ids: selectedFilesIds.value,
        profile_id: authStore.user.profile.id,
    };
    clientKey(authStore.token)
        .post("/api/warranty/sync-images/store-update", formObj)
        .then((res) => {
            let msg = "Attachment has been updated";
            if (selectedFilesIds.value.length == 0) {
                msg = "Attachment has been deleted";
            }
            kpiEmit("fileSave", msg);
        })
        .catch((err) => {});
};

const deleteData = (index) => {
    
    let rows = dataObj.value.display_assets;
    let rows2 = dataObj.value.assets;

    rows.splice(index, 1);
    dataObj.value.display_assets = [...rows];

    rows2.splice(index, 1); 
    dataObj.value.assets = [...rows2]; 
 
};

const popDialog = ref(false);
const dataObj = ref({});

const freeForm = ref(props.freeForm);
watch(
    () => props.freeForm,
    (newVal) => {
        freeForm.value = [...[], ...newVal]; 
    }
);
const kpiEmit = defineEmits(["savedResponse", "fileSave", "save"]);

const cancelAddNew = () => {
    popDialog.value = false;
    dataObj.value = {};

    kpiEmit("cancelled", popDialog.value);
};

const saveData = () => {
    popDialog.value = false;

    kpiEmit("save", dataObj.value);
    dataObj.value = {};
};

const requiredFields = ref({});

freeForm.value.map((o, i) => {
    if (o.required) {
        requiredFields.value[o.name] = yup.string().required();
    }
});
let validation = yup.object(requiredFields.value);
const warrantyAsset = ref(false);

const fetchAssetWarranty = (id) => {
    selectedFiles.value = [];
    if(!id){
        return;
    }

    clientKey(authStore.token)
        .post("/api/fetch/warranty/by/id/" + id)
        .then((res) => {
            if (res?.data?.data?.pivot_assets?.length > 0) {
                res.data.data.pivot_assets.map((o, i) => {
                    dataObj.value.assets.push(o.id);
                    dataObj.value.display_assets.push(o);
                });
            }

            selectedFiles.value = res.data?.data?.attachment;
        })
        .catch((err) => {});
};
const baseURL = ref(window.location.origin);
const currentID = ref(0);
watch(
    () => props.addNewDialog,
    (newVal) => {
        warrantyAsset.value = false;
        dataObj.value = {};
        popDialog.value = newVal;
        title.value = "New " + props.title;
      
        if (props.dataObject.type) {
            dataObj.value = props.dataObject;
            title.value = "Edit " + props.title; 
        }
        if (props.title == "Warranty") {
                dataObj.value.assets = [];
                dataObj.value.display_assets = [];
                dataObj.value.pivot_assets = [];
                fetchAssetWarranty(dataObj.value.id);
                currentID.value = dataObj.value.id;
                warrantyAsset.value = true;
            }
    }
);

const searchAssetFn = (e) => {
    if (e.length < 4) {
        return;
    }

    clientKey(authStore.token)
        .get("/api/search-assets/warranty/" + e)
        .then((res) => {
            let mergeData = [];
            if (res.data && res.data.length > 0) {
                res.data.map((o, i) => {
                    mergeData[i] = o;
                    mergeData[i].new_title =
                        o.asset_name + " (" + o.asset_code + ")";
                });
            }
            assetObj.value = mergeData;
        })
        .catch((err) => {});
};

watch(
    () => searchAsset.value,
    (newVal) => {
        if (newVal) {
            if (!dataObj.value.assets.includes(newVal.id)) {
                dataObj.value.assets.push(newVal.id);
                dataObj.value.display_assets.push(newVal);
            }
            searchAsset.value = "";
        }

        console.log("dataObj.value", newVal);
    }
);
</script>
