<template>
    <v-row>
        <div class="v-col-12 pt-0 pb-2">
            <v-btn
                @click="() => openWarrantyDialog()"
                class="mb-3"
                size="small"
            >
                New Warranty <v-icon class="ml-2" :icon="mdiPlus"></v-icon>
            </v-btn>
            <v-card>
                <v-table density="compact">
                    <thead>
                        <tr>
                            <th class="text-left text-primary">#</th>
                            <th class="text-left text-primary">Title</th>
                            <th class="text-left text-primary">Vendor</th>
                            <th class="text-left text-primary">
                                Warranty Start
                            </th>
                            <th class="text-left text-primary">Warranty End</th>
                            <th class="text-left text-primary">AMC Start</th>
                            <th class="text-left text-primary">AMC End</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in warranties"
                            :key="item.id"
                            @click="() => openWarrantyDialog(item)"
                            class="cursor-pointer table-cell-hover"
                        >
                            <td>{{ index + 1 }}</td>
                            <td>{{ item.warranty_title }}</td>
                            <td>{{ item.vendor && item.vendor.title }}</td>
                            <td>
                                {{
                                    item.warranty_start_date && item.warranty_start_date != '0000-00-00' ?
                                    dayjs(item.warranty_start_date).format(
                                        "MMM. DD, YYYY"
                                    ) : ''
                                }}
                            </td>
                            <td>
                                {{
                                    item.warranty_end_date && item.warranty_end_date != '0000-00-00' ?
                                    dayjs(item.warranty_end_date).format(
                                        "MMM. DD, YYYY"
                                    ) : ''
                                }}
                            </td>

                            <td>
                                {{
                                    item.amc_start_date && item.amc_start_date != '0000-00-00' ?
                                    dayjs(item.amc_start_date).format(
                                        "MMM. DD, YYYY"
                                    ) : ''
                                }}
                            </td>
                            <td>
                                {{
                                    item.amc_end_date && item.amc_end_date != '0000-00-00' ?
                                    dayjs(item.amc_end_date).format(
                                        "MMM. DD, YYYY"
                                    ): ''
                                }}
                            </td>
                        </tr>
                    </tbody>
                </v-table>

                <v-sheet v-if="warranties?.length == 0" class="pa-3 text-center"
                    >No record at the moment</v-sheet
                >
            </v-card>
        </div>
        <v-dialog v-model="dialogWarranty.status" persistent width="600">
            <v-card>
                <v-card-title
                    class="text-capitalize mb-3 d-flex justify-space-between align-center"
                >
                    {{ dialogWarranty.action + " Warranty" }}
                    <v-btn
                        :icon="mdiClose"
                        variant="flat"
                        size="small"
                        @click="dialogWarranty.status = false"
                    ></v-btn>
                </v-card-title>
                <v-card-text>
                    <Form
                        as="v-form" 
                    >
                        <v-row class="mb-0">
                            <div class="v-col-12 v-col-md-12 pt-0 pb-2">
                                 
                                    <v-autocomplete
                                        v-model="dialogWarranty.data.warranty_id"
                                        :items="allWarranties"
                                        item-title="warranty_title"
                                        item-value="id"
                                        label="Select Warranty"
                                        density="compact"
                                        variant="outlined" 
                                    />
                               
                            </div>

                            <div class="v-col-12 pt-0 pb-2 d-flex justify-end">
                                <v-btn
                                    color="primary"
                                    :loading="dialogWarranty.loading"
                                    @click="saveWarranty"
                                    :disabled="!dialogWarranty.data.warranty_id"
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
import { Form, Field } from "vee-validate";
import { clientKey } from "@/services/axiosToken";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { mdiPlus, mdiClose } from "@mdi/js";
import dayjs from "dayjs";
import * as yup from "yup";
import { useRoute } from "vue-router";

const route = useRoute();

// authStore
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

// snackbar
const sbOptions = ref({});

const props = defineProps({
    page: {
        type: String,
        default: "add",
    },
    asset: {
        type: Object,
        default: null,
    },
}); 

// warranty
const dialogWarranty = ref({
    status: false,
    action: "add",
    loading: false,
    data: {},
});
const warranties = ref([]);

watch(
    () => props.asset,
    (newVal) => {
        warranties.value = newVal.pivot_warranties;
        fetchAllWarranties();
    }
);

const oldWarrantyID = ref(0);
const openWarrantyDialog = (warranty = null) => {
    dialogWarranty.value.status = true;
    if (warranty) {
        dialogWarranty.value.action = "edit";
        dialogWarranty.value.data = Object.assign({}, warranty);
        oldWarrantyID.value = warranty.id;
    } else {
        dialogWarranty.value.action = "add";
        dialogWarranty.value.data = {};
    }
};
const saveWarranty = async () => {
    dialogWarranty.value.loading = true;
    let data = {
        id: dialogWarranty.value.data.id,
        asset_id: route.params.id,
        warranty_id: dialogWarranty.value.data.warranty_id,
        old_warranty_id: oldWarrantyID.value,
    };
    await clientKey(authStore.token)
        .post("/api/asset/save-warranty", data)
        .then((res) => {
            getWarranties().then(() => {
                sbOptions.value = {
                    status: true,
                    type: "success",
                    text: res.data.message,
                };
                dialogWarranty.value.loading = false;
                dialogWarranty.value.status = false;
            });
        })
        .catch((err) => {
            dialogWarranty.value.loading = false;
            sbOptions.value = {
                status: true,
                type: "eroor",
                text: "Error while saving warranty",
            };
            console.log("err", err);
        });
};

const allWarranties = ref([]);
const fetchAllWarranties = () => {
    clientKey(authStore.token)
        .get("/api/fetch-warranties/non-paginated")
        .then((res) => {
            allWarranties.value = res.data;
        })
        .catch((err) => {
            dialogWarranty.value.loading = false;
        });
};

const getWarranties = async () => {
    await clientKey(authStore.token)
        .get("/api/asset/warranty/" + route.params.id)
        .then((res) => {
            warranties.value = res.data;
            dialogWarranty.value.loading = false;
        })
        .catch((err) => {
            dialogWarranty.value.loading = false;
        });
};
</script>
