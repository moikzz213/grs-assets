<template>
    <div>
        <v-dialog
            width="600"
            persistent
            v-model="popDialog"
            @keydown.esc="cancelAddNew"
        >
            <v-card class="pt-3">
                <v-card-title class="text-h6 d-flex"
                    >{{ title }}<v-spacer></v-spacer
                    ><small class="text-error">{{ errorMessage }}</small>
                    <v-spacer></v-spacer
                ></v-card-title>
                <v-card-text>
                    <v-row> 
                        <div class="v-col-12" v-for="(item, index) in freeForm" :key="index"> 
                            <v-text-field v-model="item.type" density="compact" variant="outlined" :label="`${item.label}${item.required ? '*': ''}`" hide-details></v-text-field> 
                        </div>
                    </v-row>
                </v-card-text>
                <v-divider class="mt-2"></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        size="small"
                        text="cancel"
                        @click="cancelAddNew"
                    ></v-btn>
                    <v-btn
                        size="small"
                        v-if="confirmOk"
                        color="secondary"
                        @click="saveData"
                        >Confirm</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
        <SnackBar :options="sbOptions" />
    </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import SnackBar from "./AppSnackBar.vue";
const title = ref("");

const props = defineProps({
    dataObject: {
        type: Object,
        default: {},
    },
    addNewDialog: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: ''
    },
    freeForm:{
        type: Array,
        default: []
    }
});
const confirmOk = ref(false);
const popDialog = ref(false);
const dataObj = ref([]);
const sbOptions = ref({});
const freeForm = ref(props.freeForm);
const kpiEmit = defineEmits(["savedResponse"]);
console.log("freeForm",freeForm.value);
const cancelAddNew = () => {
    popDialog.value = false;
    dataObj.value = [];
    confirmOk.value = false;
    kpiEmit("cancelled", popDialog.value);
};


watch(
    () => props.addNewDialog,
    (newVal) => {
        popDialog.value = newVal;
        title.value = "New "+ props.title;

        if(props.dataObject.type){
           
            dataObj.value = props.dataObject;
            title.value = "Edit "+ props.title;
        
        }
        
    }
);
</script>
