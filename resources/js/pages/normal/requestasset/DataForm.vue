<template>
  <v-container>
    <AppPageHeader :title="props.headertitle" />
    <v-row class="mb-3">
      <v-col class="v-col-12 mt-1 col-sm-12 py-0">
        <v-card>
          <v-card-title
            class="my-0 ml-3 text-uppercase text-h6 d-flex justify-space-between font-weight-bold"
          >
            <div>{{ props.headertitle }}</div>
            <div v-if="!hasSignatories" class="text-error text-h6">
              {{ errorMsg }}
            </div>
            <div
              v-if="isEdit"
              :class="
                formObjData.status == 'cancelled'
                  ? 'text-error'
                  : formObjData.status == 'complete'
                  ? 'text-success'
                  : ''
              "
            >
              STATUS ({{ formObjData.status }})
            </div>

            <div>DATE: {{ useFormatDate(currentDate) }}</div>
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text>
            <v-row>
              <div
                :class="`${formObjData.status == 'complete' ? 'v-col-12' : 'v-col-4'}`"
                v-if="isEdit"
              >
                REQUEST NO: SN-5{{ pad(formObjData.id) }}
              </div>
              <div
                class="v-col-8 text-right"
                v-if="
                  isEdit &&
                  (formObjData.status == 'pending' || formObjData.status == 'cancelled')
                "
              >
                <v-btn
                  v-if="formObjData.status == 'pending'"
                  @click="changeRequestStatus('cancelled')"
                  color="warning"
                  >Cancel request</v-btn
                >
                <v-btn
                  v-if="formObjData.status == 'cancelled'"
                  @click="changeRequestStatus('pending')"
                  size="small"
                  color="info"
                  >Change status to Pending</v-btn
                >
              </div>
              <div class="v-col-12 v-col-md-6">
                <v-autocomplete
                  :items="requestTypeList"
                  v-model="objData.request_type_id"
                  variant="outlined"
                  density="compact"
                  hide-details
                  item-value="id"
                  :disabled="isEdit"
                  item-title="title"
                  label="TYPE OF REQUEST*"
                  @update:modelValue="setupApprovals"
                ></v-autocomplete>
              </div>
              <div class="v-col-12 v-col-md-6">
                <v-autocomplete
                  :items="companyList"
                  v-model="objData.company_id"
                  variant="outlined"
                  density="compact"
                  hide-details
                  item-value="id"
                  item-title="title"
                  label="COMPANY*"
                ></v-autocomplete>
              </div>
              <div class="v-col-12 v-col-md-6">
                <v-autocomplete
                  :items="locationList"
                  v-model="objData.transferred_from"
                  variant="outlined"
                  density="compact"
                  hide-details
                  item-value="id"
                  item-title="title"
                  label="LOCATION FROM*"
                ></v-autocomplete>
              </div>
              <div class="v-col-12 v-col-md-6">
                <v-autocomplete
                  :items="locationList"
                  v-model="objData.transferred_to"
                  variant="outlined"
                  density="compact"
                  hide-details
                  item-value="id"
                  item-title="title"
                  label="LOCATION TO*"
                ></v-autocomplete>
              </div>
              <div class="v-col-12">
                <v-text-field
                  v-model="objData.subject"
                  variant="outlined"
                  density="compact"
                  hide-details
                  clearable
                  label="SUBJECT*"
                ></v-text-field>
              </div>
            </v-row>
          </v-card-text>
        </v-card>
        <v-card class="my-2">
          <v-card-text>
            <v-row>
              <div class="v-col-12">
                <v-btn
                  color="primary"
                  @click="AddAsset"
                  v-if="
                    formObjData.status != 'complete' &&
                    (!route.params.id ||
                      props.objectdata?.status == 'pending' ||
                      authStore.user.profile.role == 'superadmin' ||
                      authStore.user.profile.role == 'administrator')
                  "
                  >Add</v-btn
                >
              </div>
            </v-row>
            <v-row v-for="(item, index) in assetDataObj" :key="index">
              <div class="v-col-12 v-col-md-1 px-1">
                <v-row v-if="item.attachment?.id" class="px-1">
                  <div class="v-col-12 v-col-md-12 pa-2" style="position: relative">
                    <v-text-field
                      style="display: none"
                      type="hidden"
                      class="hidden"
                      v-model="item.attachment.id"
                    ></v-text-field>
                    <v-btn
                      style="position: absolute; top: 0; right: 0; z-index: 1"
                      :icon="mdiClose"
                      size="16px"
                      color="error"
                      @click="() => removeAttachment(index, item.attachment.id)"
                    >
                    </v-btn>
                    <v-card
                      @click="() => openAttachment(index)"
                      maxHeight="40"
                      style="
                        background-image: url('/assets/images/fav.png');
                        background-size: cover;
                        height: 100px;
                        width: 100px;
                      "
                    >
                    </v-card>
                  </div>

                  <v-dialog v-model="dialogAttachment" width="95%" max-width="900">
                    <v-card class="bg-black">
                      <v-carousel
                        hide-delimiter-background
                        show-arrows="hover"
                        height="680px"
                        v-model="currentSlider"
                      >
                        <v-carousel-item reverse-transition="fade" transition="fade">
                          <div
                            style="height: 680px; width: 100%"
                            class="d-flex align-center justify-center"
                          >
                            <v-img
                              :src="baseURL + '/file/' + item.attachment.path"
                            ></v-img>
                          </div>
                        </v-carousel-item>
                      </v-carousel>
                    </v-card>
                  </v-dialog>
                </v-row>
                <v-row v-else class="mt-0">
                  <div class="v-col-12 pt-0 pb-0">
                    <v-sheet color="grey-lighten-4" class="text-center">
                      <Studio
                        :options="{
                          multiSelect: false,
                          type: 'request-asset',
                        }"
                        @select="(e) => studioSelectResponse(index, e)"
                      />
                    </v-sheet>
                  </div>
                </v-row>
              </div>
              <div class="v-col-12 v-col-md-3">
                <v-text-field
                  v-model="item.item_description"
                  variant="outlined"
                  density="compact"
                  hide-details
                  clearable
                  label="ITEM DESC*"
                  @update:modelValue="requiredData"
                ></v-text-field>
              </div>
              <div class="v-col-12 v-col-md-2">
                <v-text-field
                  v-model="item.asset_code"
                  variant="outlined"
                  density="compact"
                  hide-details
                  label="ASSET CODE"
                ></v-text-field>
              </div>
              <div class="v-col-12 v-col-md-1">
                <v-text-field
                  type="number"
                  v-model="item.qty"
                  variant="outlined"
                  density="compact"
                  hide-details
                  label="QTY"
                ></v-text-field>
              </div>
              <div class="v-col-12 v-col-md-1">
                <v-text-field
                  type="number"
                  v-model="item.weight"
                  variant="outlined"
                  density="compact"
                  hide-details
                  label="WEIGHT(KG)"
                ></v-text-field>
              </div>
              <div class="v-col-12 v-col-md-1">
                <v-text-field
                  type="number"
                  v-model="item.item_value"
                  variant="outlined"
                  density="compact"
                  hide-details
                  label="VALUE"
                ></v-text-field>
              </div>
              <div class="v-col-12 v-col-md-3 d-flex">
                <v-text-field
                  v-model="item.reason_for_request"
                  variant="outlined"
                  density="compact"
                  hide-details
                  label="REASON"
                ></v-text-field>
                <v-icon
                  size="small"
                  @click="() => deleteData(item.id, index)"
                  :icon="mdiTrashCan"
                  class="my-auto ml-2"
                  v-if="
                    !isEdit ||
                    (isEdit &&
                      authStore.user.profile.id == props.objectdata.profile_id &&
                      props.objectdata?.status == 'pending')
                  "
                  color="error"
                />
              </div>
            </v-row>
          </v-card-text>
        </v-card>
        <v-card class="my-2">
          <v-card-text>
            <v-row>
              <div class="v-col-12 font-weight-bold text-uppercase">APPROVAL SETUP</div>
              <v-divider></v-divider>
            </v-row>
            <v-row>
              <div class="v-col-12 v-col-md-2">Requestor</div>
              <div class="v-col-12 v-col-md-6">
                {{ formObjData?.profile?.display_name }}
              </div>
              <div class="v-col-12 v-col-md-1">Status</div>
              <div class="v-col-12 v-col-md-2">Date Approved</div>
            </v-row>
            <template v-if="hasSignatories">
              <v-row v-for="(item, index) in approvalSetupList" :key="item.id">
                <div class="v-col-12 v-col-md-2">
                  {{
                    item.types ? statusTitle(item.types) : statusTitle(item.approval_type)
                  }}
                </div>
                <div class="v-col-12 v-col-md-7 d-flex">
                  <v-autocomplete
                    :items="item.signatures"
                    v-model="item.profile_id"
                    variant="outlined"
                    density="compact"
                    hide-details
                    item-value="id"
                    item-title="display_name"
                    label="SELECT SIGNATORY*"
                    @update:modelValue="requiredData"
                    :disabled="item.status == 'done'"
                  >
                  </v-autocomplete>
                  <v-btn variant="text" size="x-small">
                    <v-icon
                      v-if="item.status"
                      class="mx-2 my-3"
                      :icon="
                        item.status == 'awaiting-approval'
                          ? mdiAccountClockOutline
                          : item.status == 'pending'
                          ? mdiAccountDetails
                          : item.status == 'reject'
                          ? mdiAccountCancel
                          : mdiAccountCheck
                      "
                      :color="
                        item.status == 'awaiting-approval'
                          ? 'info'
                          : item.status == 'pending'
                          ? ''
                          : item.status == 'reject'
                          ? 'error'
                          : 'success'
                      "
                    >
                    </v-icon>

                    <v-tooltip activator="parent" location="top" max-width="400">{{
                      item.status == "awaiting-approval"
                        ? "Awaiting Approval"
                        : item.status == "pending"
                        ? "Pending"
                        : item.status == "reject"
                        ? item.reason_rejected
                        : "Already Signed"
                    }}</v-tooltip>
                  </v-btn>
                </div>
                <div class="my-auto">
                  {{ item.date_approved ? useFormatDate(item.date_approved) : "" }}
                </div>
              </v-row>
              <v-row>
                <v-divider></v-divider>
                <div class="v-col-12">
                  <v-btn
                    :disabled="!isValidate"
                    @click="submitRequest"
                    color="primary"
                    v-if="
                      (!route.params.id ||
                        props.objectdata?.status == 'pending' ||
                        authStore.user.profile.role == 'asset-supervisor' ||
                        authStore.user.profile.role == 'superadmin' ||
                        authStore.user.profile.role == 'administrator') &&
                      formObjData.status != 'complete'
                    "
                    >{{
                      route.params.id ? "Update Request" : "Submit For Approval"
                    }}</v-btn
                  >
                </div>
              </v-row>
            </template>
            <v-row v-else>
              <div class="v-col-12 text-error">
                {{ errorMsg }}
              </div>
            </v-row>
            <v-row>
              <div class="v-col-12 v-col-md-12 text-info">
                Note: Once submitted and approval is on-going, you will no longer allowed
                to update the request.
              </div>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <AppSnackBar :options="sbOptions" />
  </v-container>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";

