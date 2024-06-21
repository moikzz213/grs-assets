<template>
    <div>
      <v-card :loading="isLoading">
        
          <v-card-title class="text-primary text-capitalize mb-3">
           APPROVAL TRANSFER  
           <div class="text-caption">TRANSFER ASSET / REQUEST ASSET</div> 
           <div class="text-caption mt-4 text-capitalize">FROM: {{ props.user.display_name }}</div>
          </v-card-title>
          <v-card-text class="pb-6"> 
            <v-text-field 
            label="Search Employee Code" 
            v-model="employeeCode"
            item-value="id"
            item-title="title"
            variant="outlined"
            :append-inner-icon="mdiMagnify"
            hide-details
            density="compact"
            class="mb-2"
            @click:append-inner="searchEmployee"
            @keydown.enter="searchEmployee"
            /> 

            <v-autocomplete
            v-if="employeeValidate"
            label="Select Approval Type" 
            v-model="approvalType"
            :items="approvalTypes"
            item-value="value"
            item-title="label"
            variant="outlined" 
            hide-details
            density="compact"
             @update:modelValue="selectApprovalType"
            class="my-4" 
            /> 
            <div :class="`${responseMode} my-5`">
                {{ msg }}
            </div>
         
            <div class="d-flex align-center">
            
              <v-btn :disabled="!isValidate" :loading="isLoading" color="primary" @click="transferApproval"> Transfer approval confirmation</v-btn>
            </div>
            <div class="text-caption mt-2">
                Note: Be sure that the employee has been added in Approval Setup.
            </div>
          </v-card-text>
       
      </v-card>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { useAuthStore } from "@/stores/auth";
  import { mdiMagnify } from "@mdi/js";
  import { clientKey } from "@/services/axiosToken";

  const props = defineProps(["user"]);
  const authStore = useAuthStore();  
  const employeeCode = ref(''); 
  const isLoading = ref(false);

  const isValidate = ref(false);
  const msg = ref('');
 
  const dataTransfer = ref({});
  const responseMode = ref('');
  const approvalType = ref(null);

  const approvalTypes = ref([
    {
      label: 'All',
      value: 'all',
    },
    {
      label: 'Change Receiver only',
      value: 'receiver',
    },
    {
      label: 'Change Transport only',
      value: 'transport',
    },
    {
      label: 'Change Releasing only',
      value: 'releasing',
    },

  ]);

  const selectApprovalType = () => {  
    isValidate.value = true;
  }

  const employeeValidate = ref(false);
  const searchEmployee = async () => {
    isValidate.value = false;
    employeeValidate.value = false;
    isLoading.value = true;  
    
    await clientKey(authStore.token)
      .get("/api/admin/add-new/profile-by/ecode/"+ employeeCode.value)
      .then((response) => {
        responseMode.value = 'text-error';
        msg.value = "Employee Not Found.";
        if(response?.data?.id){
            responseMode.value = 'text-info';
            dataTransfer.value = response.data; 
            msg.value = "Employee: "+ response.data.display_name;
            employeeValidate.value = true;
        }
        isLoading.value = false; 
      })
      .catch((err) => {
        isLoading.value = false;
        
      });
  }; 

  const transferApproval = async () => {
        isLoading.value = true;  
        responseMode.value = 'text-info';

        let formData = {
            from: props.user?.id,
            to: dataTransfer.value.id,
            type: approvalType.value
        }
        await clientKey(authStore.token)
        .post("/api/user-transfer/approval", formData)
        .then((response) => {
        
            if(response.data.data){
                responseMode.value = 'text-success';
                msg.value = "Success: Pending approvals has been transfered to "+ dataTransfer.value.display_name; 
            }else{
                msg.value = "Info: No pending approvals for this employee.";  
            }
        
            isValidate.value = false;
            isLoading.value = false;
            
        })
        .catch((err) => { 
            isLoading.value = false;
            responseMode.value = 'text-error';
            msg.value = "Error: Transfer has been failed!"; 
            isValidate.value = true;
        });
  }
   
  </script>
  