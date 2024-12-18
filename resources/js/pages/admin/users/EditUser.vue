<template>
    <v-container>
        <AppPageHeader title="Edit User" />
        <v-row class="mb-3" :disabled="loadingPage">
            <div class="v-col-12">
                <v-btn color="primary" class="mr-4" :loading="loadingAsset" @click="cancelFn"  >Back</v-btn>
            </div>
            <div class="v-col-12">
                <div class="d-flex flex-wrap">
                    <v-btn
                        :color="`${
                            currentForm == 'profile' ? 'primary' : 'white'
                        } `"
                        size="large"
                        class="mr-3 my-1"
                        :loading="user.loading"
                        @click="() => openForm('profile')"
                        >profile</v-btn
                    >
                    <v-btn
                        :color="`${
                            currentForm == 'account' ? 'primary' : 'white'
                        } `"
                        size="large"
                        class="mr-3 my-1"
                        :loading="user.loading"
                        @click="() => openForm('account')"
                        >Account</v-btn
                    >
                    <v-btn
                        :color="`${
                            currentForm == 'change_password'
                                ? 'primary'
                                : 'white'
                        } `"
                        size="large"
                        class="mr-3 my-1"
                        :loading="user.loading"
                        @click="() => openForm('change_password')"
                        >Change Password</v-btn
                    >
                    <v-btn
                        :color="`${
                            currentForm == 'approval_transfer'
                                ? 'primary'
                                : 'white'
                        } `"
                        size="large"
                        class="mr-3 my-1"
                        :loading="user.loading"
                        @click="() => openForm('approval_transfer')"
                        >APPROVAL TRANSFER</v-btn
                    >
                </div>
            </div>
            <div class="v-col-12 v-col-md-5">
                <v-row>
                    <div class="v-col-12">
                        <AccountForm
                            v-show="currentForm == 'account'"
                            :user="user.data"
                            @saved="savedResponse"
                        />
                        <ProfileForm
                            v-show="currentForm == 'profile'"
                            :user="user.data"
                            @saved="savedResponse"
                        />
                        <ChangePassword
                            v-show="currentForm == 'change_password'"
                            :user="user.data"
                        />
                        <ApprovalTransfer
                            v-show="currentForm == 'approval_transfer'"
                            :user="user.data"
                        />
                    </div>
                </v-row>
            </div>
            <div class="v-col-12 v-col-md-4">
                <v-card>
                    <v-card-text>
                        <v-row>
                            <div class="v-col-12 v-col-md-12 d-flex">
                                <v-checkbox
                                    v-model="selectAll"
                                    @update:modelValue="selectAllFn"
                                    hide-details
                                    variant="outlined"
                                    density="compact"
                                    label=" Access / View Pages"
                                ></v-checkbox>
                                <v-btn
                                    class="ml-5"
                                    size="small"
                                    :loading="loadingBtn"
                                    color="secondary"
                                    v-if="pageSelected"
                                    @click="SaveAccessCapabilities"
                                    >Save</v-btn
                                >
                            </div>
                        </v-row>
                        <v-divider class="mb-3"></v-divider>
                        <v-row v-for="(item, index) in pageSlug" :key="item.id">
                            <div class="v-col-12 v-col-md-12 py-0 d-flex">
                                <v-checkbox
                                    :model-value="selected.includes(item.slug)"
                                    hide-details
                                    variant="outlined"
                                    @update:modelValue="
                                        singleSelectFn(item, index)
                                    "
                                    :label="item.title"
                                    density="compact"
                                ></v-checkbox>

                                <div class="my-auto">
                                    <Capability
                                        v-if="item.isSelected"
                                        :item="item"
                                        @saved="capabilitySave"
                                    />
                                </div>
                            </div>
                            <v-divider></v-divider>
                        </v-row>
                    </v-card-text>
                </v-card>
            </div>

            <div class="v-col-12 v-col-md-3" v-if="pageSelected">
                <v-card>
                    <v-card-text>
                        <v-row>
                            <div class="v-col-12 v-col-md-12">
                                Capabilities <br />
                                <small class="text-error"
                                    >Warning: it will override the capabilities
                                    of all page.</small
                                >
                                <br />
                                <small class="text-error"
                                    >Select only here if all pages have same
                                    capabilities</small
                                >
                            </div>
                            <v-divider></v-divider>
                            <div class="v-col-12 v-col-md-12 my-auto">
                                <v-autocomplete
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    multiple
                                    label="- Select -"
                                    v-model="capabilities"
                                    :items="capabilityItems"
                                ></v-autocomplete>
                            </div>
                        </v-row>
                    </v-card-text>
                </v-card>
            </div>
        </v-row>
        <AppSnackbar :options="sbOptions" />
    </v-container>
</template>

<script setup>
import { onMounted, ref } from "vue";
import AppPageHeader from "@/components/ApppageHeader.vue";
import AccountForm from "@/pages/account/AccountForm.vue";
import ProfileForm from "@/pages/account/ProfileForm.vue";
import AppSnackbar from "@/components/AppSnackBar.vue";
import Capability from "./Capability.vue";
import ChangePassword from "@/pages/account/ChangePassword.vue";
import ApprovalTransfer from "@/pages/account/ApprovalTransfer.vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { clientKey } from "@/services/axiosToken";
const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const loadingPage = ref(true);
const user = ref({
    loading: false,
    data: {},
});

const cancelFn = () =>{
  router.back();
}

const getSingleUser = async () => {
    loadingPage.value = true;
    await clientKey(authStore.token)
        .get("/api/admin/user/single/" + route.params.id)
        .then((response) => {
            user.value.data = response.data; 
            loadingPage.value = false;
        })
        .catch((err) => {
            user.value.loading = false;
            loadingPage.value = false;
        });
};

const savedResponse = (resMsg) => {
    getSingleUser().then(() => {
        sbOptions.value = {
            status: true,
            type: "success",
            text: resMsg,
        };
    });
};
onMounted(() => {
    getSingleUser().then(() => {
        fetchPage();
    });
});

const currentForm = ref("profile");
const openForm =  async (comp) => {
    currentForm.value = comp;
};

const pageSlug = ref([]);
const selectAll = ref(false);

const fetchPage =    () => {
        clientKey(authStore.token)
        .get("/api/fetch/pages-slug")
        .then((res) => {
            pageSlug.value = res.data;
            if (user.value.data?.access.length > 0) {
                user.value.data.access.map((o, i) => { 
                    pageSlug.value.map((oo, ii) => {
                        if (o.slug == oo.slug) {
                            oo.isSelected = o.slug;
                            oo.capabilities = o.capabilities ? JSON.parse(o.capabilities) : [];
                            selected.value.push(o.slug);
                            pageSelected.value = true; 
                        }
                      
                    });
                });
            }
        })
        .catch((err) => {});
};

const pageSelected = ref(false);
const selectAllFn = () => {
    pageSlug.value.map((o, i) => {
        if(!o.capabilities){
            o.capabilities = [];
        }
        if (selectAll.value) {
            o.isSelected = o.slug;
            selected.value.push(o.slug);
            pageSelected.value = true;
        } else {
            o.isSelected = "";
            selected.value = [];
            pageSelected.value = false;
        }
        return o;
    });
};
const loadingBtn = ref(false);
const selected = ref([]);
const singleSelectFn = (vv) => {
    if (vv.isSelected) {
        selected.value.splice(
            selected.value.findIndex((v) => v === vv.isSelected),
            1
        );
        vv.isSelected = "";
        vv.capabilities = "";
    } else if (!vv.isSelected || vv.isSelected == null || vv.isSelected == "") {
        selected.value.push(vv.slug);

        vv.isSelected = vv.slug;
    }
    pageSelected.value = true;
    let dataSelected = pageSlug.value.filter((o, i) => !o.isSelected);
    if (dataSelected.length > 0) {
        selectAll.value = false;
    } else {
        selectAll.value = true;
    }
 
};

const capabilities = ref([]);
const capabilityItems = ref(["add", "edit", "delete"]);
const sbOptions = ref({
    status: true,
    type: "info",
    text: null,
});

const capabilitySave = (data) => {
    pageSlug.value.map((o, i) => {
        if (o.id == data.id) {
            o.capabilities = data.capabilities;
        }
    });
};
const profile_id = ref(authStore?.user?.profile?.id);
const SaveAccessCapabilities = () => {
    loadingBtn.value = true;
    if (capabilities.value.length > 0) {
        pageSlug.value.map((o, i) => {
            if (o.isSelected) {
                o.capabilities = capabilities.value;
            }else{
                o.capabilities = [];
            }
        });
    }

    let dataFiltered = pageSlug.value.filter((o) => o.isSelected);

    let formData = { profileID: route.params.id, data: dataFiltered, profile_id: authStore.user.profile.id };
    clientKey(authStore.token)
        .post("/api/store-page/settings-capabilities/profile", formData)
        .then((res) => {
            selected.value = [];
            capabilities.value = [];
            sbOptions.value = {
                status: true,
                type: "success",
                text: res.data.message,
            };
           
            loadingBtn.value = false;
            getSingleUser().then(() => {
                fetchPage();
            });
        });
};
</script>
