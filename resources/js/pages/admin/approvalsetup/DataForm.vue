<template>
    <v-container>
        <AppPageHeader title="Global Approval Setup" />
        <v-row class="mb-3">
            <v-col class="v-col-12 mt-1 col-sm-12 py-0">
                <div class="mb-3">
                    <v-btn
                        :class="`${
                            isActive == 'request-asset' ? 'tab-active' : ''
                        }  v-col-12 v-col-md-3 mx-2`"
                        @click="changeType('request-asset')"
                        >Request Asset Approvals</v-btn
                    >
                    <v-btn
                        :class="`${
                            isActive == 'transfer-asset' ? 'tab-active' : ''
                        }   v-col-12 v-col-md-3 mx-2`"
                        @click="changeType('transfer-asset')"
                        >Transfer Asset Approvals</v-btn
                    >
                    <v-btn
                        class="v-col-12 v-col-md-3 mx-2"
                        color="primary"
                        @click="changeType('change-approval')"
                        >Change / Assign Approvals</v-btn
                    >
                </div>
                <v-card>
                    <Form
                        as="v-form"
                        :validation-schema="validation"
                        v-slot="{ meta }"
                    >
                        <v-card-title class="my-3 ml-3 text-uppercase">
                            {{ props.headertitle }}</v-card-title
                        >
                        <v-card-text>
                            <v-row>
                                <div class="v-col-12 d-flex my-auto">
                                    <Field
                                        name="Title"
                                        v-slot="{ field, errors }"
                                        v-model="dataObj.title"
                                    >
                                        <v-text-field
                                            v-model="dataObj.title"
                                            v-bind="field"
                                            :error-messages="errors"
                                            label="Title here*"
                                            variant="outlined"
                                            hide-details="auto"
                                            density="compact"
                                            class="v-col-10 pt-0"
                                        ></v-text-field>
                                    </Field>
                                    <v-btn
                                        size="small"
                                        class="mt-1"
                                        color="primary"
                                        :disabled="!meta.valid"
                                        :loading="btnLoading"
                                        @click="saveData('title')"
                                        >Save</v-btn
                                    >
                                </div>
                            </v-row>
                            <v-row v-if="isEdit">
                                <div class="v-col-12 px-6 pt-0 my-auto">
                                    <v-btn
                                        @click="addStages"
                                        color="primary"
                                        size="x-small"
                                        :icon="mdiPlus"
                                    ></v-btn>
                                    Add Signatories
                                </div>
                                <div class="v-col-12 px-6 pt-3">
                                    <v-row>
                                        <div
                                            class="v-col-1 font-weight-bold"
                                        ></div>
                                        <div class="v-col-3 font-weight-bold">
                                            TYPES
                                        </div>
                                        <div class="v-col-5 font-weight-bold">
                                            SIGNATORIES
                                        </div>
                                    </v-row>
                                    <v-divider></v-divider>

                                    <v-row class="mt-0">
                                        <div
                                            class="v-col-1 v-col-md-1 my-2 font-weight-bold"
                                        ></div>
                                        <div
                                            class="v-col-3 v-col-md-3 my-2 font-weight-bold"
                                        >
                                            Requestor
                                        </div>
                                        <div
                                            class="v-col-8 v-col-md-8 my-2 font-weight-bold"
                                        >
                                            Auto
                                        </div>
                                    </v-row>
                                    <v-row
                                        class="mt-0"
                                        v-for="(
                                            item, index
                                        ) in signatoriesObject"
                                        :key="index"
                                    >
                                        <div class="v-col-12 v-col-md-1">
                                            <v-text-field
                                                v-model="item.sort"
                                                label="Sort*"
                                                variant="outlined"
                                                hide-details="auto"
                                                density="compact"
                                            ></v-text-field>
                                        </div>
                                        <div class="v-col-12 v-col-md-3">
                                            <v-autocomplete
                                                label="Select Type"
                                                v-model="item.types"
                                                hide-details="auto"
                                                variant="outlined"
                                                item-value="id"
                                                item-title="value"
                                                density="compact"
                                                :items="typeItems"
                                            ></v-autocomplete>
                                        </div>

                                        <div
                                            class="v-col-12 v-col-md-6 d-flex my-auto"
                                        >
                                            <v-autocomplete
                                                label="Select Signatory"
                                                v-model="item.signatories"
                                                hide-details="auto"
                                                variant="outlined"
                                                density="compact"
                                                multiple
                                                item-value="id"
                                                item-title="display_name"
                                                :items="signatoryItems"
                                            ></v-autocomplete>
                                        </div>
                                        <div
                                            class="v-col-12 v-col-md-2 d-flex my-auto"
                                        >
                                            <v-btn
                                                :disabled="
                                                    !item.types ||
                                                    item.signatories.length == 0
                                                "
                                                size="x-small"
                                                @click="() => saveData(item)"
                                                :icon="mdiContentSave"
                                                :loading="btnLoading"
                                                color="success"
                                                class="mx-1 ml-3 my-auto"
                                                title="Save Signatory"
                                            >
                                            </v-btn>
                                            <v-btn
                                                size="x-small"
                                                @click="
                                                    () =>
                                                        deleteData(
                                                            item.id,
                                                            index
                                                        )
                                                "
                                                :icon="mdiTrashCan"
                                                color="error"
                                                class="mx-1 ml-3 my-auto"
                                                title="Delete Signatory"
                                            >
                                            </v-btn>
                                        </div>
                                    </v-row>
                                    <v-row class="mt-0">
                                        <div
                                            class="v-col-1 v-col-md-1 my-2 font-weight-bold"
                                        ></div>
                                        <div
                                            class="v-col-3 v-col-md-3 my-2 font-weight-bold"
                                        >
                                            Receiver
                                        </div>
                                        <div
                                            class="v-col-8 v-col-md-8 my-2 font-weight-bold"
                                        >
                                            Requestor
                                        </div>
                                    </v-row>
                                </div>
                            </v-row>
                        </v-card-text>
                    </Form>
                </v-card>
            </v-col>
        </v-row>
        <AppSnackBar :options="sbOptions" />
    </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import AppPageHeader from "@/components/ApppageHeader.vue";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter, useRoute } from "vue-router";
import { mdiTrashCan, mdiPlus, mdiContentSave } from "@mdi/js";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
import { clientKey } from "@/services/axiosToken";
const emit = defineEmits(["saved"]);
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

const signatoriesObject = ref([]);
const sbOptions = ref({});
const dataObj = ref({ title: props.objectdata.title });
const typeItems = ref([
    { id: "approve", value: "Approval" },
    { id: "releasing", value: "Asset Releasing" },
    { id: "receiver", value: "Asset Receiving" },
    { id: "reviewer", value: "Reviewer" },
    { id: "transport", value: "Transport Arrangement" },
    { id: "verify", value: "Verify" },
]);
const signatoryItems = ref([]);

const router = useRouter();
const route = useRoute();
const isActive = ref(route.params.type);
let validation = yup.object({
    Title: yup.string().required(),
});
 
const changeType = (type) => {
    isActive.value = type;
    router.push({ path: "/approval-setup/" + type });
  
    if(type == 'transfer-asset'){
        typeItems.value = typeItems.value.filter((o) => {
           return o.id !== 'releasing'
        })
    } 
};

const addSort = ref(0);
const addStages = () => {
    let dataForm = {
        id: route.params.id,
        sort: addSort.value,
        profile_id: authStore.user.profile.id,
        class: "new",
    };
    clientKey(authStore.token)
        .post("/api/approval-setups/store-signatory/update-data", dataForm)
        .then((res) => {
            signatoriesObject.value.push({
                id: res.data.id,
                sort: addSort.value,
                types: null,
                signatories: [],
            });
            addSort.value += 1;
            sbOptions.value = {
                status: true,
                type: "success",
                text: "New Signatory has been added",
            };
        })
        .catch((err) => {
            dataObj.value.loading = false;
        });
};

const fetchSignatories = async () => {
    await clientKey(authStore.token)
        .get("/api/user/fetch/signatories")
        .then((res) => {
            signatoryItems.value = res.data;
        })
        .catch((err) => {
            dataObj.value.loading = false;
            console.log(err);
        });
};

const btnLoading = ref(false);
const isEdit = ref(route.params.id ? true : false);

const deleteData = (id, index) => {

    let dataForm = {
        id: route.params.id,
        profile_id: authStore.user.profile.id,
        stageID: id,
        class: "deleted",
    };

    signatoriesObject.value.splice(index, 1);

    clientKey(authStore.token)
        .post("/api/approval-setups/store-signatory/update-data", dataForm)
        .then((res) => {
            sbOptions.value = {
                status: true,
                type: "success",
                text: "Signatory has been deleted.",
            };
        })
        .catch((err) => {
            dataObj.value.loading = false;
        });
};

const saveData = (data) => {
    btnLoading.value = true;
    sbOptions.value = {
        status: true,
        type: "info",
        text: "Please wait...",
    };

    let type = "transfer";
    if (isActive.value == "request-asset") {
        type = "request";
    }
    let id = null;
    if (route.params.id) {
        id = route.params.id;
    }
    let submitData = {
        id: id,
        type: type,
        signatories: data,
        profile_id: authStore.user.profile.id,
        class: "update",
    };
    let apiURL = "/api/approval-setups/store-signatory/update-data";

    if (data == "title") {
        apiURL = "/api/approval-setups/store-update/data";

        submitData = {
            id: id,
            type: type,
            title: dataObj.value.title,
            signatories: signatoriesObject.value,
            profile_id: authStore.user.profile.id,
        };
    }

    clientKey(authStore.token)
        .post(apiURL, submitData)
        .then((res) => {
            
            sbOptions.value = {
                status: true,
                type: "success",
                text: "Data has been saved.",
            };

            btnLoading.value = false;
            signatoriesObject.value.sort((a, b) => a.sort - b.sort);
            if (!route.params.id) {
                setTimeout(() => {
                    router
                        .push({
                            name: "EditApproval",
                            params: {
                                id: res.data.id,
                            },
                        })
                        .catch((err) => {});
                }, 800);
            }
        })
        .catch((err) => {
            dataObj.value.loading = false;
        });
};

onMounted(() => {
    fetchSignatories();

    if (route.params.id) {
       
        if(isActive.value == 'transfer-asset'){
            typeItems.value = typeItems.value.filter((o) => {
            return o.id !== 'releasing'
            })
        }

        if (props.objectdata.stages.length > 0) {
            let lastSortNumber =
                props.objectdata.stages[props.objectdata.stages.length - 1]
                    .sort;
            props.objectdata.stages.map((o, i) => {
                let sign = [];
                o.signatures.map((oo) => {
                    if (oo.profile_id != 0) {
                        sign.push(oo.profile_id);
                    }
                });

                signatoriesObject.value.push({
                    id: o.id,
                    sort: o.sort,
                    types: o.types,
                    signatories: sign,
                });
            });

            addSort.value = lastSortNumber + 1;
        }
    }
});
</script>

<style lang="scss" scoped>
.tab-active {
    background-color: #c6a92d;
    color: #ffffff;
}
</style>