import AppPageHeader from "@/components/ApppageHeader.vue";
import { useRoute, useRouter } from "vue-router";
import Studio from "@/studio/Studio.vue";
import { useAuthStore } from "@/stores/auth";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { clientKey } from "@/services/axiosToken";

import {
  mdiTrashCan,
  mdiAccountCheck,
  mdiAccountClockOutline,
  mdiAccountDetails,
  mdiClose,
  mdiAccountCancel,
} from "@mdi/js";
import { useFormatDate } from "@/composables/formatDate.js";
const emit = defineEmits(["deleted"]);
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

const isValidate = ref(false);
const route = useRoute();
const router = useRouter();
const objData = ref({});
const isEdit = ref(false);
const assetDataObj = ref([{ qty: 1, attachment: {} }]);
const currentDate = ref(new Date());

const baseURL = ref(window.location.origin);
const studioSelectResponse = (index, v) => {
  assetDataObj.value[index].attachment = v[0];
};
const dialogAttachment = ref(false);
const currentSlider = ref(1);
const openAttachment = (index) => {
  currentSlider.value = index;
  dialogAttachment.value = true;
};
const removeAttachment = (index) => {
  assetDataObj.value[index].attachment = "";
};

const statusTitle = (v) => {
  if (v == "approve") {
    return "Approved By";
  } else if (v == "reviewer") {
    return "Reviewed By";
  } else if (v == "receiver") {
    return "Received By";
  } else if (v == "transport") {
    return "Trasport Arranged By";
  } else if (v == "releasing") {
    return "Released By";
  } else if (v == "verify") {
    return "Verified By";
  }
};
const requestTypeList = ref([]);
const fetchSetupRequest = async () => {
  await clientKey(authStore.token)
    .get("/api/fetch/approval-setups/non-paginated/data/request")
    .then((res) => {
      requestTypeList.value = res.data;
    })
    .catch((err) => {});
};

