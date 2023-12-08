<template>
    <GuestLayout>
      <div
        class="mx-auto px-3 text-center"
        style="z-index: 1; max-width: 400px; width: 100%; margin-top: 100px"
      >
        <WhiteLogo width="100%" />
        <div class="text-subtitle-1 text-white">{{ appName }}</div>
      </div>
  
      <v-card class="mt-8 pa-3 rounded-lg elevation-3" width="90%" max-width="450">
        <v-card-title class="px-5 pb-0 primary--text">Reset Password</v-card-title>
        <v-card-text class="py-4">
            <v-text-field
              v-model="credentials.ecode"
              variant="outlined"
              class="border-radius"
              autocomplete="off"
              label="Enter Employee Code"
              :error="hasError"
            >
            </v-text-field>
            <v-btn
              @click="resetPassword"
              width="100%"
              color="primary"
              height="55"
              large
              :disabled="resetBtn"
              :loading="loadingLogin"
              >Send Password reset link</v-btn
            > 
            <div class="text-info mt-4 text-center">{{  message  }}</div>
          
        </v-card-text>
      </v-card>
    </GuestLayout>
  </template>
  
  <script setup>
  import { ref } from "vue"; 
  import axios from "axios";
  import GuestLayout from "../layouts/GuestLayout.vue";
  import WhiteLogo from "@/Components/logo/WhiteLogo.vue"; 
 
  const appName = ref(import.meta.env.VITE_APP_NAME);
  
  // login
  const loadingLogin = ref(false);
  const credentials = ref({ecode: ''});
  const hasError = ref(false);
  const message = ref("");
  const resetBtn = ref(false);
  const resetPassword = () => { 
    hasError.value = false;
    if(credentials.value.ecode.length == 0){
      message.value = "Enter Employee Code."; 
      hasError.value = true;
      return;
    }
    resetBtn.value = true;
    loadingLogin.value = true;
    
    axios.post("/api/send-mail-reset-password", {ecode: credentials.value.ecode})
      .then((res) => { 
        loadingLogin.value = false;
          message.value = res.data.message;
          credentials.value.ecode = '';
      })
      .catch((err) => {});
  };
</script>