<template>
  <v-card :loading="loadingAsset" max-width="1600px" class="mx-auto elevation-0">
    <Form as="v-form" :validation-schema="validation">
      <v-card-title class="pb-6 pt-3 d-flex align-center justify-space-between">
        <div class="text-primary text-capitalize">
          {{ formTitle }}
        </div>
        <div class="d-flex align-center">
          <v-btn color="primary" class="mr-3" :loading="loadingAsset" @click="fillAsset"
            >fill form</v-btn
          >
          <v-btn color="primary" :loading="loadingAsset" @click="saveAsset">Save</v-btn>
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
                label="Name"
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
                label="Serial Number"
                variant="outlined"
                density="compact"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-2">
            <Field
              name="Asset Code"
              v-slot="{ field, errors }"
              v-model="assetObj.asset_code"
            >
              <v-text-field
                v-model="assetObj.asset_code"
                v-bind="field"
                label="Asset Code"
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
          <div class="v-col-12 v-col-md-4 pt-0 pb-2">
            <Field
              name="Category"
              v-slot="{ field, errors }"
              v-model="assetObj.category_id"
            >
              <v-select
                v-model="assetObj.category_id"
                v-bind="field"
                :items="categoryStore.list"
                label="Category"
                density="compact"
                variant="outlined"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-4 pt-0 pb-2">
            <Field
              name="Company"
              v-slot="{ field, errors }"
              v-model="assetObj.company_id"
            >
              <v-select
                v-model="assetObj.company_id"
                v-bind="field"
                :items="companyStore.list"
                label="Company"
                density="compact"
                variant="outlined"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-4 pt-0 pb-2">
            <Field name="Location" v-slot="{ field, errors }" v-model="assetObj.location">
              <v-select
                v-model="assetObj.location_id"
                v-bind="field"
                :items="locationStore.list"
                label="Location"
                density="compact"
                variant="outlined"
                :error-messages="errors"
              />
            </Field>
          </div>
        </v-row>
        <v-row>
          <div class="v-col-12 pt-0 font-weight-bold">Additional Info</div>
          <div class="v-col-12 pt-0">
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('specification')"
              :color="selectedTab == 'specification' ? 'primary' : 'white'"
              >Specification</v-btn
            >
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('purchase')"
              :color="selectedTab == 'purchase' ? 'primary' : 'white'"
              >Purchase</v-btn
            >
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('financial')"
              :color="selectedTab == 'financial' ? 'primary' : 'white'"
              >Financial</v-btn
            >
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('warranty')"
              :color="selectedTab == 'warranty' ? 'primary' : 'white'"
              >Warranty</v-btn
            >
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('allotted')"
              :color="selectedTab == 'allotted' ? 'primary' : 'white'"
              >Allotted</v-btn
            >
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('maintenance')"
              :color="selectedTab == 'maintenance' ? 'primary' : 'white'"
              >Maintenance</v-btn
            >
          </div>
          <div class="v-col-12 pt-0 text-capitalize">
            {{ selectedTab + " Information" }}
          </div>
          <!-- Additional Information -->
          <div class="v-col-12" v-show="selectedTab == 'specification'">
            <v-row>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Specification"
                  v-slot="{ field, errors }"
                  v-model="assetObj.specification"
                >
                  <v-text-field
                    v-model="assetObj.specification"
                    v-bind="field"
                    label="Specification"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Model"
                  v-slot="{ field, errors }"
                  v-model="assetObj.model_id"
                >
                  <v-select
                    v-model="assetObj.model_id"
                    v-bind="field"
                    :items="modelStore.list"
                    label="Model"
                    density="compact"
                    variant="outlined"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Brand"
                  v-slot="{ field, errors }"
                  v-model="assetObj.brand_id"
                >
                  <v-select
                    v-model="assetObj.brand_id"
                    v-bind="field"
                    :items="brandStore.list"
                    label="Brand"
                    density="compact"
                    variant="outlined"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Condition"
                  v-slot="{ field, errors }"
                  v-model="assetObj.condition_id"
                >
                  <v-select
                    v-model="assetObj.condition_id"
                    v-bind="field"
                    :items="conditionStore.list"
                    label="Condition"
                    density="compact"
                    variant="outlined"
                    :error-messages="errors"
                  />
                </Field>
              </div>
            </v-row>
          </div>
          <!-- Purchase Information -->
          <div class="v-col-12" v-show="selectedTab == 'purchase'">
            <v-row>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field name="Price" v-slot="{ field, errors }" v-model="assetObj.price">
                  <v-text-field
                    v-model="assetObj.price"
                    v-bind="field"
                    label="Price"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-6 pt-0 pb-2">
                <Field
                  name="Vendor"
                  v-slot="{ field, errors }"
                  v-model="assetObj.vendor_id"
                >
                  <v-select
                    v-model="assetObj.vendor_id"
                    v-bind="field"
                    :items="vendorStore.list"
                    label="Vendor"
                    density="compact"
                    variant="outlined"
                    :error-messages="errors"
                  />
                </Field>
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
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
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
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
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
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
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
                  name="Capitalization Date"
                  v-slot="{ field, errors }"
                  v-model="assetObj.capitalization_price"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.capitalization_price"
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
          <div class="v-col-12" v-show="selectedTab == 'warranty'">
            <v-row>
              <div class="v-col-12">Warranty</div>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <Field
                  name="Warranty"
                  v-slot="{ field, errors }"
                  v-model="assetObj.title"
                >
                  <v-text-field
                    v-model="assetObj.title"
                    v-bind="field"
                    label="Warranty"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <Field
                  name="Warranty Start Date"
                  v-slot="{ field, errors }"
                  v-model="assetObj.warranty_start_date"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.warranty_start_date"
                    v-bind="field"
                    label="Warranty Start Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <Field
                  name="Warranty End Date"
                  v-slot="{ field, errors }"
                  v-model="assetObj.warranty_end_date"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.warranty_end_date"
                    v-bind="field"
                    label="Warranty End Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 pt-0 pb-2">Vendor Warranty</div>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <Field
                  name="Vendor Warranty"
                  v-slot="{ field, errors }"
                  v-model="assetObj.vendor_warranty"
                >
                  <v-select
                    v-model="assetObj.vendor_warranty"
                    v-bind="field"
                    :items="vendorStore.list"
                    label="Vendor Warranty"
                    density="compact"
                    variant="outlined"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <Field
                  name="Vendor Start Date"
                  v-slot="{ field, errors }"
                  v-model="assetObj.vendor_start_date"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.vendor_start_date"
                    v-bind="field"
                    label="Vendor Start Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
                <Field
                  name="Vendor End Date"
                  v-slot="{ field, errors }"
                  v-model="assetObj.warranty_end_date"
                >
                  <v-text-field
                    type="date"
                    v-model="assetObj.warranty_end_date"
                    v-bind="field"
                    label="Vendor End Date"
                    variant="outlined"
                    density="compact"
                    :error-messages="errors"
                  />
                </Field>
              </div>
            </v-row>
          </div>
          <!-- Allotted Information -->
          <div class="v-col-12 mb-6" v-show="selectedTab == 'allotted'">
            <v-row>
              <div class="v-col-12 pt-0 pb-2">
                <v-card>
                  <v-table density="compact">
                    <thead>
                      <tr>
                        <th class="text-left text-primary">#</th>
                        <th class="text-left text-primary">Alloted to</th>
                        <th class="text-left text-primary">Remarks</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(item, index) in [
                          {
                            id: 1,
                            title: 'Grandiose Barsha',
                            remarks: 'Static Remarks',
                          },
                          {
                            id: 2,
                            title: 'GAG HO',
                            remarks: 'Static Remarks',
                          },
                        ]"
                        :key="item.id"
                      >
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.title }}</td>
                        <td>{{ item.remarks }}</td>
                      </tr>
                    </tbody>
                  </v-table>
                </v-card>
              </div>
            </v-row>
          </div>
          <!-- Maintenance Information -->
          <div class="v-col-12 mb-6" v-show="selectedTab == 'maintenance'">
            <v-row>
              <div class="v-col-12 pt-0 pb-2">
                <v-card>
                  <v-table density="compact">
                    <thead>
                      <tr>
                        <th class="text-left text-primary">INC ID</th>
                        <th class="text-left text-primary">Location</th>
                        <th class="text-left text-primary">Handled by</th>
                        <th class="text-left text-primary">Service Type</th>
                        <th class="text-left text-primary">Date Start</th>
                        <th class="text-left text-primary">Date End</th>
                        <th class="text-left text-primary">Cost</th>
                        <th class="text-left text-primary">Status</th>
                        <th class="text-left text-primary">Remarks</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(item, index) in [
                          {
                            id: 1,
                            location: 'Grandiose Barsha',
                            handle_by: 'Romel Indemne',
                            service_type: 'complaint',
                            date_start: '2023-11-11',
                            date_closed: '2023-11-11',
                            cost: '550',
                            status: 'halo-halo',
                            remarks: 'Static remarks',
                          },
                          {
                            id: 2,
                            location: 'GAG HO',
                            handle_by: 'Romel Indemne',
                            service_type: 'complaint',
                            date_start: '2023-11-11',
                            date_closed: '2023-11-11',
                            cost: '550',
                            status: 'halo-halo',
                            remarks: 'Static remarks',
                          },
                        ]"
                        :key="item.id"
                      >
                        <td>{{ item.id }}</td>
                        <td>{{ item.location }}</td>
                        <td>{{ item.handle_by }}</td>
                        <td>{{ item.service_type }}</td>
                        <td>{{ item.date_start }}</td>
                        <td>{{ item.date_closed }}</td>
                        <td>{{ item.cost }}</td>
                        <td>{{ item.status }}</td>
                        <td>{{ item.remarks }}</td>
                      </tr>
                    </tbody>
                  </v-table>
                </v-card>
              </div>
            </v-row>
          </div>
          <div v-if="props.page == 'edit'" class="v-col-12 pt-0">
            <div class="font-weight-bold">Attachment</div>
            <v-row>
              <div class="v-col-12 pt-0 pb-2">
                <v-card> </v-card>
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
import { ref, watch } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
import { clientKey } from "@/services/axiosToken";
import AppSnackBar from "@/components/AppSnackBar.vue";

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

