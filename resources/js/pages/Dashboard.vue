<template>
    <v-container>
        <v-row class="mb-3">
            <div class="v-col-12 v-col-md-3 pb-0 scan-barcode">
                <v-card 
                    width="100%" 
                    class="rounded-lg text-center my-auto py-4 "
                    color="primary"
                    @click="goTo('Scan')"
                > 
                <v-card-text>
                     SCAN QRCODE
                    </v-card-text>
                </v-card>
            </div>
            <div
                class="v-col-12 v-col-md-3 pb-0"
                v-for="item in cards"
                :key="item.id"
            >
                <v-card
                    height="80"
                    width="100%" 
                    class="rounded-lg text-center pt-3 cursor-pointer"
                    @click="goTo(item.to)"
                > 
                    <div class="text-h6 text-capitalize" >{{ item.value }}</div>
                    <div class="text-capitalize">{{ item.title }}</div>
                  
                </v-card>
            </div>
        </v-row>
        <v-row>
            <div class="v-col-12">
               <v-card> 
                <v-card-text>
                  <v-row>
                    <v-col col="12" class="text-body-1">RECENT ACTIVITIES</v-col>
                  </v-row>
                  <v-row>
                    <v-col col="12">
                      <v-table
                            fixed-header
                            height="590px"
                          >
                            <thead>
                              <tr>
                                <th class="text-left"> Company </th>
                                <th class="text-left"> Location </th>
                                <th class="text-left"> Category </th>
                                <th class="text-left"> AssetName / Subject</th>
                                <th class="text-left"> AssetCode</th>
                                <th class="text-left"> Type</th>
                                <th class="text-left"> Author</th>
                                <th class="text-left">Update<br/>
                                    <small>DD/MM/YY</small></th> 
                              </tr>
                            </thead>
                            <tbody v-if="recentActivities.length > 0">
                               <tr v-for="item in recentActivities" :key="item.id">
                                <td>{{ item.company }}</td>
                                <td>{{ item.location }}</td>
                                <td>{{ item.category }}</td>
                                <td>{{ item.asset_name }}</td>
                                <td>{{ item.asset_code }}</td>
                                <td class="text-capitalize">{{ item.type }}</td>
                                <td>{{ item.author }}</td>
                                <td>{{ useFormatDate(item.date) }} </td> 
                               </tr>
                            </tbody>
                          </v-table>
                    </v-col>
                  </v-row>
                </v-card-text>
               </v-card>
            </div>
        </v-row>
    </v-container>
</template>

<script setup>
import { ref } from "vue";
import { clientKey } from "@/services/axiosToken";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import { useFormatDate } from "@/composables/formatDate"
const authStore = useAuthStore();
const router = useRouter();
const cards = ref([
    {
        id: 1,
        title: "PENDING MAINTENANCE",
    },
    {
        id: 2,
        title: "PENDING TICKETS",
    },
    {
        id: 3,
        title: "TRANSFER REQUEST",
    },
    {
        id: 4,
        title: "REQUEST ASSET",
    },
]);

const recentActivities = ref([]);
const fetchQuery = async () => { 
    await clientKey(authStore.token)
        .get("/api/dashboard/fetch-query/data?role="+authStore.user.profile.role+"&id="+authStore.user.profile.id)
        .then((res) => {
          recentActivities.value = res.data.table;
            cards.value = [
                {
                    id: 1,
                    title: "PENDING MAINTENANCE",
                    value: res.data.count.maintenance,
                    to: 'Maintenance'
                },
                {
                    id: 2,
                    title: "PENDING TICKETS",
                    value: res.data.count.incident,
                    to: 'Incident'
                },
                {
                    id: 3,
                    title: "TRANSFER REQUEST",
                    value: res.data.count.transfer,
                    to: 'Transferasset'
                },
                {
                    id: 4,
                    title: "REQUEST ASSET",
                    value: res.data.count.request,
                    to: 'RequestAsset'
                },
            ];
        })
        .catch((err) => {
            console.log(err);
        });
};
fetchQuery();

const goTo = (v) => {
    router
        .push({  name: v,  })
        .catch((err) => {
            console.log(err);
        });
}
</script>
<style scoped>
.scan-barcode{ display: none;}
@media only screen and (max-width: 600px) {
    .scan-barcode {
    display: block;
  }
}
</style>