const sbOptions = ref({});
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

const AddAsset = () => {
  assetDataObj.value.push({ qty: 1, attachment: {} });
  requiredData();
};

const deleteData = (id, index) => {
  assetDataObj.value.splice(index, 1);
  requiredData();
};

const errorMsg = ref("Note: Administrator needs to setup signatories first.");

const submitRequest = () => {
  sbOptions.value = {
    status: true,
    type: "info",
    text: "Please wait...",
  };

  let newAssetDataObj = assetDataObj.value.map((o, i) => {
    o.file_id = "";
    if (o.attachment) {
      o.file_id = o.attachment.id;
    }
    delete o.assets;
    delete o.updated_at;
    delete o.created_at;
    delete o.id;
    delete o.attachment;
    return o;
  });

  let formData = {
    data: objData.value,
    assets: newAssetDataObj,
    approval: approvalSetupList.value,
    profile_id: authStore.user.profile.id,
    type: "request",
    role: authStore.user.profile.role,
  };

  clientKey(authStore.token)
    .post("/api/request-asset/store-update/data", formData)
    .then((res) => {
      let msg = "";
      let typeError = "";
      if (res.data.message) {
        msg = res.data.message;
        typeError = "success";
      } else if (res.data.error) {
        msg = res.data.error;
        typeError = "error";
      }
      sbOptions.value = {
        status: true,
        type: typeError,
        text: msg,
      };

      if (!route.params.id) {
        setTimeout(() => {
          router
            .push({
              name: "EditRequestAsset",
              params: {
                id: res.data.id,
              },
            })
            .catch((err) => {});
        }, 800);
      }
    })
    .catch((err) => {
      console.log(err);
    });
};

