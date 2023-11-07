<template>
  <div id="component">
    <file-pond
      name="filepond"
      ref="pond"
      instantUpload="false"
      allow-multiple="true"
      accepted-file-types="image/jpeg, image/png, application/pdf"
      v-bind:server="serverSetup"
      v-bind:files="selectedFiles"
      v-on:init="handleFilePondInit"
      :credits="{}"
    />
  </div>
</template>

<script setup>
import axios from "axios";
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
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";

// Create component
const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginImageExifOrientation,
  FilePondPluginImagePreview
);

const selectedFiles = ref([
  {
    source: "photo.jpeg",
    options: {
      type: "local",
    },
  },
]);

// fake server to simulate loading a 'local' server file and processing a file
const serverSetup = {
  // timeout: 7000,
  process: (
    fieldName,
    file,
    metadata,
    load,
    error,
    progress,
    abort,
    transfer,
    options
  ) => {
    // fieldName is the name of the input field
    // file is the actual file object to send
    const formData = new FormData();
    formData.append(fieldName, file, file.name);
    formData.append('profile_id',authStore.user.profile.id);

    console.log("fieldName", fieldName);
    console.log("file", file);
    console.log("metadata", metadata);
    console.log("load", load);

    axios({
      method: "post",
      url: "api/file/upload",
      data: formData,
      headers: {
        "X-CSRF-TOKEN": document.getElementsByTagName("meta")["csrf-token"].content,
        Authorization: `Bearer ${authStore.token}`,
        "Content-Type":
          "multipart/form-data; charset=utf-8; boundary=" +
          Math.random().toString().substring(2),
        withCredentials: false,
      },
      onUploadProgress: (e) => {
        // updating progress indicator
        progress(e.lengthComputable, e.loaded, e.total);
      },
    })
      .then((response) => {
        // passing the file id to FilePond
        load(response.data.data.id);
      })
      .catch((thrown) => {
        if (axios.isCancel(thrown)) {
          console.log("Request canceled", thrown.message);
        } else {
          // handle error
        }
      });
  },
  //   process: {
  //     url: "./api/file/upload",
  //     method: "POST",
  //     headers: {
  //       "X-CSRF-TOKEN": document.getElementsByTagName("meta")["csrf-token"].content,
  //       Authorization: `Bearer ${authStore.token}`,
  //       "Content-Type":
  //         "multipart/form-data; charset=utf-8; boundary=" +
  //         Math.random().toString().substring(2),
  //       withCredentials: false,
  //     },
  //     // withCredentials: false,
  //     onload: (response) => response.key,
  //     onerror: (response) => response.data,
  //     ondata: (formData) => {
  //       formData.append("Hello", "World");
  //       console.log("formData", formData);
  //       return formData;
  //     },
  //   },
  // revert: "./revert",
  // restore: "./restore/",
  // load: "./load/",
  // fetch: "./fetch/",
  //   process: (fieldName, file, metadata, load) => {
  //     console.log("fieldName", fieldName);
  //     console.log("file", file);
  //     console.log("metadata", metadata);
  //     console.log("load", load);
  //     upload();
  //     // simulates uploading a file
  //     //   setTimeout(() => {
  //     //     load(Date.now());
  //     //   }, 1500);
  //   },
  // load: (source, load) => {
  //   // simulates loading a file from the server
  //   fetch(source)
  //     .then((res) => res.blob())
  //     .then(load);
  // },
};
const upload = async () => {
  axios.post("/api/file/upload", {
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      Authorization: `Bearer ${bearer}`,
    },
  });

  await axios({
    method: "post",
    url: "v1/upload",
    data: formData,
    cancelToken: source.token,
    onUploadProgress: (e) => {
      // updating progress indicator
      progress(e.lengthComputable, e.loaded, e.total);
    },
  })
    .then((response) => {
      // passing the file id to FilePond
      load(response.data.data.id);
    })
    .catch((thrown) => {
      if (axios.isCancel(thrown)) {
        console.log("Request canceled", thrown.message);
      } else {
        // handle error
      }
    });

  //     url: "./api/file/upload",
  //     method: "POST",
  //     headers: {
  //       "X-CSRF-TOKEN": document.getElementsByTagName("meta")["csrf-token"].content,
  //       Authorization: `Bearer ${authStore.token}`,
  //       "Content-Type":
  //         "multipart/form-data; charset=utf-8; boundary=" +
  //         Math.random().toString().substring(2),
  //       withCredentials: false,
  //     },
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
