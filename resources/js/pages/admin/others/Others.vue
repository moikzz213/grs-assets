<template>
  <v-container>
    <AppPageHeader title="Others - Status Setup" />
    <v-row class="mt-5">
      <div class="v-col py-0">
        <v-btn
          class="mr-3 mb-3"
          v-for="(item, index) in statusTypeButtons"
          :key="index"
          :color="`${selectedStatusType == item ? 'primary' : 'white'}`"
          @click="selectedStatusType = item"
        >
          {{ item.replace("-", " ") }}
        </v-btn>
      </div>
    </v-row>
    <v-row class="mb-3">
      <StatusType :type="selectedStatusType" />
    </v-row>
  </v-container>
</template>
<script setup>
import { ref } from "vue";
import AppPageHeader from "@/components/ApppageHeader.vue";
import StatusType from "./types/StatusType.vue";
const statusTypeButtons = ref([
  "asset",
  "condition-type",
  "incident-type",
  "incident",
  "maintenance",
  "urgency",
]);
const selectedStatusType = ref("asset");
</script>
