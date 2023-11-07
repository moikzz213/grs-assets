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
                                    :disabled="loggedRole == 'facility'"
                                >
                                </v-autocomplete>
                               
                                <v-autocomplete
                                    :items="facilities"
                                    v-model="objData.handled_by"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    item-value="id"
                                    :disabled="loggedRole == 'facility'"
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
                            </div>
                            <div class="v-col-12 v-col-md-8">
                                <v-textarea
                                class="my-2"
                                    variant="outlined"
                                    density="compact"
                                    label="Remarks"
                                    v-model="objData.remarks"
                                ></v-textarea>
                            </div>
                        </v-row>
                    </div>
                    <v-divider class="mt-3"></v-divider>
                    <div class="v-col-12">
                        <v-btn class="" size="small" @click="submitForm" color="primary">Submit</v-btn>
                    </div>
                </v-row>
            </v-card-text>
        </v-card>
    </div>
</template>
<script setup>
import { ref, onMounted } from "vue";

import { useAuthStore } from "@/stores/auth";
import { clientKey } from "@/services/axiosToken";
import { useFormatDate } from "@/composables/formatDate.js";

const emit = defineEmits(["deleted"]);
const props = defineProps({
    objectdata: {
        type: Object,
        default: {},
    },
});
const authStore = useAuthStore();
const loggedRole = ref(authStore.user.profile.role);
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

const submitForm = () => {  
    objData.value.profile_id = authStore.user.profile.id;
    clientKey(authStore.token)
        .post("/api/incident/update-facility-team", objData.value)
        .then((res) => {
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
    objData.value.priority = parseInt(props.objectdata.priority);
});
</script>
