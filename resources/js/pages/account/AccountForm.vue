<template>
  <v-card>
    <v-card-title class="text-primary text-capitalize mb-3"
      >Account Settings</v-card-title
    >
    <v-card-text>
      <Form as="v-form" :validation-schema="validation">
        <div class="d-flex ">
          <div class="mb-2 text-body-2 mr-2 mt-2">Status</div>

        <v-menu :disabled="isOwnAccount">
          <template v-slot:activator="{ props }">
            <v-btn v-bind="props" class="mb-6" :color="statusColor">
              {{ user.data.status }}
            </v-btn>
          </template>
          <v-list density="compact">
            <v-list-item
              v-for="(item, index) in statusList"
              :key="index"
              :value="index"
              @click="() => selectStatus(item.title)"
            >
              <v-list-item-title class="text-overline d-flex align-center">
                <v-icon :color="item.color" :icon="mdiCircleMedium" class="mr-1"></v-icon>
                <div>{{ item.title }}</div></v-list-item-title
              >
            </v-list-item>
          </v-list>
        </v-menu>
        </div>

          <v-text-field
            v-model="user.data.username"
            v-bind="field"
            label="Username"
            variant="outlined"
            density="compact"
            hide-details
            class="mb-2"
            :disabled="true"
          />

        <Field name="email" v-slot="{ field, errors }" v-model="user.data.email">
          <v-text-field
            v-model="user.data.email"
            v-bind="field"
            label="Email"
            variant="outlined"
            density="compact"
            hide-details
            class="mb-2"
            :error-messages="errors"
          />
        </Field>
        <Field name="contact" v-slot="{ field, errors }" v-model="user.data.contact">
          <v-text-field
            v-model="user.data.contact"
            v-bind="field"
            type="number"
            density="compact"
            hide-details
            label="Contact number"
            variant="outlined"
            class="mb-2"
            :error-messages="errors"
          />
        </Field>
        <Field name="role" v-slot="{ field, errors }" v-model="user.data.role">
          <v-select
            v-model="user.data.role"
            v-bind="field"
            :items="roleList"
            label="Role"
            density="compact"

            variant="outlined"
            class="mb-2"
            :error-messages="errors"
          />
        </Field>
        <v-btn color="primary" size="large" :loading="user.loading" @click="saveUser"
          >Save</v-btn
        >
      </Form>
    </v-card-text>
  </v-card>
</template>
<script setup>
import { ref, watch, computed } from "vue";
import * as yup from "yup";
import { Form, Field } from "vee-validate";
import { mdiCircleMedium } from "@mdi/js";
import { useAuthStore } from "@/stores/auth";
import { clientKey } from "@/services/axiosToken";
const authStore = useAuthStore();
const props = defineProps(["user"]);
const user = ref({
  loading: false,
  data: Object.assign({}, props.user),
});
const isOwnAccount = ref(true);

watch(
  () => props.user,
  (newVal) => {

    isOwnAccount.value = false;
    user.value.data = newVal;
  }
);

console.log("props.user", props.user);
const emit = defineEmits(["saved"]);

/**
 * Status
 */
const statusList = ref([
  {
    title: "active",
    color: "success",
  },
  {
    title: "inactive",
    color: "error",
  },
]);
const statusColor = computed(() => {
  let color = "";
  if (user.value.data.status == "active") {
    color = "success";
  }
  if (user.value.data.status == "inactive") {
    color = "error";
  }
  return color;
});
const selectStatus = (selected) => {
  user.value.data.status = selected;
};

const roleList = ref([
    { title: "Asset Supervisor-Project", value: "asset-supervisor" },
    { title: "Commercial Manager-Project", value: "commercial-manager" },
    { title: "Facility Team", value: "facility" },
    { title: "Normal", value: "normal" },
    { title: "Technical Operation", value: "technical-operation" },
    { title: "Receiving/Releasing/Transport", value: "receiving-releasing" },
]);

/**
 * Submit user
 */
let validation = yup.object({

  email: yup.string().email()
});
const saveUser = async () => {
  let data = user.value.data;

  user.value.loading = true;
  if(data.profile){
    data = data.profile;
  }
  data.profile_id = authStore.user.profile.id;

  await clientKey(authStore.token)
  .post("/api/account/profile/save", data)
    .then((response) => {
      user.value.loading = false;
      emit("saved", response.data.message);
    })
    .catch((err) => {
      user.value.loading = false;
    });
};
</script>
