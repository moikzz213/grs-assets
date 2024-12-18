<template>
  <v-container class="pb-10">
    <AppPageHeader title="Import Asset" />
    <v-row v-if="check_auth">
      <div class="v-col-12">
        <v-card
          max-width="900px"
          class="mx-auto elevation-0"
          :loading="importSettings.loading"
        >
          <v-card-title class="pb-6 d-flex align-center justify-space-between"
            >Import Asset
            <v-btn
              class="bg-grey-lighten-4"
              variant="text"
              :href="baseURL + '/assets/csv/asset-import-template.csv'"
              download
              >Download Template <v-icon class="ml-2" :icon="mdiDownload"></v-icon
            ></v-btn>
          </v-card-title>
          <v-card-text>
            <v-row>
              <div class="v-col-12">
                <v-file-input
                  v-model="inputFile"
                  :rules="rules"
                  accept="text/csv"
                  variant="outlined"
                  density="compact"
                  :prepend-icon="null"
                  :append-inner-icon="mdiPaperclip"
                  label="Upload .csv file"
                ></v-file-input>
                <v-btn :loading="loadingImport" color="primary" @click="importCSV">
                  {{ "Import" }}
                  <v-icon :icon="mdiTrayArrowUp" class="ml-3"></v-icon>
                </v-btn>
                <div class="text-caption text-primary mt-3 text-info">
                  Note: Refer to below ID's upon importing data.
                </div>
              </div>
            </v-row>
          </v-card-text>
        </v-card>
      </div>
    </v-row>
    <v-row v-if="check_auth">
       
      <v-col md="3">
        <v-card>
          <v-card-subtitle class="mt-2 font-weight-bold">Business Unit</v-card-subtitle>
          <v-divider class="mt-2 mb-1" ></v-divider>
          <v-card-text style="max-height:600px; overflow: auto;">
             <v-row v-for="item in companyStore.list" :key="item.id"> 
              <v-col class="py-1"  md="12"><strong>ID: {{item.id}}</strong><br/>
                {{ item.title }}</v-col>
                  <v-divider></v-divider>
             </v-row>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col md="3">
        <v-card>
          <v-card-subtitle class="mt-2 font-weight-bold">Locations & Alloted Location</v-card-subtitle>
          <v-divider class="mt-2 mb-1"></v-divider>
          <v-card-text  style="max-height:600px; overflow: auto;">
            <v-row v-for="item in locationStore.list" :key="item.id"> 
              <v-col class="py-1"  md="12"><strong>ID: {{item.id}}</strong><br/>
                {{ item.title }}</v-col>
                  <v-divider></v-divider>
             </v-row>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col md="3">
        <v-card>
          <v-card-subtitle class="mt-2 font-weight-bold">Categories</v-card-subtitle>
          <v-divider class="mt-2 mb-1"></v-divider>
          <v-card-text  style="max-height:600px; overflow: auto;">
            <v-row v-for="item in categoryStore.list" :key="item.id"> 
              <v-col class="py-1"  md="12"><strong>ID: {{item.id}}</strong><br/>
                {{ item.title }}</v-col>
                  <v-divider></v-divider>
             </v-row>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col md="3">
        <v-card>
          <v-card-subtitle class="mt-2 font-weight-bold">Vendors</v-card-subtitle>
          <v-divider class="mt-2 mb-1"></v-divider>
          <v-card-text  style="max-height:600px; overflow: auto;">
            <v-row v-for="item in vendorStore.list" :key="item.id"> 
              <v-col class="py-1"  md="12"><strong>ID: {{item.id}}</strong><br/>
                {{ item.title }}</v-col>
                  <v-divider></v-divider>
             </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row v-if="!check_auth">
      <v-col>
        <v-card>
          <v-card-text class="text-center">
            You dont have permission to access this page.
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <AppSnackBar :options="sbOptions" />
  </v-container>
</template>

<script setup>
import { ref, computed } from "vue";
import { mdiPaperclip, mdiDownload, mdiTrayArrowUp } from "@mdi/js";
import AppPageHeader from "@/components/ApppageHeader.vue";
import * as papa from "papaparse";
import { clientKey } from "@/services/axiosToken";
import AppSnackBar from "@/components/AppSnackBar.vue";

// snackbar
const sbOptions = ref({});

// route
import { useRoute } from "vue-router";
const route = useRoute();

// authStore
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore(); 
 
const check_auth = computed(() => {
  return authStore.user.role == "superadmin" || authStore.user.role == "asset-supervisor" || authStore.user.role == "commercial-manager" ? true : false;
});

const baseURL = ref(window.location.origin);
const loadingImport = ref(false); 

const inputFile = ref(null);
const rules = ref([
  (value) => {
    return (
      !value ||
      !value.length ||
      value[0].size < 5000000 ||
      "File size should be less than 5 MB."
    );
  },
]);

const importSettings = ref({
  dialog: false,
  btnTitle: "Import",
  btnColor: "primary",
  loading: false,
  cardTitle: "Import",
  endpoint: "",
  templateFile: "",
  conditionArray: [],
});

// condition
const conditionData = ref({
  industry_id: null,
});

const importCSV = () => {
  loadingImport.value = true;
  papa.parse(inputFile.value[0], {
    header: true,
    transformHeader: function (text) {
      return text.replace(/\s+/g, "_").toLowerCase().trim();
    },
    complete: parseComplete,
  });
};

const parseComplete = async (results, file) => { 

  // Filter Empty Rows
  let resultsArray = results.data.filter(function (el) {
    let firstKey = Object.keys(el)[0].toString(); // get the first property and check
    return el != null && el[firstKey] != "";
  }); 

  // set data
  let data = {
    import_data: JSON.stringify(resultsArray),
  };

  // check import data
 
  // save result to database
  await clientKey(authStore.token)
    .post("/api/asset/import", data)
    .then((res) => {
     
      loadingImport.value = false;
      sbOptions.value = {
        status: true,
        type: "success",
        text: res.data.message,
      };
    })
    .catch((err) => {
      console.log("import error", err);
      loadingImport.value = false;
      let errorMsg = "";
      if (err.response.status == 500) {
        errorMsg = "Import error kindly double check the csv file";
      } else {
        errorMsg = "Error while importing data";
      }
      sbOptions.value = {
        status: true,
        type: "error",
        text: errorMsg,
      };
      loadingImport.value = false;
    });
}; 
 
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

// vendor
import { useVendorStore } from "@/stores/vendors";
const vendorStore = useVendorStore();
if (vendorStore.list.length == 0) {
  vendorStore.getVendors(authStore.token);
}
</script>
