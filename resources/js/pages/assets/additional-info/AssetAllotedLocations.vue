<template>
  <v-row>
    <div class="v-col-12 pt-0 pb-2">
      <v-card>
        <v-table density="compact">
          <thead>
            <tr>
              <th class="text-left text-primary">#</th>
              <th class="text-left text-primary">Transferred from</th>
              <th class="text-left text-primary">Remarks</th>
              <th class="text-left text-primary">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in props.asset.allotted_informations" :key="item.id">
              <td>{{ index + 1 }}</td>
              <td>{{ item.location?.title }}</td>
              <td>{{ item.remarks }}</td>
              <td>{{ useFormatDate(item.created_at) }}</td>
            </tr>
          </tbody>
        </v-table>
        <v-sheet
          v-if="
            props.asset.allotted_informations &&
            props.asset.allotted_informations.length == 0
          "
          class="pa-3 text-center"
          >No record at the moment</v-sheet
        >
      </v-card>
    </div>
  </v-row>
</template>

<script setup>
 
import { useFormatDate } from "@/composables/formatDate.js";
const props = defineProps({
  asset: {
    type: Object,
    default: null,
  },
});
</script>
