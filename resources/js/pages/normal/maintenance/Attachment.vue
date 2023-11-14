<template>
    <div>
        <v-card  v-if="props.objectdata.profile_id == loggedID">
            <v-card-text>
                <file-pond 
                    name="filename"
                    ref="pond"
                    :credits="''"
                    label-idle="Select or Drop image (jpeg, jpg, png) here... max 6 files only"
                    v-bind:allow-multiple="true"
                    accepted-file-types="image/jpeg, image/png"
                    v-bind:files="myFiles"
                    :server="uploadOptions"
                    v-on:init="handleFilePondInit"
                    max-files="6"
                    instantUpload="false"
                />
                <v-btn @click="uploadFileData">Upload</v-btn>
            </v-card-text>
        </v-card>
        <v-card class="mt-2">
            <v-card-text>
                <v-row>
                    <div class="v-col-12 text-uppercase font-weight-bold">
                        Uploaded Files
                    </div>
                </v-row>
                <v-row v-if="uploadedFiles && uploadedFiles.length > 0">
                    <div
                        class="v-col-12 v-col-md-3 text-center parent-row-show"
                        v-for="(item, index) in uploadedFiles"
                        :key="item.id"
                    >
                        <v-img
                            @click="enlargeImage(item, true)"
                            class="mx-3 cursor-pointer"
                            :src="`/storage/uploads/${item.path}`"
                            alt="File"
                            maxHeight="150"
                            style="border: 1px solid #e2e2e2"
                        />
                        <v-btn
                            v-if="authStore.user.profile.id == item.profile_id"
                            @click="deleteImage(item, index)"
                            class="show-hover mx-auto mt-2"
                            size="small"
                            color="error"
                            >Delete Image</v-btn
                        >
                    </div>
                </v-row>
                <v-row v-else>
                    <div class="v-col-12">No file found.</div>
                </v-row>
            </v-card-text>
        </v-card>
        <v-dialog maxWidth="900" v-model="isActiveImage">
            <v-btn
                size="small"
                @click="() => enlargeImage('', false)"
                :icon="mdiCloseCircle"
                style="
                    position: absolute;
                    right: -10px;
                    top: -10px;
                    z-index: 9999;
                "
                color="error"
            >
            </v-btn>
            <v-card class="text-center">
                <v-toolbar
                    color="primary"
                    :title="`Filename: ${activeImage.original_name}`"
                >
                </v-toolbar>

                <v-card-text>
                    <img
                        v-if="activeImage"
                        :src="`/storage/uploads/${activeImage.path}`"
                        alt="Image"
                    />
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import vueFilePond from "vue-filepond";
import "filepond/dist/filepond.min.css";
import { useAuthStore } from "@/stores/auth";
import { mdiCloseCircle } from "@mdi/js";
import { clientKey } from "@/services/axiosToken";
const emit = defineEmits(["deleted"]);
const props = defineProps({
    objectdata: {
        type: Object,
        default: {}
    },
    incidentId: {
        type: Number,
        default: 0,
    },
    files: {
        type: Array,
        default: [],
    },
});
console.log("objectdata", props.objectdata.profile_id);
const authStore = useAuthStore();
const myFiles = ref([]);
const pond = ref(null);
const FilePond = vueFilePond();
const uploadOptions = {
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
        formData.append("profile_id", authStore.user.profile.id);
        formData.append("ID", props.incidentId);
        formData.append("type", "incident");

        const request = new XMLHttpRequest();
        request.open("POST", "/api/upload-files/docs-images");

        // Should call the progress method to update the progress to 100% before calling load
        // Setting computable to false switches the loading indicator to infinite mode
        request.upload.onprogress = (e) => {
            progress(e.lengthComputable, e.loaded, e.total);
        };

        // Should call the load method when done and pass the returned server file id
        // this server file id is then used later on when reverting or restoring a file
        // so your server knows which file to return without exposing that info to the client
        request.onload = function () {
            if (request.status >= 200 && request.status < 300) {
                // the load method accepts either a string (id) or an object
                load(request.responseText);
            } else {
                // Can call the error method if something is wrong, should exit after
                error("oh no");
            }
        };

        request.send(formData);

        // Should expose an abort method so the request can be cancelled
        return {
            abort: () => {
                // This function is entered if the user has tapped the cancel button
                request.abort();

                // Let FilePond know the request has been cancelled
                abort();
            },
        };
    },
};

const loggedID = ref(authStore.user.profile.id);
const activeImage = ref("");
const isActiveImage = ref(false);
const enlargeImage = (path, status) => {
    isActiveImage.value = status;
    activeImage.value = path;
};
const handleFilePondInit = () => {
    console.log("initialised");
};

const uploadFileData = () => {
    pond.value.processFiles().then((files) => {
        let uploadNewFile = {};
        files.map((o, i) => {
            uploadNewFile = JSON.parse(o.serverId)[0][0];
            uploadedFiles.value.push(uploadNewFile);
        });
    });
};

const deleteImage = (image, index) => {
    let formData = { data: image, ID: props.incidentId };
    clientKey(authStore.token)
        .post("/api/delete/uploaded-file", formData)
        .then((res) => {
            uploadedFiles.value.splice(index, 1);  
            emit("deleted", res.data.msg);
        })
        .catch((err) => {});
};

const uploadedFiles = ref([]);

onMounted(() => {
    uploadedFiles.value = props.files;
});
</script>
<style>
.parent-row-show:hover .show-hover {
    display: block;
}
.show-hover {
    display: none;
}
</style>