const approvalSetupList = ref([]);
const hasSignatories = ref(true);
const setupApprovals = async () => {
  await clientKey(authStore.token)
    .get("/api/fetch/request-asset/approval-setup-fetch/" + objData.value.request_type_id)
    .then((res) => {
      console.log("setupApprovals", res);
      approvalSetupList.value = res.data?.stages;

      if (route.params.id && approvalSetupList.value.length > 0) {
        approvalSetupList.value.map((o, i) => {
          o.profile_id = onUpdateApproval.value[i].profile_id;
          o.status = onUpdateApproval.value[i].status;
          o.date_approved = onUpdateApproval.value[i].date_approved;
          o.reason_rejected = onUpdateApproval.value[i].reason_rejected;
          return o;
        });
        
        if (approvalSetupList.value.length > 1) {
          approvalSetupList.value[onUpdateApproval.value.length - 1] = {
            id: onUpdateApproval.value[onUpdateApproval.value.length - 1].id,
            profile_id:  onUpdateApproval.value[onUpdateApproval.value.length - 1].profile_id,
            status: onUpdateApproval.value[onUpdateApproval.value.length - 1].status,
            date_approved: onUpdateApproval.value[onUpdateApproval.value.length - 1].date_approved,
            status: onUpdateApproval.value[onUpdateApproval.value.length - 1].status,
            reason_rejected: "",
            types: onUpdateApproval.value[onUpdateApproval.value.length - 1].approval_type,
            sort: onUpdateApproval.value[onUpdateApproval.value.length - 1].orders,
            request_asset_id: onUpdateApproval.value[onUpdateApproval.value.length - 1].request_asset_id,
            signatures: [
              {
                id: onUpdateApproval.value[onUpdateApproval.value.length - 1]?.profile.id,
                display_name: onUpdateApproval.value[onUpdateApproval.value.length - 1]?.profile.display_name,
                first_name: onUpdateApproval.value[onUpdateApproval.value.length - 1]?.profile.first_name,
                last_name: onUpdateApproval.value[onUpdateApproval.value.length - 1]?.profile.last_name,
              },
            ],
          };
        }
      }

      if (res.data?.stages.length > 0) {
        hasSignatories.value = true;
      } else {
        hasSignatories.value = false;
      }
    })
    .catch((err) => {});
};

