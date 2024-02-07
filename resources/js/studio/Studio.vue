<template>
    <v-dialog
        v-model="studioSettings.dialog"
        persistent
        width="1000"
        style="min-height: 400px"
    >
        <template v-slot:activator="{ props }">
            <v-btn v-bind="props" class="mb-3" :append-icon="mdiCloudUpload"
                >Upload</v-btn
            >
        </template>
        <template v-slot:default="{ isActive }">
            <v-card>
                <v-card-title class="d-flex justify-space-between align-center">
                    Media Files
                    <v-btn
                        :icon="mdiClose"
                        variant="flat"
                        size="small"
                        @click="isActive.value = false"
                    ></v-btn>
                </v-card-title>
                <v-card-text>
                    <div class="d-flex align-center flex-wrap">
                        <v-btn
                            :class="`${
                                selectedTab == 'uploader'
                                    ? 'bg-grey-lighten-4'
                                    : 'bg-primary'
                            } rounded-0`"
                            variant="flat"
                            @click="selectedTab = 'uploader'"
                            style="border-top-left-radius: 4px !important"
                        >
                            Uploader
                        </v-btn>
                        <v-btn
                            :class="`${
                                selectedTab == 'studio'
                                    ? 'bg-grey-lighten-4'
                                    : 'bg-primary'
                            } rounded-0`"
                            variant="flat"
                            @click="selectedTab = 'studio'"
                            style="border-top-right-radius: 4px !important"
                        >
                            Media Files
                        </v-btn>
                    </div>
                    <div>
                        
                        <v-sheet class="bg-grey-lighten-4">
                            <MediaFiles
                                v-if="selectedTab == 'studio'"
                                :multi-select="studioSettings.multiSelect"
                                :type="filepondOptions.type"
                                @selected="selectedResponse"
                            />
                            <FilePondUploader
                                v-if="selectedTab == 'uploader'"
                                @uploaded="uploadReponse"
                                :options="filepondOptions"
                            />
                        </v-sheet>
                    </div>
                </v-card-text>
            </v-card>
        </template>
    </v-dialog>
</template>
<script setup>
import { ref, watch } from "vue";
import { mdiCloudUpload, mdiClose } from "@mdi/js";
import FilePondUploader from "./FilePondUploader.vue";
import MediaFiles from "./MediaFiles.vue";

const emit = defineEmits(["select"]);

const props = defineProps({
    options: {
        type: Object,
        default: null,
    }, 
});

const filepondOptions = ref({
    height: "150px",
    width: "100%",
    type: props.options.type ? props.options.type : "asset",
});

const studioSettings = ref({
    dialog: false,
    multiSelect: props.options?.multiSelect ? props.options?.multiSelect : false,
});
 
watch(
    () => props.options,
    (newVal) => {
 
        studioSettings.value = { ...studioSettings.value, ...newVal };
      //  console.log("watch studio settings", studioSettings.value);
    }
);

// tabs
const selectedTab = ref("studio"); // studio, uploader

// emit selected file
const selectedResponse = (v) => {
    emit("select", v);
    studioSettings.value.dialog = false;
};

const uploadReponse = () => {
    selectedTab.value = "studio";
};
</script>