// snackbar
const sbOptions = ref({});

// authStore
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

// companies
import { useCompanyStore } from "@/stores/companies";
const companyStore = useCompanyStore();
if (companyStore.list.length == 0) {
  companyStore.getCompanies(authStore.token);
}

// categories
import { useCategoryStore } from "@/stores/categories";
const categoryStore = useCategoryStore();
if (categoryStore.list.length == 0) {
  categoryStore.getCategories(authStore.token);
}

// locations
import { useLocationStore } from "@/stores/locations";
const locationStore = useLocationStore();
if (locationStore.list.length == 0) {
  locationStore.getLocations(authStore.token);
}

// brands
import { useBrandStore } from "@/stores/brands";
const brandStore = useBrandStore();
if (brandStore.list.length == 0) {
  brandStore.getBrands(authStore.token);
}

// models
import { useModelStore } from "@/stores/models";
const modelStore = useModelStore();
if (modelStore.list.length == 0) {
  modelStore.getModels(authStore.token);
}

// conditions
import { useConditionStore } from "@/stores/conditions";
const conditionStore = useConditionStore();
if (conditionStore.list.length == 0) {
  conditionStore.getConditions(authStore.token);
}

// vendor
import { useVendorStore } from "@/stores/vendors";
const vendorStore = useVendorStore();
if (vendorStore.list.length == 0) {
  vendorStore.getVendors(authStore.token);
}

