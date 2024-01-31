<template>
  <v-card :loading="loadingAsset" max-width="1600px" class="mx-auto elevation-0">
    <Form as="v-form" :validation-schema="validation">
      <v-card-title class="pb-6 pt-3 d-flex align-center justify-space-between">
        <div class="text-primary text-capitalize">
          {{ props.page + ": " }} <strong>{{ assetObj.asset_code }}</strong>
        </div>
        <div class="d-flex align-center">
          <v-btn color="primary" class="mr-4" :loading="loadingAsset" @click="cancelFn"
            >Cancel</v-btn
          >
          <v-btn
            v-if="route.name != 'view-asset'"
            color="primary"
            :loading="loadingAsset"
            @click="saveAsset"
            >Save</v-btn
          >
        </div>
      </v-card-title>
      <v-card-text>
        <v-row>
          <div class="v-col-12 font-weight-bold">Asset Info</div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <Field name="Name" v-slot="{ field, errors }" v-model="assetObj.asset_name">
              <v-text-field
                v-model="assetObj.asset_name"
                v-bind="field"
                label="Name*"
                variant="outlined"
                density="compact"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <Field
              name="Serial Number"
              v-slot="{ field, errors }"
              v-model="assetObj.serial_number"
            >
              <v-text-field
                v-model="assetObj.serial_number"
                v-bind="field"
                label="Serial Number(Optional)"
                variant="outlined"
                density="compact"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <Field
              name="Section code"
              v-slot="{ field, errors }"
              v-model="assetObj.section_code"
            >
              <v-text-field
                v-model="assetObj.section_code"
                v-bind="field"
                label="Section code (optional)"
                variant="outlined"
                density="compact"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <!-- item-title="title"
            item-value="id" -->
            <!-- <Field
              name="Category"
              v-slot="{ field, errors }"
              v-model="assetObj.category_id"
            >
              <v-autocomplete
                v-model="assetObj.category_id"
                v-bind="field"
                :items="categoryStore.list.map((cs) => cs.title)"
                label="Category"
                density="compact"
                variant="outlined"
                :error-messages="errors"
              />
            </Field> -->
            <v-autocomplete
              v-model="assetObj.category_id"
              :items="categoryStore.list"
              item-title="title"
              item-value="id"
              label="Category*"
              density="compact"
              variant="outlined"
              :rules="[(v) => !!v || 'Category is required']"
            />
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <v-autocomplete
              v-model="assetObj.company_id"
              :items="companyStore.list"
              item-title="title"
              item-value="id"
              label="Company*"
              density="compact"
              variant="outlined"
              :rules="[(v) => !!v || 'Company is required']"
            />
            <!-- :error-messages="errors" -->
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <v-autocomplete
              v-model="assetObj.location_id"
              :items="locationStore.list"
              item-title="title"
              item-value="id"
              label="Location*"
              density="compact"
              variant="outlined"
              :rules="[(v) => !!v || 'Location is required']"
            />
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2 d-flex align-start">
            <v-text-field
              style="width: 33.33%"
              v-model="assetObj.company_code"
              readonly
              label="Company Code"
              variant="outlined"
              density="compact"
              :apped="mdiMinus"
            />
            <v-icon :icon="mdiMinus" class="mt-3 mx-1"></v-icon>
           
            <v-text-field
              style="width: 33.33%"
              readonly
              v-model="assetObj.category_code"
              label="Asset Code"
              variant="outlined"
              density="compact"
            />
            <v-icon :icon="mdiMinus" class="mt-3 mx-1"></v-icon>
            <Field
              name="Asset Number"
              v-slot="{ field, errors }"
              v-model="assetObj.asset_tag"
            >
              <v-text-field
                style="width: 33.33%"
                v-bind="field"
                :error-messages="errors"
                type="number"
                v-model="assetObj.asset_tag"
                label="Asset Number*"
                variant="outlined"
                density="compact"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <v-autocomplete
              v-model="assetObj.status_id"
              :items="statusStore.assets"
              :disabled="assetObj.status_id == 2"
              item-title="title"
              item-value="id"
              label="Asset Status*"
              density="compact"
              variant="outlined"
              :rules="[(v) => !!v || 'Asset Status is required']"
            />
          </div>
        </v-row>
        <v-row>
          <div
            v-if="props.page == 'edit' || props.page == 'view'"
            class="v-col-12 pt-0 font-weight-bold"
          >
            Additional Info
          </div>
          <div v-if="props.page == 'edit' || props.page == 'view'" class="v-col-12 pt-0">
            <v-btn
              v-for="(btn, index) in buttonArray"
              :key="index"
              class="mr-2 mb-2"
              @click="() => changeTab(btn)"
              :color="selectedTab == btn ? 'primary' : 'white'"
              >{{ btn }}</v-btn
            >
          </div>
          <div class="v-col-12 pt-0 text-capitalize">
            {{ selectedTab + " Information" }}
          </div>
          <!-- Additional Information -->
          <div class="v-col-12" v-show="selectedTab == 'specification'">
            <v-row>
              <div
                class="v-col-12 pt-0 d-flex justify-space-between"
                v-if="props.page == 'edit' || props.page == 'view'"
              >
                <div>
                  Date Created: {{ useFormatDateTime(assetObj.created_at) }}<br />
                  Date Updated: {{ useFormatDateTime(assetObj.updated_at) }}
                </div>
                <div>UpdatedBy: {{ assetObj.last_updated_by?.display_name }}</div>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Specification"
                  v-slot="{ field, errors }"
                  v-model="assetObj.specification"
                >
                  <v-text-field
                    v-model="assetObj.specification"
                    v-bind="field"
                    label="Specification*"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <v-text-field
                  v-model="assetObj.model"
                  label="Model*"
                  variant="outlined"
                  density="compact"
                />
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <v-text-field
                  v-model="assetObj.brand"
                  label="Brand*"
                  variant="outlined"
                  density="compact"
                />
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <v-autocomplete
                  v-model="assetObj.condition_id"
                  :items="statusStore.conditions"
                  item-title="title"
                  item-value="id"
                  label="Condition*"
                  density="compact"
                  variant="outlined"
                  :rules="[(v) => !!v || 'Condition is required']"
                />
              </div>
              <div class="v-col-12 v-col-md-12 pt-0 pb-2">
                <v-textarea
                  v-model="assetObj.remarks"
                  label="Asset Remarks"
                  density="compact"
                  variant="outlined"
                  rows="2"
                >
                </v-textarea>
              </div>
            </v-row>
          </div>
          <!-- Purchase Information -->
          <div class="v-col-12" v-show="selectedTab == 'purchase'">
            <v-row>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <Field name="Price" v-slot="{ field, errors }" v-model="assetObj.price">
                  <v-text-field
                    v-model="assetObj.price"
                    v-bind="field"
                    label="Price (Numeric Only)"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-2 pt-0 pb-2">
                <Field
                  name="Currency"
                  v-slot="{ field, errors }"
                  v-model="assetObj.currency"
                >
                  <v-text-field
                    v-model="assetObj.currency"
                    v-bind="field"
                    label="Currency(AED/USD)"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <v-autocomplete
                  v-model="assetObj.vendor_id"
                  :items="vendorStore.list"
                  item-title="title"
                  item-value="id"
                  label="Vendor"
                  density="compact"
                  variant="outlined"
                  :rules="[(v) => !!v || 'Vendor is required']"
                />
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="PO Number"
                  v-slot="{ field, errors }"
                  v-model="assetObj.po_number"
                >
                  <v-text-field
                    v-model="assetObj.po_number"
                    v-bind="field"
                    label="PO Number"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Purchase Date"
                  v-slot="{ field, errors }"
                  v-model="assetObj.purchased_date"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.purchased_date"
                    v-bind="field"
                    label="Purchase Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
            </v-row>
          </div>
          <!-- Financial Information -->
          <div class="v-col-12" v-show="selectedTab == 'financial'">
            <v-row>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Capitalization Price"
                  v-slot="{ field, errors }"
                  v-model="assetObj.capitalization_price"
                >
                  <v-text-field
                    v-model="assetObj.capitalization_price"
                    v-bind="field"
                    label="Capitalization Price"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Depreciation Percentage"
                  v-slot="{ field, errors }"
                  v-model="assetObj.depreciation_percentage"
                >
                  <v-text-field
                    v-model="assetObj.depreciation_percentage"
                    v-bind="field"
                    label="Depreciation Percentage"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Scrap Value"
                  v-slot="{ field, errors }"
                  v-model="assetObj.scrap_value"
                >
                  <v-text-field
                    v-model="assetObj.scrap_value"
                    v-bind="field"
                    label="Scrap Value"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Scrap Date"
                  v-slot="{ field, errors }"
                  v-model="assetObj.scrap_date"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.scrap_date"
                    v-bind="field"
                    label="Scrap Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Capitalization Date"
                  v-slot="{ field, errors }"
                  v-model="assetObj.capitalization_date"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.capitalization_date"
                    v-bind="field"
                    label="Capitalization Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="End of Life"
                  v-slot="{ field, errors }"
                  v-model="assetObj.end_of_life"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.end_of_life"
                    v-bind="field"
                    label="End of Life"
                    density="compact"
                    variant="outlined"
                    :error-messages="errors"
                  />
                </Field>
              </div>
            </v-row>
          </div>
          <!-- Warranty Information -->
          <div class="v-col-12 mb-6" v-show="selectedTab == 'warranty'">
            <AssetWarranties
              :asset="assetObj"
              :page="props.page"
              v-if="props.page != 'add'"
            />
          </div>
          <!-- Transfer history Information -->
          <div class="v-col-12 mb-6" v-show="selectedTab == 'transfer history'">
            <AssetAllotedLocations :asset="assetObj" />
          </div>
          <!-- Maintenance Information -->
          <div class="v-col-12 mb-6" v-show="selectedTab == 'maintenance'">
            <AssetMaintenances :asset="assetObj" />
          </div>
          <!-- Incident Information -->
          <div class="v-col-12 mb-6" v-show="selectedTab == 'incident'">
            <AssetIncidents :asset="assetObj" />
          </div>

          <div class="v-col-12 pt-0 font-weight-bold">Attachment</div>
          <div class="v-col-12 pt-0">
            <v-row>
              <div class="v-col-12 pb-2">
                <Studio
                  :options="{ multiSelect: true }"
                  @select="studioSelectResponse"
                  v-if="route.name != 'view-asset'"
                />
              </div>
            </v-row>
            <v-row v-if="selectedFiles.length > 0" class="px-1">
              <div
                v-for="(file, index) in selectedFiles"
                :key="file.id"
                class="v-col-12 v-col-md-2 pa-2"
                style="position: relative"
              >
                <v-btn
                  style="position: absolute; top: 0; right: 0; z-index: 1"
                  :icon="mdiClose"
                  size="26px"
                  color="error"
                  @click="() => removeAttachment(file.id)"
                >
                </v-btn>
                <v-card @click="() => openAttachment(index)">
                  <v-img :src="baseURL + '/file/' + file.path" cover aspect-ratio="1">
                  </v-img>
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
                    <v-carousel-item
                      v-for="(item, i) in selectedFiles"
                      :key="i"
                      reverse-transition="fade"
                      transition="fade"
                    >
                      <div
                        style="height: 680px; width: 100%"
                        class="d-flex align-center justify-center"
                      >
                        <v-img :src="baseURL + '/file/' + item.path"></v-img>
                      </div>
                    </v-carousel-item>
                  </v-carousel>
                </v-card>
              </v-dialog>
            </v-row>
            <v-row v-else>
              <div class="v-col-12 pt-0 pb-2">
                <v-sheet color="grey-lighten-4" class="pa-6 text-center">
                  Attachment is empty at the moment.
                </v-sheet>
              </div>
            </v-row>
          </div>
        </v-row>
      </v-card-text>
    </Form>
    <AppSnackBar :options="sbOptions" />
  </v-card>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
