<template>
  <v-container>
    <AppPageHeader title="Account" />
    <v-row>
      <div class="v-col-12">
        <v-btn color="primary" class="mr-4" :loading="loadingAsset" @click="cancelFn"  >Back</v-btn>
      </div>
      <div class="v-col-12 v-col-md-8">

        <div class="d-flex flex-wrap">
          <v-btn
            :color="`${currentForm == 'profile' ? 'primary' : 'white'} `"
            size="large"
            class="mr-3 mb-1"
            :loading="user.loading"
            @click="() => openForm('profile')"
            >profile</v-btn
          >
          <v-btn
            :color="`${currentForm == 'account' ? 'primary' : 'white'} `"
            size="large"
            class="mr-3 mb-1"
            :loading="user.loading"
            @click="() => openForm('account')"
            >Account</v-btn
          >
          <v-btn
            :color="`${currentForm == 'change_password' ? 'primary' : 'white'} `"
            size="large"
            class="mr-3 mb-1"
            :loading="user.loading"
            @click="() => openForm('change_password')"
            >Change Password</v-btn
          >
        </div>
      </div>
      <div class="v-col-12 v-col-md-8">
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
        <ChangePassword  @saved="savedResponse" v-show="currentForm == 'change_password'" :user="authStore.user?.profile" />
      </div>
    </v-row>
    <AppSnackBar :options="sbOptions" />
  </v-container>
</template>

<script setup>
import { ref } from "vue";
import AppPageHeader from "@/components/AppPageHeader.vue";
import AccountForm from "./AccountForm.vue";
import ProfileForm from "./ProfileForm.vue";
import ChangePassword from "./ChangePassword.vue";
import { useAuthStore } from "@/stores/auth";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { useRouter } from "vue-router";
const router = useRouter();

const sbOptions = ref({
  status: false,
  type: "primary",
  text: null,
});
console.log("router",router);

// user
const authStore = useAuthStore();
const user = ref({
  loading: false,
  data: Object.assign({}, authStore.user),
});

// tabs
const currentForm = ref("profile");
const openForm = async (comp) => {
  currentForm.value = comp;
};
const cancelFn = () =>{
  router.back();
}
// form response
const savedResponse = (res) => {
  sbOptions.value = {
    status: true,
    type: "success",
    text: res,
  };
};
</script>
