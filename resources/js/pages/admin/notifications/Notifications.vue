<template>
    <v-container>
        <AppPageHeader title="Notifications - Setup" />

        <v-row class="mb-3">
            <v-col class="v-col-12 mt-1 col-sm-12">
                <v-card class="px-5" :loading="page.loading">
                    <v-card-text>
                        <v-btn
                            @click="saveData()"
                            :loading="btnLoading"
                            size="small"
                            color="secondary"
                            v-if="
                                authStore.user.role == 'superadmin' ||
                                authStore.capabilities?.includes('edit')
                            "
                            >Save</v-btn
                        >
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col class="v-col-12 v-col-md-6 mt-1 col-sm-12">
                <v-card class="px-5" :loading="page.loading">
                    <v-card-text>
                        <v-row class="pb-2">
                            <div class="v-col-12 font-weight-bold">
                                MAINTENANCE - RECEIVERS
                            </div>
                            <v-divider></v-divider>
                            <div class="v-col-12 pt-3">
                                <v-text-field
                                    hide-details="auto"
                                    v-model="dataObj[0].meta_value"
                                    label="Add Email"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </div>
                            <div class="v-col-12">
                                <v-text-field
                                    hide-details="auto"
                                    v-model="dataObj[1].meta_value"
                                    label="Add Email"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </div>
                            <div class="v-col-12">
                                <v-text-field
                                    hide-details="auto"
                                    v-model="dataObj[2].meta_value"
                                    label="Add Email"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </div>
                            <div class="v-col-12">
                                <v-text-field
                                    hide-details="auto"
                                    v-model="dataObj[3].meta_value"
                                    label="Add Email"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </div>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col class="v-col-12 v-col-md-6 mt-1 col-sm-12">
                <v-card class="px-5" :loading="page.loading">
                    <v-card-text>
                        <v-row class="pb-2">
                            <div class="v-col-12 font-weight-bold">
                                INCIDENT - RECEIVERS
                            </div>
                            <v-divider></v-divider>
                            <div class="v-col-12 pt-3">
                                <v-text-field
                                    hide-details="auto"
                                    v-model="dataObj[4].meta_value"
                                    label="Add Email"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </div>
                            <div class="v-col-12">
                                <v-text-field
                                    hide-details="auto"
                                    v-model="dataObj[5].meta_value"
                                    label="Add Email"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </div>
                            <div class="v-col-12">
                                <v-text-field
                                    hide-details="auto"
                                    v-model="dataObj[6].meta_value"
                                    label="Add Email"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </div>
                            <div class="v-col-12">
                                <v-text-field
                                    hide-details="auto"
                                    v-model="dataObj[7].meta_value"
                                    label="Add Email"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </div>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col class="v-col-12 v-col-md-12 mt-1 col-sm-12">
                <v-card class="px-5" :loading="page.loading">
                    <v-card-text>
                        <v-row class="pb-2">
                            <div class="v-col-12 font-weight-bold">
                                NOTIFICATION INTERVAL - FOR MAINTENANCE
                            </div>
                            <v-divider></v-divider>
                            <div class="v-col-12">
                                <v-row>
                                    <div class="v-col-12 v-col-md-6">
                                        <v-row>
                                            <div class="v-col-4">
                                                <v-text-field
                                                    type="number"
                                                    hide-details="auto"
                                                    v-model="
                                                        dataObj[8].meta_value
                                                    "
                                                    label="Days"
                                                    variant="outlined"
                                                    density="compact"
                                                ></v-text-field>
                                                Days before expiration
                                            </div>
                                            <div class="v-col-8 mt-2">
                                                Asset Expiration - First
                                                Notification
                                            </div>
                                        </v-row>
                                        <v-row>
                                            <div class="v-col-4">
                                                <v-text-field
                                                    type="number"
                                                    hide-details="auto"
                                                    v-model="
                                                        dataObj[9].meta_value
                                                    "
                                                    label="Days"
                                                    variant="outlined"
                                                    density="compact"
                                                ></v-text-field>
                                                Day(s) before expiration
                                            </div>
                                            <div class="v-col-8 mt-2">
                                                Asset Expiration - Second
                                                Notification
                                            </div>
                                        </v-row>
                                        <v-row>
                                            <div class="v-col-4">
                                                <v-text-field
                                                    type="number"
                                                    hide-details="auto"
                                                    v-model="
                                                        dataObj[10].meta_value
                                                    "
                                                    label="Days"
                                                    variant="outlined"
                                                    density="compact"
                                                ></v-text-field>
                                                Day(s) before expiration
                                            </div>
                                            <div class="v-col-8 mt-2">
                                                Asset Expiration - Third
                                                Notification
                                            </div>
                                        </v-row>
                                    </div>
                                </v-row>
                            </div>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <AppSnackbar :options="sbOptions" />
    </v-container>