// ui
const formTitle = ref("Add Asset");

// tabs
const selectedTab = ref("specification"); // additional, purchase, financial, allotted, warranty, maintenance
const changeTab = (tab) => {
  selectedTab.value = tab;
};

// asset
let validation = yup.object({
  Name: yup.string().required().max(150),
  "Asset Code": yup.string().required().max(50),
  Company: yup.string().required(),
  Location: yup.string().required(),
  "Serial Number": yup.string().required().max(80),
  Specification: yup.string().max(120),
  "Section code": yup.string().required().max(30),
  Brand: yup.string().required(),
});

// fill asset form
const fillAsset = () => {
  assetObj.value = {
    ...assetObj.value,
    ...{
      company_id: 1,
      location_id: 1,
      category_id: 1,
      status_id: 1,
      band_id: 1,
      model_id: 1,
      vendor_id: 1,
      author_id: 1,
      asset_name: "Test Asset",
      asset_code: "testassetcode",
      serial_number: "testserialnumber",
      section_code: "testsectioncode",
      specification: "Sample Specs",
      price: "550.00",
      po_number: "0001",
      purchased_date: "2023-11-11",
      remarks: "static remarks",
    },
  };
};

// save asset
const loadingAsset = ref(false);
const assetObj = ref({});

// set assetObj
assetObj.value = Object.assign({}, props.asset);
watch(
  () => props.asset,
  (newVal) => {
    assetObj.value = Object.assign({}, newVal);
  }
);
console.log("assetObj.value", assetObj.value);

const saveAsset = async () => {
  loadingAsset.value = true;
  await clientKey(authStore.token)
    .post("/api/asset/save", assetObj.value)
    .then((res) => {
      sbOptions.value = {
        status: true,
        type: "success",
        text: res.data.message,
      };
      loadingAsset.value = false;
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
</script>
