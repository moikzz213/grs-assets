<template>
  <div>
    <v-dialog width="600" persistent>
      <template v-slot:activator="{ props }">
        <v-btn
          v-bind="props"
          color="primary"
          :prepend-icon="mdiCloudUpload"
          text="Upload"
        >
        </v-btn>
      </template>
      <template v-slot:default="{ isActive }">
        <v-card>
          <v-card-title>Upload</v-card-title>
          <v-card-text>
            <FilePondUploader />
            <div class="d-flex">
              <v-btn class="ml-auto mr-1" flat @click="isActive.value = false"
                >Cancel</v-btn
              >
              <v-btn color="primary">Upload</v-btn>
            </div>
          </v-card-text>
        </v-card>
      </template>
    </v-dialog>
  </div>
</template>

<script setup>
import { mdiCloudUpload } from "@mdi/js";
import FilePondUploader from "./FilePondUploader.vue";
</script>
