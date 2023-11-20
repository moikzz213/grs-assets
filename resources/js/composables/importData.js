import * as papa from "papaparse";
import { clientKey } from "@/services/axiosToken";
import { useAuthStore } from "@/stores/auth";
function useImportData(data) {
    papa.parse(inputFile.value[0], {
        header: true,
        transformHeader: function (text) {
          return text.replace(/\s+/g, "_").toLowerCase().trim();
        },
        complete: parseComplete,
      });
}

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


export { useImportData };
