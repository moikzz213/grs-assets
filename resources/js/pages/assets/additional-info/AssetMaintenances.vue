<template>
  <v-row>
    <div class="v-col-12 pt-0 pb-2">
      <v-btn @click="openNewMaintenance" class="mb-3" size="small">
        New Maintenance <v-icon class="ml-2" :icon="mdiPlus"></v-icon>
      </v-btn>
      <v-card>
        <v-table density="compact">
          <thead>
            <tr>
              <th class="text-left text-primary">INC ID</th>
              <th class="text-left text-primary">Location</th>
              <th class="text-left text-primary">Handled by</th>
              <th class="text-left text-primary">Service Type</th>
              <th class="text-left text-primary">Date Start</th>
              <th class="text-left text-primary">Date End</th>
              <th class="text-left text-primary">Cost</th>
              <th class="text-left text-primary">Status</th>
              <th class="text-left text-primary">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in props.asset.maintenance" :key="item.id">
              <td>{{ item.id }}</td>
              <td>{{ item.location }}</td>
              <td>{{ item.handle_by }}</td>
              <td>{{ item.service_type }}</td>
              <td>{{ item.date_start }}</td>
              <td>{{ item.date_closed }}</td>
              <td>{{ item.cost }}</td>
              <td>{{ item.status }}</td>
              <td>{{ item.remarks }}</td>
            </tr>
          </tbody>
        </v-table>
        <v-sheet
          v-if="props.asset.maintenance && props.asset.maintenance.length == 0"
          class="pa-3 text-center"
          >No record at the moment</v-sheet
        >
      </v-card>
    </div>
  </v-row>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { mdiPlus, mdiClose } from "@mdi/js";
import { useRouter } from "vue-router";
const router = useRouter();
const props = defineProps({
  asset: {
    type: Object,
    default: null,
  },
});

// maintenance
const openNewMaintenance = () => {
  //   router.push({
  //     name: "NewMaintenance",
  //     query: {
  //       type: "details",
  //       asset_code: "1231231",
  //       asset_id: 1,
  //       comp: 1,
  //       loc: 1,
  //       title: "test",
  //     },
  //   });
};
</script>
