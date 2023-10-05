<template>
    <div>
        <v-dialog
            width="600"
            persistent
            v-model="popDialog"
            @keydown.esc="cancelAddNew"
        >
            <v-card class="pt-3">
                <Form
                    as="v-form"
                    :validation-schema="validation"
                    v-slot="{ meta }"
                >
                    <v-card-title class="text-h6 d-flex"
                        >{{ title }}<v-spacer></v-spacer
                        ><small class="text-error">{{ errorMessage }}</small>
                        <v-spacer></v-spacer
                    ></v-card-title>
                    <v-card-text>
                        <v-row>
                            <div
                                class="v-col-12"
                                v-for="(item, index) in freeForm"
                                :key="index"
                            >
                                <Field
                                    :name="item.name"
                                    v-slot="{ field, errors }"
                                    v-model="dataObj[item.name]"
                                >
                                    <v-text-field
                                        v-if="
                                            item.type == 'text' ||
                                            item.type == 'number' ||
                                            item.type == 'email'
                                        "
                                        :type="item.type"
                                        :autofocus="index === 0 ? true : false"
                                        v-model="dataObj[item.name]"
                                        density="compact"
                                        variant="outlined"
                                        :label="`${item.label}${
                                            item.required ? '*' : ' (Optional)'
                                        }`"
                                        hide-details
                                        v-bind="field"
                                        @keydown.enter="saveData"
                                        :error-messages="errors"
                                    ></v-text-field>
                                    <v-autocomplete
                                        v-if="item.type == 'select'"
                                        :autofocus="index === 0 ? true : false"
                                        v-model="dataObj[item.name]"
                                        density="compact"
                                        variant="outlined"
                                        :label="`${item.label}${
                                            item.required ? '*' : ''
                                        }`"
                                        :items="item.data"
                                        hide-details
                                        item-value="id"
                                        item-title="title"
                                        v-bind="field"
                                        :error-messages="errors"
                                    ></v-autocomplete>
                                </Field>
                            </div>
                        </v-row>
                    </v-card-text>
                    <v-divider class="mt-2"></v-divider>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            size="small"
                            text="cancel"
                            @click="cancelAddNew"
                        ></v-btn>

                        <v-btn
                            size="small"
                            color="secondary"
                            @click="saveData"
                            :disabled="!meta.valid"
                            >Confirm</v-btn
                        >
                    </v-card-actions>
                </Form>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { Form, Field } from "vee-validate";
import * as yup from "yup";
const title = ref("");

const props = defineProps({
    dataObject: {
        type: Object,
        default: {},
    },
    addNewDialog: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "",
    },
    freeForm: {
        type: Array,
        default: [],
    },
});

const popDialog = ref(false);
const dataObj = ref({});

const freeForm = ref(props.freeForm);
const kpiEmit = defineEmits(["savedResponse"]);

const cancelAddNew = () => {
    popDialog.value = false;
    dataObj.value = {};

    kpiEmit("cancelled", popDialog.value);
};

const saveData = () => {
    popDialog.value = false;

    kpiEmit("save", dataObj.value);
    dataObj.value = {};
};

const requiredFields = ref({});

freeForm.value.map((o, i) => {
    if (o.required) {
        requiredFields.value[o.name] = yup.string().required();
    }
});
let validation = yup.object(requiredFields.value);

watch(
    () => props.addNewDialog,
    (newVal) => {
        dataObj.value = {};
        popDialog.value = newVal;
        title.value = "New " + props.title;

        if (props.dataObject.type) {
            dataObj.value = props.dataObject;
            title.value = "Edit " + props.title;
        }
    }
);
</script>
