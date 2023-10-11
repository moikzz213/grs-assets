<template>
    <v-container>
        <AppPageHeader title="Global Approval Setup" />
        <v-row class="mb-3">
            <v-col class="v-col-12 mt-1 col-sm-12 py-0">
                <div class="mb-3">
                    <v-btn
                        :class="`${
                            isActive == 'request-asset' ? 'tab-active' : ''
                        } ml-7`"
                        @click="changeType('request-asset')"
                        >Request Asset Approvals</v-btn
                    >
                    <v-btn
                        :class="`${
                            isActive == 'transfer-asset' ? 'tab-active' : ''
                        } ml-3`"
                        @click="changeType('transfer-asset')"
                        >Transfer Asset Approvals</v-btn
                    >
                </div>
                <v-card>
                    <Form
                        as="v-form"
                        :validation-schema="validation"
                        v-slot="{ meta }"
                    >
                        <v-card-title class="my-3 ml-3 text-uppercase">
                            {{ props.headertitle }}</v-card-title
                        >
                        <v-card-text>
                            <v-row>
                                <div class="v-col-12 d-flex my-auto">
                                    <Field
                                        name="Title"
                                        v-slot="{ field, errors }"
                                        v-model="dataObj.title"
                                    >
                                        <v-text-field
                                            v-model="dataObj.title"
                                            v-bind="field"
                                            :error-messages="errors"
                                            label="Title here*"
                                            variant="outlined"
                                            hide-details="auto"
                                            density="compact"
                                            class="v-col-10 pt-0"
                                        ></v-text-field>
                                    </Field>
                                    <v-btn
                                        size="small"
                                        class="mt-1"
                                        color="primary"
                                        :disabled="!meta.valid"
                                        >Save</v-btn
                                    >
                                </div>
                            </v-row>
                            <v-row>
                                <div class="v-col-12 px-6 pt-0">
                                    <v-btn
                                        @click="addStages"
                                        color="primary"
                                        size="x-small"
                                        :icon="mdiPlus"
                                    ></v-btn>
                                </div>
                                <div class="v-col-12 px-6 pt-0">
                                    <v-row>
                                        <div class="v-col-2 font-weight-bold">
                                            TYPES
                                        </div>
                                        <div class="v-col-5 font-weight-bold">
                                            SIGNATORIES
                                        </div>
                                    </v-row>
                                    <v-row
                                        class="mt-0"
                                        v-for="(
                                            item, index
                                        ) in signatoriesObject"
                                        :key="index"
                                    >
                                        <div class="v-col-12 v-col-md-3">
                                            <Field
                                                name="Types"
                                                v-slot="{ field, errors }"
                                                v-model="item.types"
                                            >
                                                <v-autocomplete
                                                    :disabled="index === 0"
                                                    label="Select Type"
                                                    v-model="item.types"
                                                    hide-details="auto"
                                                    variant="outlined"
                                                    v-bind="field"
                                                    :error-messages="errors"
                                                    density="compact"
                                                    :items="typeItems"
                                                ></v-autocomplete>
                                            </Field>
                                        </div>
                                        <div class="v-col-12 v-col-md-5">
                                            <v-autocomplete
                                                :disabled="index === 0"
                                                label="Select Signatory"
                                                v-model="item.signatories"
                                                hide-details="auto"
                                                variant="outlined"
                                                density="compact"
                                                multiple
                                                :items="signatoryItems"
                                            ></v-autocomplete>
                                        </div>
                                    </v-row>
                                </div>
                            </v-row>
                        </v-card-text>
                    </Form>
                </v-card>
            </v-col>
        </v-row>
        <AppSnackBar :options="sbOptions" />
    </v-container>
</template>

<script setup>
import { onMounted, ref } from "vue";
import AppPageHeader from "@/components/ApppageHeader.vue";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter, useRoute } from "vue-router";
import { mdiTrashCan, mdiPlus } from "@mdi/js";
import { Form, Field, useFieldArray  } from "vee-validate";
import * as yup from "yup";
const authStore = useAuthStore();
const props = defineProps({
    objectdata: {
        type: Object,
        default: {},
    },
    headertitle: {
        type: String,
        default: null,
    },
});

const dataObj = ref({});

const typeItems = ref(["approve", "receiver", "review", "verify"]);
const signatoryItems = ref([{}]);

const router = useRouter();
const route = useRoute();
const isActive = ref(route.params.type);
let validation = yup.object({
    Title: yup.string().required(),
    Types: yup.string().required(),
});

const changeType = (type) => {
    isActive.value = type;
    router.push({ path: "/approval-setup/" + type });
};

const { remove, push, fields } = useFieldArray('types');

const signatoriesObject = ref([{   types: "Requestor", signatories: [] }]);
const addStages = () => { 
    signatoriesObject.value.push({ types: null, signatories: [] });
};
</script>

<style lang="scss" scoped>
.tab-active {
    background-color: #c6a92d;
    color: #ffffff;
}
</style>
