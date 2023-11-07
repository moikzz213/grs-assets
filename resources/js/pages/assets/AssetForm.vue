<template>
  <v-card :loading="loadingAsset" max-width="1200px" class="mx-auto elevation-0">
    <Form as="v-form" :validation-schema="validation">
      <v-card-title class="mb-5 py-3 d-flex align-center justify-space-between">
        <div class="text-primary text-capitalize">
          {{ formTitle }}
        </div>
        <v-btn color="primary" :loading="loadingAsset" @click="saveAsset">Save</v-btn>
      </v-card-title>
      <v-card-text>
        <v-row>
          <div class="v-col-12 font-weight-bold">Asset Info</div>
          <div class="v-col-12 v-col-md-4 pt-0 pb-2">
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
          <div class="v-col-12 v-col-md-4 pt-0 pb-2">
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
          <div class="v-col-12 v-col-md-4 pt-0 pb-2">
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
          <div class="v-col-12 v-col-md-4 pt-0 pb-2">
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
              @click="() => changeTab('additional')"
              :color="selectedTab == 'additional' ? 'primary' : 'white'"
              >Additional Info</v-btn
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
              @click="() => changeTab('alloted')"
              :color="selectedTab == 'alloted' ? 'primary' : 'white'"
              >Allotted</v-btn
            >
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('warranty')"
              :color="selectedTab == 'warranty' ? 'primary' : 'white'"
              >Warranty</v-btn
            >
            <v-btn
              class="mr-2 mb-2"
              @click="() => changeTab('maintenance')"
              :color="selectedTab == 'maintenance' ? 'primary' : 'white'"
              >Maintenance</v-btn
            >
          </div>
          <div class="v-col-12 text-capitalize">
            {{ selectedTab + " Information" }}
          </div>
          <div class="v-col-12">
            <v-row v-show="selectedTab == 'additional'">
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
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
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
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
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
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
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
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
            <v-row v-show="selectedTab == 'purchase'">
              <div class="v-col-12 v-col-md-4 pt-0 pb-2">
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
            </v-row>
          </div>
        </v-row>
      </v-card-text>
    </Form>
  </v-card>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";

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

// ui
const formTitle = ref("Add Asset");
const panel = ref([]);

// tabs
const selectedTab = ref("additional");
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
const loadingAsset = ref(false);
const assetObj = ref({});
const saveAsset = async () => {};

// panels
const openPanel = async (panel) => {
  console.log("openPanel", panel);
};
</script>