const requiredData = () => {
  let checkObj = false;
  isValidate.value = false;
  if (
    objData.value.company_id &&
    objData.value.request_type_id &&
    objData.value.subject &&
    objData.value.transferred_from &&
    objData.value.transferred_to
  ) {
    checkObj = true;
  }

  let checkAsset = true;
  assetDataObj.value.map((o) => {
    if (!o.item_description) {
      checkAsset = false;
    }else if(!o.reason_for_request){
      checkAsset = false;
    }
  });

  let checkSignatories = true;
  approvalSetupList.value.map((o) => {
    if (!o.profile_id) {
      checkSignatories = false;
    }
  });

  if (checkSignatories && checkAsset && checkObj) {
    isValidate.value = true;
  }
};

const changeRequestStatus = (v) => {
  let dataForm = {
    status: v,
    profile_id: authStore.user.profile.id,
    ID: formObjData.value.id,
  };
  clientKey(authStore.token)
    .post("/api/request-asset/change-request/data", dataForm)
    .then((res) => {
      sbOptions.value = {
        status: true,
        type: "success",
        text: res.data.message,
      };
      formObjData.value.status = v;
      emit("saved", res.data.message);
    })
    .catch((err) => {});
};

const pad = (v, size = 6) => {
  let s = "00000" + v;
  return s.substring(s.length - size);
};

const onUpdateApproval = ref([]);
const formObjData = ref({});
onMounted(() => {
  fetchSetupRequest().then(() => {
    fetchCompanies().then(() => {
      fetchLocations();
    });
  });

  if (route.params.id) {
    isEdit.value = true;

    let v = props.objectdata;
    formObjData.value = v;

    objData.value.company_id = v.company_id;
    objData.value.request_type_id = v.request_type_id;
    objData.value.subject = v.subject;
    objData.value.transferred_from = v.transferred_from;
    objData.value.transferred_to = v.transferred_to;
    objData.value.id = v.id;
    objData.value.requestor_id = v.profile_id;
    onUpdateApproval.value = v.request_approvals;
     
    assetDataObj.value = v.items;

    setupApprovals();
  } else {
    objData.value.company_id = authStore.user.profile.company_id;
  }
});

watch(objData.value, async (newVal, oldVal) => {
  requiredData();
});
watch(assetDataObj.value, async (newVal, oldVal) => {
  requiredData();
});
</script>
