<template>
    <div>
        <div class="v-col-12 pt-5">
            <v-row v-if="props.objectdata.profile_id == loggedID || ( loggedRole == 'technical-operation' || loggedRole == 'facility')">
                <div class="v-col-12 pb-2">
                    <Studio
                        :options="{ multiSelect: true, type: 'incident' }"
                        @select="studioSelectResponse"
                    />
                </div>
            </v-row>
            
            <v-row v-if="selectedFiles.length > 0" class="px-1">
                <div
                    v-for="(file, index) in selectedFiles"
                    :key="file.id"
                    class="v-col-12 v-col-md-2 pa-2"
                    style="position: relative"
                >
                
                    <v-btn
                        style="position: absolute; top: 0; right: 0; z-index: 1"
                        :icon="mdiClose"
                        size="26px"
                        color="error"
                        v-if="props.objectdata.profile_id == loggedID || file.profile_id == loggedID  "
                        @click="() => removeAttachment(file.id)"
                    >
                    </v-btn>
                    <v-card @click="() => openAttachment(index)">
                        <v-img
                            :src="baseURL + '/file/' + file.path"
                            cover
                            aspect-ratio="1"
                        >
                        </v-img>
                    </v-card>
                </div>

                <v-dialog
                    v-model="dialogAttachment"
                    width="95%"
                    max-width="900"
                >
                    <v-card class="bg-black">
                        <v-carousel
                            hide-delimiter-background
                            show-arrows="hover"
                            height="680px"
                            v-model="currentSlider"
                        >
                            <v-carousel-item
                                v-for="(item, i) in selectedFiles"
                                :key="i"
                                reverse-transition="fade"
                                transition="fade"
                            >
                                <div
                                    style="height: 680px; width: 100%"
                                    class="d-flex align-center justify-center"
                                >
                                    <v-img
                                        :src="baseURL + '/file/' + item.path"
                                    ></v-img>
                                </div>
                            </v-carousel-item>
                        </v-carousel>
                    </v-card>
                </v-dialog>
            </v-row>
            <v-row v-else>
                <div class="v-col-12 pt-0 pb-2">
                    <v-sheet color="grey-lighten-4" class="pa-6 text-center">
                        Attachment is empty at the moment.
                    </v-sheet>
                </div>
            </v-row>
            <v-row v-if="props.objectdata.profile_id == loggedID || ( loggedRole == 'technical-operation' || loggedRole == 'facility')">
                <div class="v-col-12">
                    <v-btn color="primary" @click="saveImage">Save</v-btn>
                </div>
            </v-row>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted, computed, watch } from "vue";

import "filepond/dist/filepond.min.css";
import { useAuthStore } from "@/stores/auth";
import Studio from "@/studio/Studio.vue";
import { mdiClose } from "@mdi/js";
import { clientKey } from "@/services/axiosToken";

const emit = defineEmits(["save"]);
const props = defineProps({
    objectdata: {
        type: Object,
        default: {},
    },
    incidentId: {
        type: Number,
        default: 0,
    },
    attachment: {
        type: Array,
        default: [],
    },
});

const authStore = useAuthStore();
const baseURL = ref(window.location.origin);
const studioSelectResponse = (v) => {
    let fileExist = null;
    v.map((sudioFile) => {
        fileExist = selectedFiles.value.find((f) => f.id == sudioFile.id);
        if (!fileExist) {
            selectedFiles.value.push(sudioFile);
        }
    });
};
const dialogAttachment = ref(false);
const currentSlider = ref(1);
const openAttachment = (index) => {
    currentSlider.value = index;
    dialogAttachment.value = true;
};
const removeAttachment = (theId) => {
    selectedFiles.value = selectedFiles.value.filter((f) => f.id !== theId);
};

const selectedFiles = ref([]);
const selectedFilesIds = computed(() => selectedFiles.value.map((sf) => sf.id));
const saveImage = () => {
    let formObj = {
        id: parseInt(props.incidentId),
        file_ids: selectedFilesIds.value,
        profile_id: props.objectdata.profile_id,
    };
    clientKey(authStore.token)
        .post("/api/incident/sync-images/store-update", formObj)
        .then((res) => {
            let msg = "Attachment has been updated";
            if (selectedFilesIds.value.length == 0) {
                msg = "Attachment has been deleted";
            }
            emit("save", msg);
        })
        .catch((err) => {});
};
const loggedID = ref(authStore.user.profile.id);
const loggedRole = ref(authStore.user.profile.role);
 
onMounted(() => {
    selectedFiles.value = props.attachment; 
});
watch(
    () => props.objectdata.attachment,
    (newValue, oldValue) => {
        selectedFiles.value = newValue;
    },
    { deep: true }
);
</script>
<style>
.parent-row-show:hover .show-hover {
    display: block;
}
.show-hover {
    display: none;
}
</style>
