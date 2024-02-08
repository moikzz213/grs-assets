<template>
  <v-container>
    <AppPageHeader title="Incident detail page" />
    <v-row class="mt-5">
      <div class="v-col py-0">
        <v-btn
          @click="changeType('details')"
          class="mr-3 mb-3"
          :color="`${isActive == 'details' ? 'primary' : 'white'}`"
          >Details</v-btn
        >
        <v-btn
          v-if="isEdit"
          class="mr-3 mb-3"
          :color="`${isActive == 'attachment' ? 'primary' : 'white'}`"
          @click="changeType('attachment')"
          >Attachments</v-btn
        >
        <v-btn
          v-if="
            isEdit &&
            (authStore.user.profile.role == 'facility' ||
              authStore.user.profile.role == 'admin' ||
              authStore.user.profile.role == 'superadmin' ||
              authStore.user.profile.role == 'technical-operation')
          "
          class="mr-3 mb-3"
          :color="`${isActive == 'warranty' ? 'primary' : 'white'}`"
          @click="changeType('warranty')"
          >Warranty</v-btn
        >
        <v-btn
          v-if="
            isEdit &&
            (authStore.user.profile.role == 'facility' ||
              authStore.user.profile.role == 'admin' ||
              authStore.user.profile.role == 'superadmin' ||
              authStore.user.profile.role == 'technical-operation')
          "
          class="mr-3 mb-3"
          :color="`${isActive == 'facility' ? 'primary' : 'white'}`"
          @click="changeType('facility')"
          >Facility Team</v-btn
        >
      </div>
    </v-row>
    <v-row class="mb-10">
      <div class="v-col py-0">
        <v-card v-if="isActive == 'details'">
          <Form as="v-form" :validation-schema="validation" v-slot="{ meta }">
            <v-card-title class="text-capitalize my-1">
              {{ props.headertitle }}</v-card-title
            >
            <v-card-text>
              <v-row v-if="isEdit" class="bg-background-darken-1 ma-0 rounded">
                <div class="v-col-12 v-col-md-4">
                  <div class="font-weight-bold">SN:</div>
                  <div>ISR-2{{ pad(objData.id) }}</div>
                </div>
                <div class="v-col-12 v-col-md-4">
                  <div class="font-weight-bold">Reported by:</div>
                  <div>
                    {{ objData.profile?.display_name }}
                  </div>
                </div>
                <div class="v-col-12 v-col-md-4">
                  <div class="font-weight-bold">Status</div>
                  <div>{{ objData.status?.title }}</div>
                </div>
                <div class="v-col-12 v-col-md-4">
                  <div class="font-weight-bold">Date Reported</div>
                  <div>
                    {{
                      objData.created_at
                        ? dayjs(objData.created_at).format("MMM. DD, YYYY")
                        : "-"
                    }}
                  </div>
                </div>
                <div class="v-col-12 v-col-md-4">
                  <div class="font-weight-bold">Date Closed</div>
                  <div>
                    {{
                      objData.date_closed
                        ? dayjs(objData.date_closed).format("MMM. DD, YYYY")
                        : "-"
                    }}
                  </div>
                </div>
              </v-row>
              <v-row>
                <div class="v-col-12 v-col-md-4">
                  <Field name="Type" v-slot="{ field, errors }" v-model="objData.type_id">
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
                <div class="v-col-12 v-col-md-4">
                  <Field
                    name="Urgency"
                    v-slot="{ field, errors }"
                    v-model="objData.urgency_id"
                  >
                    <v-select
                      :items="urgencyList"
                      v-model="objData.urgency_id"
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
                <div class="v-col-12 v-col-md-4 d-flex">
                  <v-text-field
                    v-model="objData.asset_code"
                    variant="outlined"
                    density="compact"
                    hide-details
                    label="Asset Code"
                    :append-inner-icon="mdiMagnify"
                    @click:appendInner="onDecode(objData.asset_code)"
                  ></v-text-field>
                  <v-icon
                    @click="enableBarcodeFn"
                    class="ml-2 my-auto"
                    size="large"
                    :icon="mdiBarcodeScan"
                  ></v-icon>
                </div>
                <div class="v-col-12">
                  <Field name="Title" v-slot="{ field, errors }" v-model="objData.title">
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
                <div class="v-col-12 v-col-md-6">
                  <Field
                    name="Company"
                    v-slot="{ field, errors }"
                    v-model="objData.company_id"
                  >
                    <v-select
                      :items="companyList"
                      v-model="objData.company_id"
                      variant="outlined"
                      density="compact"
                      hide-details
                      item-value="id"
                      item-title="title"
                      clearable
                      label="Company*"
                      v-bind="field"
                      :error-messages="errors"
                    ></v-select>
                  </Field>
                </div>
                <div class="v-col-12 v-col-md-6">
                  <Field
                    name="Location"
                    v-slot="{ field, errors }"
                    v-model="objData.location_id"
                  >
                    <v-select
                      :items="locationList"
                      v-model="objData.location_id"
                      variant="outlined"
                      density="compact"
                      hide-details
                      item-value="id"
                      item-title="title"
                      clearable
                      label="Location*"
                      v-bind="field"
                      :error-messages="errors"
                    ></v-select>
                  </Field>
                </div>
                <div class="v-col-12">
                  <v-textarea
                    rows="3"
                    variant="outlined"
                    density="compact"
                    label="Incident Description"
                    hide-details="auto"
                    v-model="objData.description"
                  ></v-textarea>
                </div>
                <div class="v-col-12">
                  <v-btn
                    color="primary"
                    @click="saveData"
                    :disabled="!meta.valid"
                    :loading="loadingBtn"
                    v-if="
                      objData?.status_id != 8 &&
                      (!isEdit ||
                        (isEdit &&
                          (props.objectdata.profile_id == loggedID ||
                            loggedRole == 'asset-supervisor')))
                    "
                    >{{ isEdit ? "update" : "submit" }}</v-btn
                  >
                </div>
              </v-row>
            </v-card-text>
          </Form>
        </v-card>
        <div v-if="isActive == 'warranty'">
          <v-card>
            <v-card-text>
              <h4 class="headline mb-0 text-center">WARRANTY INFORMATION</h4>
            </v-card-text>
          </v-card>
          <v-row v-if="objData.asset?.pivot_warranties?.length > 0">
            <div class="v-col-12 v-col-md-12">
              <v-card
                class="my-3 px-5"
                v-for="item in objData.asset.pivot_warranties"
                :key="item.id"
              >
                <v-card-text>
                  <v-row>
                    <v-col cols="4">Vendor</v-col>
                    <v-col cols="8">{{ item.vendor.title }}</v-col>
                    <v-col cols="4">Warranty Start Date</v-col>
                    <v-col cols="8">{{ useFormatDate(item.warranty_start_date) }}</v-col>
                    <v-col cols="4">Warranty End Date</v-col>
                    <v-col cols="8">{{ useFormatDate(item.warranty_end_date) }}</v-col>
                    <v-col cols="4">AMC Start Date</v-col>
                    <v-col cols="8">{{ useFormatDate(item.amc_start_date) }}</v-col>
                    <v-col cols="4">AMC End Date</v-col>
                    <v-col cols="8">{{ useFormatDate(item.amc_end_date) }}</v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </div>
          </v-row>
          <v-card class="mt-2" v-else>
            <v-card-text>
              <h5 class="headline mb-0 text-center">
                NO WARRANTY FOUND. <br />HAVING THE CORRECT ASSET CODE IN DETAILS SECTION
                WILL SHOW IT'S CORRESPONDING WARRANTY
              </h5>
            </v-card-text>
          </v-card>
        </div>
        <Attachment
          v-else-if="isEdit && isActive == 'attachment'"
          :incident-id="parseInt(route.params.id)"
          :attachment="objData.attachment"
          @save="DataUpdateEmit"
          :objectdata="objData"
        />
        <Facility
          v-else-if="
            (loggedRole == 'superadmin' ||
              loggedRole == 'admin' ||
              loggedRole == 'technical-operation' ||
              loggedRole == 'facility') &&
            isEdit &&
            isActive == 'facility'
          "
          :objectdata="objData"
          @saved="DataUpdateEmit"
        />
      </div>
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
import { ref, onMounted, watch } from "vue";
import AppPageHeader from "@/components/ApppageHeader.vue";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { useAuthStore } from "@/stores/auth";
import { useRoute, useRouter } from "vue-router";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
import { clientKey } from "@/services/axiosToken";
import { mdiBarcodeScan, mdiMagnify } from "@mdi/js";
import { StreamBarcodeReader } from "vue-barcode-reader";
import { useFormatDate } from "@/composables/formatDate.js";
import { useStatusStore } from "@/stores/status";
import Attachment from "@/pages/normal/incidents/Attachment.vue";
import Facility from "./Facility.vue";
import dayjs from "dayjs";
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

