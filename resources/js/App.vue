<template>
    <div v-if="isLoaded">

        <div v-if="authStore.authIsLoggedIn == true && route.name !== 'PublicApproval'" >
            <LoggedInLayout>
                <router-view></router-view>
            </LoggedInLayout>
        </div>
        <div v-else>
            <router-view></router-view>
        </div>
    </div>
    <div v-else>
        <v-dialog v-model="dialogLoader" fullscreen>
            <v-card class="rounded-0">
                <v-card-text
                    class="h-100 w-100 d-flex flex-column align-center justify-center"
                >
                    <v-progress-circular
                        indeterminate
                        color="primary"
                        class="mb-3"
                    ></v-progress-circular>
                    <div>Please wait....</div>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>
<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import LoggedInLayout from "@/layouts/LoggedInLayout.vue";
import { useTheme } from "vuetify";
import { useRoute } from "vue-router";
const route = useRoute();
const theme = useTheme();
const authStore = useAuthStore();
let isLoaded = ref(false);
let dialogLoader = ref(true); 
 
// check user
const checkUser = async () => {
    setTimeout(() => {
        
        isLoaded.value = true;
        dialogLoader.value = false;
    }, 300);
};

 
checkUser();

// set dark mode on load
const gagDarkTheme = localStorage.getItem("gag_dark_theme");
if (gagDarkTheme) {
    theme.global.name.value = gagDarkTheme;
} else {
    theme.global.name.value = "light";
    localStorage.setItem("gag_dark_theme", theme.global.name.value);
}
</script>