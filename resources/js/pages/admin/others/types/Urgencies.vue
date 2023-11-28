<template>
  <div class="v-col-12 v-col-md-12">
    <v-card>
      <v-card-title>
        <div class="d-flex align-center">
          <v-btn
            :icon="mdiPlus"
            @click="AddNewStatus()"
            size="x-small"
            color="white"
            class="mr-3"
            v-if="
              authStore.user.role == 'superadmin' ||
              authStore.capabilities?.includes('add')
            "
          ></v-btn>
          <div>Urgency List</div>
          <v-btn
            :loading="loadingSave"
            @click="savePage('status')"
            color="primary"
            class="ml-auto"
            v-if="
              (authStore.user.role == 'superadmin' ||
                authStore.capabilities?.includes('edit')) &&
              statusStore.urgencies.length > 0
            "
            >Save</v-btn
          >
        </div>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text>
        <v-row v-for="(item, index) in statusStore.urgencies" :key="item.id">
          <div class="v-col">
            <v-text-field
              hide-details="auto"
              v-model="item.title"
              label="Urgency Title"
              variant="outlined"
              density="compact"
              :disabled="item.id < 10"
            ></v-text-field>
          </div>
          <div class="v-col">
            <v-text-field
              hide-details="auto"
              v-model="item.interval"
              label="Notification Interval (in days)"
              variant="outlined"
              density="compact"
              :disabled="item.id < 10"
            ></v-text-field>
          </div>
          <div class="v-col-2 d-flex align-center justify-center">
            <AppStatusDropDown
              @update="appStatusDropDownRes"
              :list="statusList"
              :current-state="item"
              :loading="loadingAppStatusDropDown.includes(item.id) == true ? true : false"
            />
          </div>
        </v-row>
      </v-card-text>
    </v-card>
    <AppSnackbar :options="sbOptions" />
  </div>
</template>

<script setup>
import { ref } from "vue";
import { mdiPlus } from "@mdi/js";
import AppStatusDropDown from "@/components/AppStatusDropDown.vue";
import AppSnackbar from "@/components/AppSnackbar.vue";

// auth
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

// ui
const sbOptions = ref({});

// status
import { useStatusStore } from "@/stores/status";
const statusStore = useStatusStore();
if (statusStore.urgencies.length < 1) {
  statusStore.getStatuses(authStore.token);
}

const loadingSave = ref(false);
const loadingAppStatusDropDown = ref([]);
const statusList = ref([
  {
    title: "active",
    color: "success",
  },
  {
    title: "disabled",
    color: "error",
  },
]);
const appStatusDropDownRes = (v) => {
  // activate loading
  loadingAppStatusDropDown.value.push(v.model_id);

  // call status update from status store
  let data = {
    id: v.model_id,
    type: "urgency",
    status: v.state,
  };
  statusStore
    .updateStatus(data, authStore.token)
    .then((res) => {
      // deactivate loading
      loadingAppStatusDropDown.value = [];
      sbOptions.value = {
        status: true,
        type: "success",
        text: res.data.message,
      };
    })
    .catch((err) => {
      // deactivate loading
      loadingAppStatusDropDown.value = [];
      sbOptions.value = {
        status: true,
        type: "success",
        text: "Error while updating status",
      };
    });
};
</script>
