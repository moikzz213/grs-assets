<template>
  <div>
    <file-pond
      name="filepond"
      ref="pond"
      instantUpload="false"
      maxFileSize="5MB"
      accepted-file-types="image/jpeg, image/png, application/pdf"
      v-bind:server="serverOptions"
      v-bind:files="selectedFiles"
      v-on:init="handleFilePondInit"
      v-on:error="handleFilePondError"
      :credits="{}"
      :imagePreviewHeight="250"
      :imageResizeTargetWidth="200"
      :imageResizeTargetHeight="200"
      imageCropAspectRatio="1:1"
      stylePanelLayout="integrated"
      styleLoadIndicatorPosition="center bottom"
      styleProgressIndicatorPosition="right bottom"
      styleButtonRemoveItemPosition="left bottom"
      styleButtonProcessItemPosition="right bottom"
    />
    <!-- <v-btn color="primary" @click="upload">Upload</v-btn> -->
  </div>
</template>

<script setup>
import axios from "axios";
import { mdiCloudUpload } from "@mdi/js";
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

// Import Vue FilePond
import vueFilePond from "vue-filepond";

// Import FilePond styles
import "filepond/dist/filepond.min.css";

// Import FilePond plugins
// Please note that you need to install these plugins separately
// `npm i filepond-plugin-image-preview filepond-plugin-image-exif-orientation filepond-plugin-file-validate-type --save`
import FilePondPluginImageExifOrientation from "filepond-plugin-image-exif-orientation";
import FilePondPluginFileValidateSize from "filepond-plugin-file-validate-size";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImageCrop from "filepond-plugin-image-crop";
import FilePondPluginImageResize from "filepond-plugin-image-resize";
import FilePondPluginImageTransform from "filepond-plugin-image-transform";

import FilePondPluginPdfPreview from "filepond-plugin-pdf-preview";

import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";

import FilePondPluginImageEdit from "filepond-plugin-image-edit";
import "filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css";

const pond = ref(null);

// Create component
const FilePond = vueFilePond(
  FilePondPluginFileValidateSize,
  FilePondPluginImageExifOrientation,
  FilePondPluginImagePreview,
  FilePondPluginFileValidateType,
  FilePondPluginImageCrop,
  FilePondPluginImageResize,
  FilePondPluginImageTransform,
  FilePondPluginImageEdit,
  FilePondPluginPdfPreview
);

const selectedFiles = ref([
  //   {
  //     source: "photo.jpeg",
  //     options: {
  //       type: "local",
  //     },
  //   },
]);

// fake server to simulate loading a 'local' server file and processing a file
// https://stackoverflow.com/questions/65989017/filepond-send-a-blank-request-to-server
const serverOptions = {
  url: "",
  // timeout: 7000,
  process: (
    fieldName,
    file,
    load,
    progress,
    metadata,
    error,
    abort,
    transfer,
    options
  ) => {
    console.log("process file", file);

    // fieldName is the name of the input field
    // file is the actual file object to send
    const formData = new FormData();
    formData.append(fieldName, file, file.name);
    formData.append("profile_id", authStore.user.profile.id);
    formData.append("file_id", file.id);

    // set XMLHttpRequest
    const request = new XMLHttpRequest();
    request.open("POST", "/api/file/upload");

    // set headers
    request.setRequestHeader(
      "X-CSRF-TOKEN",
      document.getElementsByTagName("meta")["csrf-token"].content
    );
    request.setRequestHeader("Authorization", `Bearer ${authStore.token}`);

    // set upload
    request.upload.onprogress = (e) => {
      progress(e.lengthComputable, e.loaded, e.total);
    };

    // set on load
    request.onload = function () {
      if (request.status >= 200 && request.status < 300) {
        console.log("request.responseText", request.responseText);
        // load(request.responseText);
        console.log("selectedFiles", selectedFiles.value);
      } else {
        error("Process Error");
      }
    };

    request.send(formData);

    return {
      abort: () => {
        request.abort();

        abort();
      },
    };

    // if (error) {
    //   console.log("error", error);
    //   return;
    // }
    // await axios({
    //   method: "post",
    //   url: "/api/file/upload",
    //   data: formData,
    //   headers: {
    //     "X-CSRF-TOKEN": document.getElementsByTagName("meta")["csrf-token"].content,
    //     Authorization: `Bearer ${authStore.token}`,
    //     "Content-Type":
    //       "multipart/form-data; charset=utf-8; boundary=" +
    //       Math.random().toString().substring(2),
    //     withCredentials: false,
    //   },
    //   onUploadProgress: (e) => {
    //     // updating progress indicator
    //     progress(e.lengthComputable, e.loaded, e.total);
    //   },
    // })
    //   .then((response) => {
    //     console.log("then response", response);
    //     // passing the file id to FilePond
    //     load(response.data.data.id);
    //   })
    //   .catch((thrown) => {
    //     error("oh no");
    //     if (axios.isCancel(thrown)) {
    //       console.log("Request canceled", thrown.message);
    //     } else {
    //       // handle error
    //     }
    //   });
  },
  //   labelFileProcessingError: () => {
  //     // replaces the error on the FilePond error label
  //     console.log("serverResponse.message", serverResponse.message);
  //     return serverResponse.message;
  //   },
  //   https://pqina.nl/filepond/docs/api/server/#advanced
  revert: null,
  restore: null,
  load: null,
  fetch: null,
};
const upload = async () => {
  console.log("pond.value", pond.value);
  pond.value.processFiles();
  //   pond.value
  //     .processFiles()
  //     .then((files) => {
  //       console.log("files", files);
  //     })
  //     .catch((err) => {
  //       console.log("err", err);
  //     });
};

const handleFilePondError = (file, status) => {
  console.log("file", file);
  console.log(" status", status);
};
const handleFilePondInit = () => {
  // FilePond instance methods are available on `this.$refs.pond`

  /* eslint-disable */
  console.log("FilePond has initialized");
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>
