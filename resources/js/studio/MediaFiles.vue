<template>
  <div class="pa-3 d-flex flex-column" style="min-height: 400px">
    <div class="mb-3">
      <v-row v-if="files.length > 0" class="ma-0">
        <div v-for="file in files" :key="file.id" class="v-col-12 v-col-md-2 pa-1 text-center" style="border:1px solid #e7e7e7; border-radius: 5px;
        "> 
          <v-img
          v-if="mimeTypes.includes(file.mime)"
            cover
            :aspect-ratio="1"
            :class="`${file.mime} cursor-pointer`"
            :lazy-src="baseURL + '/assets/images/placeholder-image.png'"
            :src="baseURL + '/file/' + file.path"
            @click="() => selectFile(file)"
            :style="`border: 4px solid ${
              theSelectedFiles.includes(file.id) == true ? '#ffed00' : 'transparent'
            }`"
          >
          </v-img>
          <v-img
          v-else
            cover
            :aspect-ratio="1"
            :class="`${file.mime} cursor-pointer`"
            :lazy-src="baseURL + '/assets/images/placeholder-image.png'"
            :src="baseURL + '/assets/images/pdf-image.png'"
            @click="() => selectFile(file)"
            :style="`border: 4px solid ${
              theSelectedFiles.includes(file.id) == true ? '#ffed00' : 'transparent'
            }`"
          >
          </v-img>
          <small style="font-size:10px;">{{ limitText(file.title, 25) }}.{{ getExtension(file) }}</small>
          <div style="font-size:10px;">{{ useFormatDateTime(file.created_at) }}</div> 
        </div>
      </v-row>
      <v-sheet
        v-else
        class="w-100 d-flex align-center justify-center"
        min-height="150"
        color="grey-lighten-3"
      >
        No media files at the moment
      </v-sheet>

      <v-row v-if="files.length > 0" class="ma-0 mt-5">
          <div class="ma-auto">
            <v-btn color="info" @click="loadMore" v-if="getCurrentLoad < getMaxLimit">Load More</v-btn>
            <div   v-else>All  Files Loaded.</div>
          </div>
      </v-row>
    </div>
    <div class="mt-auto d-flex justify-end">
      <v-btn
        :disabled="selectedFile.length > 0 ? false : true"
        color="primary"
        @click="useFile"
        >Select</v-btn
      >
    </div>
  </div>
</template>
<script setup>
import { ref, computed } from "vue";
import { clientKey } from "@/services/axiosToken";
import { useAuthStore } from "@/stores/auth";
import { limitText } from "@/composables/generateRandomString.js";
import { useFormatDateTime } from "@/composables/formatDate.js"
const props = defineProps({
  multiSelect: {
    type: Boolean,
    default: false,
  },
  type: {
    type: String,
    default: 'asset'
  }
});

const authStore = useAuthStore();
const baseURL = window.location.origin;
const emit = defineEmits(["selected"]);

// get files
const files = ref([]);
const loadingFiles = ref(false);
const loadMoreNumber = ref(1);
const getMaxLimit = ref(0);
const getCurrentLoad= ref(0);
const mimeTypes = ref(['image/jpeg', 'image/gif','image/png', 'image/jpg']);
 
const getFiles = async () => {
  loadingFiles.value = true;
  await clientKey(authStore.token)
    .get("/api/system/file/all/"+props.type+"?page=" + loadMoreNumber.value)
    .then((res) => {
      loadingFiles.value = false; 

      if(files.value.length == 0){
        files.value = res.data.data; 
       
      }else{
        files.value = [...files.value, ...res.data.data];  
      } 
       
      getCurrentLoad.value = res.data.to;
      getMaxLimit.value = res.data.total;
    })
    .catch((err) => { 
      loadingFiles.value = false;
    });
}; 

getFiles();  

const loadMore = () => {
  loadMoreNumber.value = loadMoreNumber.value + 1;
  getFiles();
}
 
const getExtension = (ext) => {
  let mimeType = ext.path.split(".");
   return mimeType[mimeType.length - 1];
}

// select file
const selectedFile = ref([]);
const selectFile = (file) => {
  const fileExist = selectedFile.value.find((f) => f.id == file.id);
  if (!fileExist) {
    if(selectedFile.value.length  >= 1 && !props.multiSelect){
      selectedFile.value[0] = file;
    }else{
      selectedFile.value.push(file);
    }
  } else {
    selectedFile.value = selectedFile.value.filter((f) => f.id !== file.id);
  }
 
};

// the selected files
const theSelectedFiles = computed(() => {
  return selectedFile.value.map((sf) => sf.id);
});

// use file
const useFile = () => {
  emit("selected", selectedFile.value);
};
</script>
