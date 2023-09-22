<template>
    <div>
        <v-list-group v-if="item.subs" :value="item.title">
            <template v-slot:activator="{ props }">
                <v-list-item
                    nav
                    v-bind="props"
                    :prepend-icon="item.icon"
                    :title="item.title"
                ></v-list-item>
            </template>
            <div class="bg-grey-darken-4" style="border-radius: 4px">
                <v-list-item
                    v-for="(sub, i) in item.subs"
                    :key="i"
                    :title="sub.title"
                    :value="sub.title"
                    density="compact"
                    style="padding-left: 12px !important"
                    link
                    :to="sub.path"
                >
                    <template v-slot:title>
                        <div style="font-size: 12px">
                            {{ sub.title }}
                        </div>
                    </template>
                    <template v-slot:prepend>
                        <v-icon size="16" :icon="sub.icon"></v-icon>
                    </template>
                </v-list-item>
            </div>
        </v-list-group>
        <div v-else>
            <v-list-item
                v-if="returnAccess(item.slug)"
                link
                :to="item.path"
                :prepend-icon="item.icon"
                :title="item.title"
            ></v-list-item>
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();

const props = defineProps({
    nav: {
        type: Object,
        default: {},
    },
});

const item = props.nav;

const returnAccess = (slug) => {
    
    let hasAccess = false;
    if (authStore.authRole == "superadmin") {
        hasAccess = true;
    } else if (slug == "dashboard") {
        hasAccess = true;
    }

    authStore.access.map((o, i) => {
        if (slug == o.slug) {
            hasAccess = true;
        }
    });
    return hasAccess;
};
</script>
