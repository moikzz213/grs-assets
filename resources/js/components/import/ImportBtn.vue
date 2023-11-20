<template>
  <div>
    <v-btn :color="importData.btnColor" v-bind="props">
      {{ importData.btnTitle }}
      <v-icon :icon="mdiTrayArrowUp" class="ml-3"></v-icon>
    </v-btn>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { mdiPaperclip, mdiTrayArrowUp, mdiDownload } from "@mdi/js";
import { useAuthStore } from "@/stores/auth";
import { clientKey } from "@/services/axiosToken";
import * as papa from "papaparse";

const appURL = ref(import.meta.env.VITE_APP_URL);
const authStore = useAuthStore();
const emit = defineEmits(["imported"]);
const props = defineProps({
  options: {
    type: Object,
    default: null,
  },
});

// import
const conditionData = ref({
  industry_id: null,
});
const importData = ref({
  dialog: false,
  btnTitle: "Import",
  btnColor: "primary",
  loading: false,
  cardTitle: "Import",
  endpoint: "",
  templateFile: "",
  conditionArray: [],
});
importData.value = { ...importData.value, ...props.options };
const inputFile = ref(null);
const rules = ref([
  (value) => {
    return (
      !value ||
      !value.length ||
      value[0].size < 2000000 ||
      "File size should be less than 2 MB."
    );
  },
]);
const importCSV = () => {
  importData.value.loading = true;
  papa.parse(inputFile.value[0], {
    header: true,
    transformHeader: function (text) {
      return text.replace(/\s+/g, "_").toLowerCase().trim();
    },
    complete: parseComplete,
  });
};

const parseComplete = async (results, file) => {
  // Remove 1st row header
  // delete results.data[0];

  // Filter Empty Rows
  let resultsArray = results.data.filter(function (el) {
    let firstKey = Object.keys(el)[0].toString(); // get the first property and check
    return el != null && el[firstKey] != "";
  });

  let validation = new Set(
    resultsArray.map((obj) => {
      return obj.title;
    })
  );

  if (validation.size < resultsArray.length) {
    emit("imported", {
      status: false,
      message: "Kindly remove the duplicate data from the file.",
    });

    setTimeout(() => {
      importData.value.loading = false;
    }, 1500);
    return false;
  }

  // set data
  let data = {
    import_data: JSON.stringify(resultsArray),
  };

  // save result to database
  await clientKey(authStore.authToken)
    .post(importData.value.endpoint, data)
    .then((res) => {
      emit("imported", {
        status: true,
        message: res.data.message,
      });
    })
    .catch((err) => {
      console.log("import error", err);
      let errorMsg = "";
      if (err.response.status == 500) {
        errorMsg = "Import error kindly double check the csv file";
      } else {
        errorMsg = "Error while importing data";
      }
      importData.value.loading = false;
      emit("imported", {
        status: false,
        message: errorMsg,
      });
    });
};

watch(
  () => props.options,
  (newVal) => {
    importData.value = { ...importData.value, ...newVal };
    console.log("importData.value", importData.value);
  }
);
</script>
