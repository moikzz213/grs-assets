<template>
  <v-container class="pb-10">
    <AppPageHeader title="Import Asset" />
    <v-row>
      <div class="v-col-12">
        <v-card
          max-width="900px"
          class="mx-auto elevation-0"
          :loading="importSettings.loading"
        >
          <v-card-title class="pb-6">Import Asset</v-card-title>
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
              </div>
            </v-row>
          </v-card-text>
          <div class="px-3 py-1 text-caption text-primary">
            Note: This import function will ignore the data that already exist.
          </div>
          <v-divider></v-divider>
        </v-card>
      </div>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref } from "vue";
import { mdiPaperclip, mdiTrayArrowUp, mdiDownload } from "@mdi/js";
import AppPageHeader from "@/components/ApppageHeader.vue";
import ImportData from "@/components/import/ImportData.vue";
import { clientKey } from "@/services/axiosToken";

// route
import { useRoute } from "vue-router";
const route = useRoute();

// authStore
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

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
</script>