import { clientKey } from "@/services/axiosToken";
import AppSnackBar from "@/components/AppSnackBar.vue";
import Studio from "@/studio/Studio.vue";
import { useRouter, useRoute } from "vue-router";
import { mdiClose, mdiMinus } from "@mdi/js";
import AssetWarranties from "@/pages/assets/additional-info/AssetWarranties.vue";
import AssetMaintenances from "@/pages/assets/additional-info/AssetMaintenances.vue";
import AssetIncidents from "@/pages/assets/additional-info/AssetIncidents.vue";
import AssetAllotedLocations from "@/pages/assets/additional-info/AssetAllotedLocations.vue";
import { useFormatDateTime } from "@/composables/formatDate.js";
const router = useRouter();
const route = useRoute();

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

const buttonArray = ref([
  "specification",
  "purchase",
  "financial",
  "warranty",
  "transfer history",
  "maintenance",
  "incident",
]);
const assetObj = ref({});
// snackbar
const sbOptions = ref({});

// authStore
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

// companies
import { useCompanyStore } from "@/stores/companies";
const companyStore = useCompanyStore();
// if (companyStore.list.length == 0) {
companyStore.getCompanies(authStore.token);
// }
watch(
  () => assetObj.value.company_id,
  (newVal) => {
    assetObj.value.company_code = companyStore.list.filter(
      (comp) => comp.id == newVal
    )[0]?.code;
  }
);

