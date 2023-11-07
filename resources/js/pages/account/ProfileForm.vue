<template>
  <v-card>
    <v-card-title class="text-primary text-capitalize">Profile Settings</v-card-title>
    <v-card-text>
      <Form as="v-form" :validation-schema="validation" v-slot="{ meta }">
        <!-- <div style="width: 100%; max-width: 320px; border-radius: 12px" class="mb-6">
          <v-img
            :src="'./assets/images/placeholder-user.png'"
            class="rounded-lg mb-2"
          ></v-img>
        </div> -->
        <SingleUploader class="mb-6" :options="{ size: '320px' }" />

        <Field name="ecode" v-slot="{ field }" v-model="profileData.data.ecode">
          <v-text-field
            v-model="profileData.data.ecode"
            v-bind="field"
            label="Employee ID"
            density="compact"
            variant="outlined"
            :disabled="true"
          />
        </Field>
        <Field
          name="display_name"
          v-slot="{ field, errors }"
          v-model="profileData.data.display_name"
        >
          <v-text-field
            v-model="profileData.data.display_name"
            v-bind="field"
            label="Full name"
            density="compact"
            variant="outlined"
            :error-messages="errors"
          />
        </Field>
        <Field
          name="first_name"
          v-slot="{ field, errors }"
          v-model="profileData.data.first_name"
        >
          <v-text-field
            v-model="profileData.data.first_name"
            v-bind="field"
            label="First name"
            density="compact"
            variant="outlined"
            :error-messages="errors"
          />
        </Field>
        <Field
          name="last_name"
          v-slot="{ field, errors }"
          v-model="profileData.data.last_name"
        >
          <v-text-field
            v-model="profileData.data.last_name"
            v-bind="field"
            label="Last name"
            density="compact"
            variant="outlined"
            :error-messages="errors"
          />
        </Field>
        <v-autocomplete
          v-bind="field"
          label="Company"
          :items="companies"
          v-model="profileData.data.company_id"
          item-value="id"
          item-title="title"
          variant="outlined"
          density="compact"
          class="mb-2"
        />
        <v-btn
          color="primary"
          :loading="profileData.loading"
          @click="saveProfile"
          :disabled="!meta.valid"
          >Save</v-btn
        >
      </Form>
    </v-card-text>
  </v-card>
</template>
<script setup>
// import SingleUploader from "@/studio/SingleUploader.vue";
import SingleUploader from "@/studio/SingleUploader.vue";
import { ref, watch } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
import { useRoute } from "vue-router";
import { clientKey } from "@/services/axiosToken";
import nationalities from "@/json/nationalities.json";
import { useAuthStore } from "@/stores/auth";
import axios from "axios";

const authStore = useAuthStore();
const route = useRoute();
const props = defineProps(["user"]);

// profile
const emit = defineEmits(["saved"]);
const nationalityList = ref([]);
nationalityList.value = nationalities;
const profileData = ref({
  loading: false,
  data: {
    user_id: route.params.id,
    display_name: null,
    dob: null,
    nationality: null,
  },
});
profileData.value.data = Object.assign({}, props.user);

watch(
  () => props.user,
  (newVal) => {
    profileData.value.data = Object.assign({}, newVal);
  }
);

const getProfile = () => {
  profileData.value.data = props.user.profile;
};
if (props.user?.id) {
  getProfile();
}

const companies = ref([]);

const fetchCompanies = async () => {
  await clientKey(authStore.token)
    .get("/api/fetch/companies")
    .then((res) => {
      companies.value = res.data;
    })
    .catch((err) => {});
};
fetchCompanies();

// save profile
let validation = yup.object({
  ecode: yup.string().required(),
  display_name: yup.string().required(),
  first_name: yup.string().required(),
  last_name: yup.string().required(),
});

const saveProfile = async () => {
  profileData.value.loading = true;
  profileData.value.data = {
    ...profileData.value.data,
    ...{
      id: props.user?.profile?.id ? props.user.profile.id : props.user.id,
    },
  };
  await clientKey(authStore.token)
    .post("/api/account/profile/save", profileData.value.data)
    .then((response) => {
      profileData.value.loading = false;
      emit("saved", response.data.message);
    })
    .catch((err) => {
      profileData.value.loading = false;
    });
};
</script>
