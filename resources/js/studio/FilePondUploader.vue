<template>
  <div class="pa-3 d-flex flex-column" style="min-height: 400px">
    <file-pond
      name="filepond"
      ref="pond"
      instantUpload="false"
      :credits="''"
      :allow-multiple="uploadOptions.allow_multiple"
      :maxFiles="uploadOptions.max_files"
      :maxFileSize="uploadOptions.max_file_size"
      :accepted-file-types="uploadOptions.accepted_file_types"
      v-bind:server="serverSetup"
      v-bind:files="selectedFiles"
      v-on:init="handleFilePondInit"
      v-on:error="handleFilePondError"
      allowProcess="false"
    />
    <div class="mt-auto d-flex justify-end">
      <v-btn color="primary" @click="handleUpload">Upload</v-btn>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref, watch } from "vue";
import { useAuthStore } from "@/stores/auth";
// Import Vue FilePond
import vueFilePond from "vue-filepond";
import "filepond/dist/filepond.min.css";
// Import FilePond plugins
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

const authStore = useAuthStore();
const pond = ref(null);

// emits
const emit = defineEmits(["uploaded"]);

// props
const props = defineProps({
  options: {
    type: Object,
    default: null,
  },
});
 
const uploadOptions = ref({
  max_files: props.options.max_files ? props.options.max_files : 4,
  allow_multiple: props.options.allow_multiple ? props.options.allow_multiple : true,
  height: props.options.height ? props.options.height : "150px",
  width: props.options.width ? props.options.width : "100%",
  max_file_size: props.options.max_file_size ? props.options.max_file_size : "20MB",
  //   image_crop_aspect_ratio: props.options.image_crop_aspect_ratio
  //     ? props.options.image_crop_aspect_ratio
  //     : "1:1",
  accepted_file_types: props.options.accepted_file_types
    ? props.options.accepted_file_types
    : "image/jpeg, image/png", // image/jpeg, image/png, application/pdf
  type: props.options.type ? props.options.type : 'asset',
});
watch(
  () => props.options,
  (newVal) => {
    uploadOptions.value = { ...uploadOptions.value, ...newVal };
    console.log("uploadOptions.value", uploadOptions.value);
  }
);

console.log("uploadOptions", uploadOptions.value);

// FilePond
const selectedFiles = ref([]);
const FilePond = vueFilePond(
  //   FilePondPluginFileValidateType,
  //   FilePondPluginImagePreview,
  //   FilePondPluginImageExifOrientation,
  //   FilePondPluginFileValidateSize,
  //   FilePondPluginImageEdit

  FilePondPluginFileValidateSize,
  FilePondPluginImageExifOrientation,
  FilePondPluginImagePreview,
  FilePondPluginFileValidateType,
  //   FilePondPluginImageCrop,
  FilePondPluginImageResize,
  FilePondPluginImageTransform,
  FilePondPluginImageEdit
);

// fake server to simulate loading a 'local' server file and processing a file
const serverSetup = {
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
    formData.append("type", uploadOptions.value.type);
    // set XMLHttpRequest
    const request = new XMLHttpRequest();
    request.open("POST", "/api/system/file/upload");

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
        emit("uploaded");
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
  },
  revert: null,
  restore: null,
  load: null,
  fetch: null,
};

const handleFilePondInit = () => {
  // FilePond instance methods are available on `this.$refs.pond`

  /* eslint-disable */
  console.log("FilePond has initialized");
};
const handleFilePondError = (file, status) => {
  console.log("file", file);
  console.log(" status", status);
};

const handleUpload = (files) => {
  pond.value.processFiles();
  console.log("files", files);
};

console.log("uploadOptions.height", uploadOptions.value.height);
</script>

<style>
.filepond--root,
.filepond--root .filepond--drop-label {
  min-height: v-bind("uploadOptions.height");
  width: v-bind("uploadOptions.width");
}

.filepond--root .filepond--list-scroller {
  margin-top: 10px !important;
}
.filepond--item {
  width: calc(50% - 0.5em) !important;
  margin-bottom: 0.25em !important;
}
@media (min-width: 30em) {
  .filepond--item {
    width: calc(50% - 0.5em);
  }
}

@media (min-width: 50em) {
  .filepond--item {
    width: calc(33.33% - 0.5em);
  }
}
</style>