// categories
import { useCategoryStore } from "@/stores/categories";
const categoryStore = useCategoryStore();
// if (categoryStore.list.length == 0) {
categoryStore.getCategories(authStore.token);
// }
watch(
  () => assetObj.value.category_id,
  (newVal) => {
    assetObj.value.category_code = categoryStore.list.filter(
      (cat) => cat.id == newVal
    )[0]?.code;
  }
);

// locations
import { useLocationStore } from "@/stores/locations";
const locationStore = useLocationStore();
// if (locationStore.list.length == 0) {
locationStore.getLocations(authStore.token);
// }

// vendor
import { useVendorStore } from "@/stores/vendors";
const vendorStore = useVendorStore();
// if (vendorStore.list.length == 0) {
vendorStore.getVendors(authStore.token);
// }

// base URL
const baseURL = ref(window.location.origin);

// tabs
const selectedTab = ref("specification"); // additional, purchase, financial, transfer history, warranty, maintenance
const changeTab = (tab) => {
  selectedTab.value = tab;
};

// asset
let validation = yup.object({
  // add
  Name: yup.string().required().max(150),
  "Serial Number": yup.string().max(80).nullable(),
  "Asset Number": yup.number().typeError("Asset number must be a number").required(),
  "Section code": yup.string().max(30).nullable(),
  Category: yup.string().required(),
  Company: yup.string().required(),
  Location: yup.string().required(),
  "Asset Status": yup.string().required(),
  Specification: yup.string().max(120).nullable(),
  Brand: yup.string().required(),
  Model: yup.string().required(),
  Condition: yup.string().required(),

  // edit
});

