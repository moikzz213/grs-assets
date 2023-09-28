<template>
    <v-dialog width="500" persistent v-model="dialogCapability">
        <template v-slot:activator="{ props }">
            <v-btn
                v-bind="props"
                text="Capability"
                size="small"
                color="primary"
            >
            </v-btn>
        </template>

        <template v-slot:default="{ isActive }">
            <v-card title="Capabilities">
                <v-card-text>
                    <v-autocomplete
                        variant="outlined"
                        density="compact"
                        hide-details
                        multiple
                        item-title="name"
                        item-value="name"
                        label="- Select -"
                        v-model="capabilities"
                        :items="capabilityItems"
                    ></v-autocomplete>
                </v-card-text>
                <v-card-actions>
                  
                    <v-spacer></v-spacer>
                    <v-btn size="small" text="cancel" @click="cancel"></v-btn>
                    <v-btn size="small" color="secondary" @click="saveData"
                        >Confirm</v-btn
                    >
                </v-card-actions>
            </v-card>
        </template>
    </v-dialog>
</template>
<script setup>
import { ref } from "vue";
const props = defineProps(["item"]);
const emit = defineEmits(["saved"]);

const dialogCapability = ref(false);
const capabilities = ref(props.item.capabilities);
const originalData = ref(props.item.capabilities);
const capabilityItems = ref(["add", "edit", "delete"]);

const saveData = async () => {
    dialogCapability.value = false;
    emit("saved", {id: props.item.id, capabilities: capabilities.value});
};

const cancel = () => {
    dialogCapability.value = false;
    capabilities.value = originalData.value;
}
</script>
