<template>
  <v-card :loading="loadingAsset" max-width="1200px" class="mx-auto elevation-0">
    <Form as="v-form" :validation-schema="validation">
      <v-card-title class="mb-5 py-3 d-flex align-center justify-space-between">
        <div class="text-primary text-subtitle-1 text-capitalize">
          {{ formTitle }}
        </div>
        <v-btn color="primary" :loading="loadingAsset" @click="saveAsset">Save</v-btn>
      </v-card-title>
      <v-card-text>
        <v-row>
          <div class="v-col-12 font-weight-bold">Asset Info</div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-1">
            <Field
              name="Asset Name"
              v-slot="{ field, errors }"
              v-model="assetObj.asset_name"
            >
              <v-text-field
                v-model="assetObj.asset_name"
                v-bind="field"
                label="Asset Name"
                variant="outlined"
                density="compact"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-1">
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
          <div class="v-col-12 v-col-md-6 pt-0 pb-1">
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
          <div class="v-col-12 v-col-md-6 pt-0 pb-1">
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
          <div class="v-col-12 v-col-md-6 pt-0 pb-1">
            <Field
              name="Category"
              v-slot="{ field, errors }"
              v-model="assetObj.category_id"
            >
              <v-select
                v-model="assetObj.category_id"
                v-bind="field"
                :items="companyStore.list"
                label="Category"
                density="compact"
                variant="outlined"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-6 pt-0 pb-1">
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
          <div class="v-col-12 v-col-md-6 pt-0 pb-1">
            <Field name="Location" v-slot="{ field, errors }" v-model="assetObj.location">
              <v-select
                v-model="assetObj.location_id"
                v-bind="field"
                :items="companyStore.list"
                label="Location"
                density="compact"
                variant="outlined"
                :error-messages="errors"
              />
            </Field>
          </div>
        </v-row>
        <v-row>
          <div class="v-col-12">
            <v-card>
              <v-card-title
                @click="() => openPanel('addition-information')"
                class="mb-5 py-3 cursor-pointer"
              >
                <div class="font-weight-bold">Additional Information</div>
              </v-card-title>
              <v-card-text>
                <v-row>
                  <div class="v-col-12 v-col-md-6 pt-0 pb-1">
                    <Field
                      name="Brand"
                      v-slot="{ field, errors }"
                      v-model="assetObj.brand_id"
                    >
                      <v-select
                        v-model="assetObj.brand_id"
                        v-bind="field"
                        :items="companyStore.list"
                        label="Brand"
                        density="compact"
                        variant="outlined"
                        :error-messages="errors"
                      />
                    </Field>
                  </div>
                  <div class="v-col-12 v-col-md-6 pt-0 pb-1">
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
                </v-row>
              </v-card-text>
            </v-card>
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

// ui
const formTitle = ref("Add Asset");
const panel = ref([]);

// asset
let validation = yup.object({
  "Asset Name": yup.string().required().max(150),
  "Asset Code": yup.string().required().max(50),
  Company: yup.string().required(),
  Location: yup.string().required(),
  "Serial Number": yup.string().required().max(80),
  Specification: yup.string().required().max(120),
  "Section code": yup.string().required().max(30),
  Brand: yup.string().required(),
});
const loadingAsset = ref(false);
const assetObj = ref({});
const saveAsset = async () => {};

// companies
if (companyStore.list.length == 0) {
  companyStore.getCompanies(authStore.token);
}

// panels
const openPanel = async (panel) => {
  console.log("openPanel", panel);
};
</script>