// save asset
const loadingAsset = ref(false);

const selectedFiles = ref([]);
const selectedFilesIds = computed(() => selectedFiles.value.map((sf) => sf.id));

const setAssetData = (assetData) => {
  // set assetObj
  assetObj.value = Object.assign({}, assetData);

  // set asset tag
  if (assetObj.value.asset_code) {
    let str = assetObj.value.asset_code + "";
    let split = str.split("-");

    // set asset_tag
    assetObj.value.asset_tag = split[split.length - 1];

    // set company_code
    assetObj.value.company_code = companyStore.list.filter(
      (comp) => comp.id == assetObj.value.company_id
    )[0]?.code;

    // set category_code
    
    assetObj.value.category_code = categoryStore.list.filter(
      (cat) => cat.id == assetObj.value.category_id
    )[0]?.code;
    
  }

  // set files
  selectedFiles.value =
    assetData && assetData.attachments ? Object.assign([], assetData.attachments) : [];

  // set financial info
  if (assetData && assetData.financial_information) {
    assetObj.value.capitalization_price =
      assetData.financial_information.capitalization_price;
    assetObj.value.depreciation_percentage =
      assetData.financial_information.depreciation_percentage;
    assetObj.value.scrap_value = assetData.financial_information.scrap_value;
    assetObj.value.scrap_date = assetData.financial_information.scrap_date;
    assetObj.value.capitalization_date =
      assetData.financial_information.capitalization_date;
    assetObj.value.end_of_life = assetData.financial_information.end_of_life;
  }
};
onMounted(() => {
  setTimeout(() => {
    setAssetData(props.asset);
  }, 800);
});

watch(
  () => props.asset,
  (newVal) => {
    setAssetData(newVal);
  }
);

// status
import { useStatusStore } from "@/stores/status";
const statusStore = useStatusStore();
if (statusStore.list.length == 0 || statusStore.conditions.length == 0) {
  statusStore.getStatuses(authStore.token);
}

const cancelFn = () => {
  router.go(-1);
};

const saveAsset = async () => {
  loadingAsset.value = true;
  assetObj.value.file_ids = selectedFilesIds.value;
  assetObj.value.asset_code =
    assetObj.value.company_code +
    "-" +
    assetObj.value.category_code +
    "-" +
    assetObj.value.asset_tag;
  assetObj.value.financial_information_id = assetObj.value.financial_information
    ? assetObj.value.financial_information.id
    : null;
  await clientKey(authStore.token)
    .post("/api/asset/save", assetObj.value)
    .then((res) => {
      sbOptions.value = {
        status: true,
        type: "success",
        text: res.data.message,
      };
      loadingAsset.value = false;
      // redirect to edit
      if (props.page == "add") {
        router
          .push({
            name: "edit-asset",
            params: {
              id: res.data.asset.id,
            },
          })
          .catch((err) => {
            console.log("router.push", err);
          });
      } else {
        // emit
      }
    })
    .catch((err) => {
      console.log("saveAsset", err);
      sbOptions.value = {
        status: true,
        type: "error",
        text: "Error while saving asset",
      };
      loadingAsset.value = false;
    });
};

const studioSelectResponse = (v) => {
  let fileExist = null;
  v.map((sudioFile) => {
    fileExist = selectedFiles.value.find((f) => f.id == sudioFile.id);
    if (!fileExist) {
      selectedFiles.value.push(sudioFile);
    }
  });
};

// attachment slider
const dialogAttachment = ref(false);
const currentSlider = ref(1);
const openAttachment = (index) => {
  currentSlider.value = index;
  dialogAttachment.value = true;
};
const removeAttachment = (theId) => {
  selectedFiles.value = selectedFiles.value.filter((f) => f.id !== theId);
};
</script>
