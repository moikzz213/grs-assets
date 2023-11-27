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
          <!-- <div class="v-col">
            <v-icon
              :title="`${item.status == 'active' ? 'Active' : 'Disabled'}`"
              v-if="item.id"
              :icon="`${
                item.status == 'active' ? mdiEyeCheckOutline : mdiEyeRemoveOutline
              }`"
              :color="`${item.status == 'active' ? 'success' : 'error'}`"
              class="my-auto mr-2"
            ></v-icon>
          </div> -->
        </v-row>
      </v-card-text>
    </v-card>
  </div>
</template>

<script setup>
import { ref } from "vue";
import {
  mdiTrashCan,
  mdiPlus,
  mdiEyeOff,
  mdiEyeCheckOutline,
  mdiEyeRemoveOutline,
  mdiEyeRefresh,
} from "@mdi/js";
import AppStatusDropDown from "@/components/AppStatusDropDown.vue";

// auth
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

// status
import { useStatusStore } from "@/stores/status";
const statusStore = useStatusStore();
if (statusStore.urgencies.length < 1) {
  statusStore.getStatuses(authStore.token);
}
const loadingAppStatusDropDown = ref([]);
const loadingSave = ref(false);
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
  // update status on DB
  console.log("currentState", v);
  console.log("appStatusDropDownRes", loadingAppStatusDropDown.value.push(v.model_id));
  // call status update from status store
  setTimeout(() => {
    loadingAppStatusDropDown.value = [];
  }, 3000);
};
</script>
