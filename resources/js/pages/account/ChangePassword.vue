<template>
  <div>
    <v-card :loading="password.loading">
      <Form as="v-form" :validation-schema="validation" v-slot="{ meta }">
        <v-card-title class="text-primary text-capitalize mb-3">
          Change password
        </v-card-title>
        <v-card-text class="pb-6">
          <Field
            name="password"
            v-slot="{ field, errors }"
            v-model="password.data.password"
          >
            <v-text-field
              v-model="password.data.password"
              v-bind="field"
              label="Password"
              type="password"
              variant="outlined"
              class="mb-2"
              :error-messages="errors"
            ></v-text-field>
          </Field>
          <Field
            name="password_confirmation"
            v-slot="{ field, errors }"
            v-model="password.data.password_confirmation"
          >
            <v-text-field
              v-model="password.data.password_confirmation"
              v-bind="field"
              label="Confirm Password"
              type="password"
              variant="outlined"
              class="mb-2"
              :error-messages="errors"
            ></v-text-field>
          </Field>
          <div class="d-flex align-center">
            <v-btn :disabled="!meta.valid" color="primary" size="large" @click="changePassword"> Save </v-btn>
          </div>
        </v-card-text>
      </Form>
    </v-card>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
import {  useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
const route = useRoute();
const props = defineProps(["user"]);
const authStore = useAuthStore();
const emit = defineEmits(["saved"]);

const key = ref(import.meta.env.VITE_APP_KEY);
const sanctumBaseURL = ref(import.meta.env.VITE_SANCTUM_USER_URL);
 
const password = ref({
  status: false,
  loading: false,
  data: {
    username: "",
    password: "",
    password_confirmation: "",
    url: key.value
  },
});

const validation = yup.object({
  password: yup.string().min(5).required(),
  password_confirmation: yup
    .string()
    .required()
    .oneOf([yup.ref("password")], "Passwords do not match"),
});
console.log("props.user",props.user);
const changePassword = async () => {
  
  password.value.loading = true; 
  password.value.data.username = props.user ? props.user.ecode : authStore.user.profile.ecode;
  
  await axios
    .post(sanctumBaseURL.value+"/api/application/reset-password", password.value.data)
    .then((response) => {
      emit("saved", response.data.msg);
      password.value = {
        status: false,
        loading: false, 
        data: {
          username: props.user ? props.user.ecode : route.params.ecode,
          password: "11111111111111111111111",
          password_confirmation: "11111111111111111111111",
          url: key.value
        },
      };
    })
    .catch((err) => {
      password.value.loading = false;
      console.log(err.response.data);
    });
}; 
 
</script>