const objData = ref({});
const sbOptions = ref({});

const DataUpdateEmit = (v) => {
  sbOptions.value = {
    status: true,
    type: "success",
    text: v,
  };

  emit("saved");
};

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

// status list
const statusStore = useStatusStore();
const urgencyList = ref([]);
urgencyList.value = Object.assign([], statusStore.urgencies_active_list);
 
if (statusStore.list.length == 0) {
  statusStore.getStatuses(authStore.token).then(() => {
    urgencyList.value = Object.assign([], statusStore.urgencies_active_list);
   
  });
}

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
  objData.value.asset_code = result;
  await clientKey(authStore.token)
    .get("/api/fetch/asset-info/by/asset-code/" + result)
    .then((res) => {
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
      } else {
        loadingBtn.value = false;
      }
    })
    .catch((err) => {
      
      loadingBtn.value = false;
    });
};

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

const pad = (v, size = 5) => {
  let s = "0000" + v;
  return s.substring(s.length - size);
};

onMounted(() => {
  if (route.params.id) {
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

    objData.value.asset_code = props.objectdata.asset
      ? props.objectdata.asset.asset_code
      : props.objectdata.asset_code;
    objData.value.title = props.objectdata.asset
      ? props.objectdata.asset.asset_name
      : props.objectdata.title;
    objData.value.company_id = props.objectdata.company_id;
    objData.value.location_id = props.objectdata.location_id;
    objData.value.asset_id = props.objectdata.asset_id;
    objData.value.profile_id = props.objectdata.profile_id;
    objData.value.type_id = parseInt(props.objectdata.type_id);
    objData.value.urgency = parseInt(props.objectdata.urgency);
    objData.value.description = props.objectdata.description;
  }
});

watch(
  () => props.objectdata.remarks,
  (newValue, oldValue) => {
    objData.value = props.objectdata;
  },
  { deep: true }
);

watch(
  () => props.objectdata.attachment,
  (newValue, oldValue) => {
    objData.value = props.objectdata;
    objData.value.attachment = newValue;
  },
  { deep: true }
);
</script>

<style lang="scss" scoped>
.tab-active {
  background-color: #c6a92d;
  color: #ffffff;
}
</style>
