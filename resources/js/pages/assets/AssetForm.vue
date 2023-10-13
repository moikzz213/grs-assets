<template>
  <v-card :loading="loadingAsset">
    <v-card-title class="text-primary text-capitalize mb-5 py-3">{{
      formTitle
    }}</v-card-title>
    <v-card-text>
      <Form as="v-form" :validation-schema="validation">
        <v-row>
          <div class="v-col-12 font-weight-bold">Asset Info</div>
          <div class="v-col-12 v-col-md-6 py-0">
            <Field
              name="Company"
              v-slot="{ field, errors }"
              v-model="assetObj.asset_name"
            >
              <v-select
                v-model="assetObj.company_id"
                v-bind="field"
                :items="companyStore.list"
                label="Company"
                density="compact"
                variant="outlined"
                class="mb-2"
                :error-messages="errors"
              />
            </Field>
          </div>
          <div class="v-col-12 v-col-md-6 py-0">
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
          <div class="v-col-12 v-col-md-6 py-0">
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
          <div class="v-col-12">
            <v-btn color="primary" size="large" :loading="loadingAsset" @click="saveAsset"
              >Save</v-btn
            >
          </div>
        </v-row>
      </Form>
    </v-card-text>
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

// asset
let validation = yup.object({
  "Asset Name": yup.string().required().max(150),
  "Asset Code": yup.string().required().max(50),
  company_id: yup.string().required(),
});
const loadingAsset = ref(false);
const assetObj = ref({});
const saveAsset = async () => {};

// companies
if (companyStore.list.length == 0) {
  companyStore.getCompanies(authStore.token);
}
</script>
