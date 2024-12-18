<template>
    <v-row>
        <div class="v-col-12 pt-0 pb-2">
            <v-btn
                @click="() => openDialogHistory()"
                class="mb-3"
                size="small"
                v-if="route.name != 'view-asset'"
            >
                ADD HISTORY <v-icon class="ml-2" :icon="mdiPlus"></v-icon>
            </v-btn>
            <v-card>
                <v-table density="compact">
                    <thead>
                        <tr>
                            <th class="text-left text-primary">#</th>
                            <th class="text-left text-primary">
                                Transferred from
                            </th>
                            <th class="text-left text-primary">Remarks</th>
                            <th class="text-left text-primary">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in historyLocation"
                            :key="item.id"
                        >
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.location?.title }}</td>
                            <td>{{ item.remarks }}</td>
                            <td>{{ useFormatDate(item.created_at) }}</td>
                        </tr>
                    </tbody>
                </v-table>
                <v-sheet
                    v-if="
                        props.asset.allotted_informations &&
                        props.asset.allotted_informations.length == 0
                    "
                    class="pa-3 text-center"
                    >No record at the moment</v-sheet
                >
            </v-card>
        </div>

        <v-dialog v-model="dialogHistory.status" persistent width="600">
            <v-card>
                <v-card-title
                    class="text-capitalize mb-3 d-flex justify-space-between align-center"
                >
                    ADD HISTORY
                    <v-btn
                        :icon="mdiClose"
                        variant="flat"
                        size="small"
                        @click="dialogHistory.status = false"
                    ></v-btn>
                </v-card-title>
                <v-card-text>
                    <Form as="v-form">
                        <v-row class="mb-0">
                            <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                                <v-autocomplete
                                    v-model="dialogHistory.data.location_id"
                                    :items="locationStore.list"
                                    item-title="title"
                                    item-value="id"
                                    label="Previous Location*"
                                    density="compact"
                                    variant="outlined"
                                />
                            </div>
                            <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                                <v-text-field
                                    v-model="dialogHistory.data.remarks"
                                    label="Remarks"
                                    density="compact"
                                    variant="outlined"
                                />
                            </div>
                            <div class="v-col-12 pt-0 pb-2 d-flex justify-end">
                                <v-btn
                                    color="primary"
                                    :loading="dialogHistory.loading"
                                    @click="saveData"
                                    :disabled="!dialogHistory.data.location_id"
                                    >Save</v-btn
                                >
                            </div>
                        </v-row>
                    </Form>
                </v-card-text>
            </v-card>
        </v-dialog>
        <AppSnackBar :options="sbOptions" />
    </v-row>
</template>

<script setup>
import { ref, watch } from "vue";
import { useFormatDate } from "@/composables/formatDate.js";
import { useRoute } from "vue-router";
// locations
import { useLocationStore } from "@/stores/locations";
// authStore
import { useAuthStore } from "@/stores/auth";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { clientKey } from "@/services/axiosToken";
const authStore = useAuthStore();
const locationStore = useLocationStore();
const sbOptions = ref({});
locationStore.getLocations(authStore.token);
const dialogHistory = ref({
    status: false,
    loading: false,
    data: {},
});
const route = useRoute();
const props = defineProps({
    asset: {
        type: Object,
        default: null,
    },
});

const openDialogHistory = () => {
    dialogHistory.value.status = true;
    dialogHistory.value.data = {};
};

const saveData = async () => {
    dialogHistory.value.loading = true;
    let data = {
        data: {
            asset_id: route.params.id,
            remarks: dialogHistory.value.data.remarks,
            location_id: dialogHistory.value.data.location_id,
        },
    };

    sbOptions.value = {
        status: true,
        type: "info",
        text: "Please wait...",
    };

    await clientKey(authStore.token)
        .post("/api/asset/save-history", data)
        .then((res) => {
            sbOptions.value = {
                status: true,
                type: "success",
                text: "History location has been added.",
            };
            console.log("res",res);
            historyLocation.value = res?.data?.response?.allotted_informations;
            dialogHistory.value.loading = false;
            dialogHistory.value.status = false;
        })
        .catch((err) => {
            dialogHistory.value.loading = false;
            sbOptions.value = {
                status: true,
                type: "eroor",
                text: "Error while saving history",
            };
            console.log("err", err);
        });
};

const historyLocation = ref([]);
watch(
    () => props.asset,
    (newVal) => {
        historyLocation.value = newVal.allotted_informations;
    }
);

watch(
    () => dialogHistory.value.location_id,
    (newVal) => {
        dialogHistory.value.data = { location_id: newVal };
    }
);
</script>