</template>
<script setup>
import { ref } from "vue";

import { useAuthStore } from "@/stores/auth";
import { clientKey } from "@/services/axiosToken";
import AppPageHeader from "@/components/ApppageHeader.vue";
import AppSnackbar from "@/components/AppSnackBar.vue";

const authStore = useAuthStore();
const btnLoading = ref(false);

const dataObj = ref([
    {meta_type: 'maintenance-receiver', 'meta_value' : "", profile_id: authStore.user.profile.id},
    {meta_type: 'maintenance-receiver', 'meta_value' : "", profile_id: authStore.user.profile.id},
    {meta_type: 'maintenance-receiver', 'meta_value' : "", profile_id: authStore.user.profile.id},
    {meta_type: 'maintenance-receiver', 'meta_value' : "", profile_id: authStore.user.profile.id},
    {meta_type: 'incident-receiver', 'meta_value' : "", profile_id: authStore.user.profile.id},
    {meta_type: 'incident-receiver', 'meta_value' : "", profile_id: authStore.user.profile.id},
    {meta_type: 'incident-receiver', 'meta_value' : "", profile_id: authStore.user.profile.id},
    {meta_type: 'incident-receiver', 'meta_value' : "", profile_id: authStore.user.profile.id},
    {meta_type: 'first_notification', 'meta_value' : 30, profile_id: authStore.user.profile.id},
    {meta_type: 'second_notification', 'meta_value' : 15, profile_id: authStore.user.profile.id},
    {meta_type: 'third_notification', 'meta_value' : 1, profile_id: authStore.user.profile.id},
    {meta_type: 'request_transfer', 'meta_value' : 2, profile_id: authStore.user.profile.id},
    {meta_type: 'incidents', 'meta_value' : 1, profile_id: authStore.user.profile.id},
]);


const page = ref({ loading: true });
const sbOptions = ref({
    status: false,
    type: "info",
    text: null,
});
const fetchData = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch/notification-setup")
        .then((res) => {
            dataObj.value = [
                {meta_type: 'maintenance-receiver', 'meta_value' : res.data[0].meta_value, profile_id: authStore.user.profile.id},
                {meta_type: 'maintenance-receiver', 'meta_value' : res.data[1].meta_value, profile_id: authStore.user.profile.id},
                {meta_type: 'maintenance-receiver', 'meta_value' : res.data[2].meta_value, profile_id: authStore.user.profile.id},
                {meta_type: 'maintenance-receiver', 'meta_value' : res.data[3].meta_value, profile_id: authStore.user.profile.id},
                {meta_type: 'incident-receiver', 'meta_value' : res.data[4].meta_value, profile_id: authStore.user.profile.id},
                {meta_type: 'incident-receiver', 'meta_value' : res.data[5].meta_value, profile_id: authStore.user.profile.id},
                {meta_type: 'incident-receiver', 'meta_value' : res.data[6].meta_value, profile_id: authStore.user.profile.id},
                {meta_type: 'incident-receiver', 'meta_value' : res.data[7].meta_value, profile_id: authStore.user.profile.id},
                {meta_type: 'first_notification', 'meta_value' : res.data[8].meta_value, profile_id: authStore.user.profile.id},
                {meta_type: 'second_notification', 'meta_value' : res.data[9].meta_value, profile_id: authStore.user.profile.id},
                {meta_type: 'third_notification', 'meta_value' : res.data[10].meta_value, profile_id: authStore.user.profile.id},
                {meta_type: 'request_transfer', 'meta_value' : res.data[11].meta_value, profile_id: authStore.user.profile.id},
                {meta_type: 'incidents', 'meta_value' : res.data[12].meta_value, profile_id: authStore.user.profile.id},
            ];

            page.value.loading = false;
        })
        .catch((err) => {
            page.value.loading = false;
            console.log(err);
        });
};
fetchData();

const saveData = async function () {
    btnLoading.value = true;

    let formData = {data: dataObj.value, profile_id: authStore.user.profile.id}
    await clientKey(authStore.token)
        .post("/api/store-update/notification", formData)
        .then((res) => {
            sbOptions.value = {
                status: true,
                type: "success",
                text: res.data.message,
            };

            btnLoading.value = false;
        })
        .catch((err) => {
            btnLoading.value = false;
            console.log(err);
        });
};
</script>
