<template>
    <div>
        <v-card>
            <v-card-text>
                <v-row>
                    <div
                        class="v-col-12 v-col-md-12 d-flex justify-space-between"
                    >
                        <div class="font-weight-bold">
                            SN: ISR-{{ pad(objData.id) }}
                        </div>
                        <div class="reported-by">
                            REPORTED BY:
                            {{ objData.profile?.display_name }}
                        </div>
                        <div class="reported-by">
                            DATE REPORTED: {{ useFormatDate(objData.created_at) }}
                        </div>
                    </div>
                     
                </v-row>
                <v-row>
                    <div class="v-col-12">
                        <v-row>
                            <div class="v-col-12 v-col-md-4 pb-0">
                                <v-autocomplete
                                    :items="priorityList"
                                    v-model="objData.priority"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    item-value="id"
                                    item-title="title"
                                    class="my-2"
                                    label="Priority*" 
                                >
                                </v-autocomplete>
                               
                                <v-autocomplete
                                    :items="facilities"
                                    v-model="objData.handled_by"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    item-value="id" 
                                    item-title="display_name"
                                    class="my-2"
                                    label="Handled By*"
                                >
                                </v-autocomplete>
                                <v-autocomplete
                                    :items="statusList"
                                    v-model="objData.status_id"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    item-value="id"
                                    item-title="title"
                                    class="mt-2"
                                    label="Status*"
                                >
                                </v-autocomplete>
                                <v-btn class="mt-3" size="small" :disabled="!objData.priority || !objData.handled_by || !objData.status_id" @click="submitForm" color="primary">Save</v-btn>
                            </div>
                            <div class="v-col-12 v-col-md-8">
                                <v-textarea
                                class="mt-2"
                                    variant="outlined"
                                    density="compact" 
                                    label="Add some remarks"
                                    v-model="remarks_data"
                                ></v-textarea>
                                <v-btn class="" size="small" :disabled="!objData.priority || !objData.handled_by || !objData.status_id" @click="submitRemarks" color="primary">Submit Remarks</v-btn>
                            </div>
                        </v-row>
                    </div>
                    <v-divider class="mt-3"></v-divider>
                    
                </v-row>
                <v-row>
                    <div class="v-col-12">
                        Remarks
                        <v-divider></v-divider>

                    </div>
                    <div class="v-col-12" style="max-height:400px; overflow: auto;">
                        <v-row v-for="item in objData.remarks" :key="id"> 
                            <div class="v-col-12 d-flex justify-space-between"><div><pre>{{ item.remarks }}</pre> </div> 
                                <div>{{ item.profile?.display_name }} - <small>{{ useFormatDateTime(item.created_at) }}</small></div></div>
                                <v-divider></v-divider>
                        </v-row>
                    </div>
                </v-row>
            </v-card-text>
        </v-card>
    </div>
</template>
<script setup>
import { ref, onMounted, watch } from "vue"; 
import { useAuthStore } from "@/stores/auth";
import { clientKey } from "@/services/axiosToken";
import { useFormatDate, useFormatDateTime } from "@/composables/formatDate.js";

const emit = defineEmits(["saved"]);
const props = defineProps({
    objectdata: {
        type: Object,
        default: {},
    },
});
const authStore = useAuthStore(); 
 
const objData = ref({});
const priorityList = ref([
    { id: 1, title: "1. Normal" },
    { id: 2, title: "2. Medium" },
    { id: 3, title: "3. High" },
]);

const facilities = ref([]);
const fetchFacilityTeam = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch/profile-facility/team")
        .then((res) => {
            facilities.value = res.data;
        })
        .catch((err) => {});
};

const remarks_data = ref('');

const submitForm = () => {  
    objData.value.profile_id = authStore.user.profile.id;
    clientKey(authStore.token)
        .post("/api/incident/update-facility-team", objData.value)
        .then((res) => { 
            emit("saved", res.data.message);
        })
        .catch((err) => {});
}

const submitRemarks = () => {  
    objData.value.profile_id = authStore.user.profile.id;
    let formData = {
        id: objData.value.id,
        profile_id: authStore.user.profile.id,
        remarks: remarks_data.value
    }
    clientKey(authStore.token)
        .post("/api/incident/update-facility-team-remarks", formData)
        .then((res) => {
            remarks_data.value = '';
            emit("saved", res.data.message);
        })
        .catch((err) => {});
}

const statusList = ref([]);
const fetchStatus = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch-global/incident-status/active")
        .then((res) => {
            statusList.value = res.data;
        })
        .catch((err) => {});
};

const pad = (v, size = 6) => {
    let s = "00000" + v;
    return s.substring(s.length - size);
};
onMounted(() => {
    fetchFacilityTeam();
    fetchStatus();
    objData.value = props.objectdata;
    objData.value.prev_handled_by = props.objectdata.handled_by;
     
    objData.value.priority = props.objectdata.priority ? parseInt(props.objectdata.priority) : null;
});
watch(  () => props.objectdata.remarks,  (newValue, oldValue) => {  
    objData.value.remarks = newValue;
    },  { deep: true });
</script>