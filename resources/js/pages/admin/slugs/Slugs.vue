<template>
    <v-container>
        <AppPageHeader title="Page List" />

        <v-row class="mb-3" >
            <v-col class="v-col-12 mt-1 col-sm-12" >
                <v-card class="px-5" :loading="page.loading">
                    <v-card-text> 
                        <v-btn @click="AddNew()" size="small" color="primary" v-if="authStore.user.role == 'superadmin' || authStore.capabilities?.includes('add')">Add New</v-btn> 
                        <v-btn @click=savePage() :loading="btnLoading" size="small" color="secondary" class="mx-5" v-if="authStore.user.role == 'superadmin' || authStore.capabilities?.includes('edit')"
                                    >Save</v-btn
                                >
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col class="v-col-12 mt-1 col-sm-12">
                <v-card class="px-5" :loading="page.loading">
                    <v-card-text>
                        <v-row>
                            <div class="v-col-1">#</div>
                            <div class="v-col-4 text-uppercase">Page Title</div>
                            <div class="v-col-7 text-uppercase">Page Slug</div>
                            <div class="v-col-9 my-0 py-0"></div>
                            <v-divider></v-divider>
                        </v-row>

                        <v-row v-for="(item, index) in pageSlug" :key="index" class="parent-row-show">
                         
                            <div class="v-col-1">{{ index + 1 }}</div>
                            <div class="v-col-4">
                                <v-text-field
                                    hide-details
                                    label="Page Title here"
                                    variant="outlined"
                                    v-model="item.title"
                                    density="compact"
                                ></v-text-field>
                            </div>
                            <div class="v-col-4">
                                <v-text-field
                                    hide-details
                                    v-model="item.slug"
                                    label="Slug here"
                                    variant="outlined"
                                    density="compact"
                                ></v-text-field>
                            </div>
                            <div class="v-col-3 my-auto">
                                <v-btn @click="deleteFn(index)" size="small" color="error" class="mx-1 show-hover" v-if="authStore.user.role == 'superadmin' || authStore.capabilities?.includes('delete')"
                                    >Delete</v-btn
                                >
                            </div>
                            <v-divider></v-divider>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col class="v-col-12 mt-1 col-sm-12" v-if="pageSlug.length > 10">
                <v-card class="px-5" :loading="page.loading">
                    <v-card-text> <v-btn @click="AddNew()" size="small" color="primary" v-if="authStore.capabilities?.includes('add')">Add New</v-btn> 
                        <v-btn @click=savePage() :loading="btnLoading" size="small" color="secondary" class="mx-5" v-if="authStore.capabilities?.includes('edit')"
                                    >Save</v-btn>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <AppSnackbar :options="sbOptions" />
    </v-container>
</template>
<script setup>
import { ref } from "vue"; 
 
import { useAuthStore } from "@/stores/auth";
import { clientKey } from "@/services/axiosToken";
import AppPageHeader from "@/components/ApppageHeader.vue";
import AppSnackbar from "@/components/AppSnackBar.vue";

const pageSlug = ref([{title: "", slug: ""}]);
const authStore = useAuthStore();
 const btnLoading = ref(false);
const AddNew = () => {
    pageSlug.value.push({title: "", slug: ""}); 
}
const page = ref({loading:true});
const  sbOptions = ref({
      status: false,
      type: "info",
      text: null,
});
const fetchPage = async () => {
     
    await clientKey(authStore.token)
        .get("/api/fetch/pages-slug")
        .then((res) => { 
            pageSlug.value = res.data;
            page.value.loading = false;
        })
        .catch((err) => {
            page.value.loading = false;
            console.log(err);
        });
};
fetchPage();

const deleteFn = (index) => {
    let rows = pageSlug.value;
        rows.splice(index, 1)
        pageSlug.value = [...rows];
}
 

const savePage = async function(){
    btnLoading.value = true;
    let dataForm = pageSlug.value.map((o,i) =>{
        delete o["id"];
        delete o["created_at"];
        delete o["updated_at"];
        delete o["link"];
        return o;
    });
    let formData = {data: dataForm, author: authStore.user.profile.id}
 
    await clientKey(authStore.token)
        .post("/api/store-page/settings",formData)
        .then((res) => { 
           
            sbOptions.value = {
                status: true,
                type: "success",
                text: res.data.message,
            };
            
            btnLoading.value = false;
        })
        .catch((err) => {
            btnLoading.value = false;
            console.log(err);
        });
}
</script>
<style>
.parent-row-show:hover .show-hover{ display:block; }
.show-hover { display:none;}
</style>