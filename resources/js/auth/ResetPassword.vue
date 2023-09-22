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
          <Form as="v-form" :validation-schema="validation"> 
            <Field
            name="username"
            v-slot="{ field, errors }"
            v-model="credentials.username"
          >
            <v-text-field
              v-model="credentials.username"
              v-bind="field"
              label="Employee Code" 
              variant="outlined"
              density="compact"
              disabled="disabled"
            ></v-text-field>
          </Field>
          <Field
            name="password"
            v-slot="{ field, errors }"
            v-model="credentials.password"
          >
            <v-text-field
              v-model="credentials.password"
              v-bind="field"
              label="Set New Password"
              type="password"
              variant="outlined"
              density="compact"
              :error-messages="errors"
            ></v-text-field>
          </Field>
          <Field
            name="password_confirmation"
            v-slot="{ field, errors }"
            v-model="credentials.password_confirmation"
          >
            <v-text-field
              v-model="credentials.password_confirmation"
              v-bind="field"
              label="Confirm New Password"
              type="password"
              variant="outlined"
              density="compact"
              :error-messages="errors"
            ></v-text-field>
          </Field>
          <div class="align-center"> 
            <v-btn
              @click="resetPassword"
              width="100%"
              color="primary"
              height="55"
              large
              :disabled="btnReset"
              :loading="loadingLogin"
              >Reset Password</v-btn
            > 
            <div class="mt-5" style="cursor: pointer;" @click="homePage">back to Login</div>
          </div>          
        </Form> 
        <div :class="`${hasError ? 'text-error' : 'text-success'}  mt-4 text-center`">{{  message  }}</div>
            
        </v-card-text>
      </v-card>
    </GuestLayout>
  </template>
  
  <script setup>
  import { ref } from "vue"; 
  import GuestLayout from "../layouts/GuestLayout.vue";
  import WhiteLogo from "@/Components/logo/WhiteLogo.vue"; 
  import { Form, Field } from "vee-validate";
  import { useRoute, useRouter } from "vue-router";
  const appName = ref(import.meta.env.VITE_APP_NAME);
  import * as yup from "yup";
  const sanctumBaseURL = ref(import.meta.env.VITE_SANCTUM_USER_URL);
  const key = ref(import.meta.env.VITE_APP_KEY);
  // login
  const loadingLogin = ref(false);
  const credentials = ref({username: '',password_confirmation:'', password:'', url: key.value});
  
  const hasError = ref(false);
  const message = ref("");
  const route = useRoute();
  const router = useRouter();
  const btnReset = ref(true);
  credentials.value.username = route.query?.ecode;

  if(credentials.value.username){
    btnReset.value = false;
  } 

  const homePage = () => {
    router.push({ path: "/login" });
  };
   
  const validation = yup.object({
    password: yup.string().min(5).required(),
    password_confirmation: yup
      .string()
      .required()
      .oneOf([yup.ref("password")], "Passwords do not match"),
  });

  const resetPassword = async () => {  
    hasError.value = false;
    message.value = '';
    if(credentials.value.password == 0){
      message.value = "Enter Password";
      hasError.value = true;
      return;
    }
    loadingLogin.value = true;
    await axios.post(sanctumBaseURL.value+"/api/application/reset-password", credentials.value)
        .then((res) => { 
          credentials.value = {  
              password: "11111111111111111111111",
              password_confirmation: "11111111111111111111111", 
          }; 
          btnReset.value = true;
          loadingLogin.value = false;
          message.value = res.data.msg;
        
      })
      .catch((err) => {});
  };
</script>