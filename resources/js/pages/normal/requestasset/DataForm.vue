<template>
    <v-container>
        <AppPageHeader :title="props.headertitle" class="no-print" />
        <v-row class="mb-3">
            <v-col class="v-col-12 mt-1 col-sm-12 py-0">
                <v-card>
                    <v-card-title
                        class="my-0 ml-3 text-uppercase text-h6 d-flex justify-space-between font-weight-bold"
                    >
                        <div>{{ props.headertitle }} {{ isDraft ?  "(Draft)" : "" }}</div>
                        <div v-if="!hasSignatories" class="text-error text-h6">
                            {{ errorMsg }}
                        </div>
                        <div
                            v-if="isEdit"
                            :class="
                                formObjData.status == 'cancelled'
                                    ? 'text-error'
                                    : formObjData.status == 'complete'
                                    ? 'text-success'
                                    : ''
                            "
                        >
                            STATUS ({{ formObjData.status }})
                        </div>

                        <div>DATE: {{ useFormatDate(currentDate) }}</div>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row class="no-print">
                            <div
                                :class="`${
                                    formObjData.status == 'complete'
                                        ? 'v-col-12'
                                        : 'v-col-md-12'
                                }`"
                                v-if="isEdit"
                            >
                                <v-row>
                                    <div class="col-12 v-col-md-4">
                                        REQUEST NO: SN-5{{
                                            pad(formObjData.id)
                                        }}
                                    </div>
                                    <!--  v-if="!is_receiving_releasing" -->
                                    <div 
                                        class="v-col-12 v-col-md-8"
                                    >
                                        <div
                                            class="text-right no-print"
                                            v-if="
                                                isEdit &&
                                                (formObjData.status ==
                                                    'pending' ||
                                                    formObjData.status ==
                                                        'cancelled')
                                            "
                                        >
                                            <v-btn
                                                v-if="
                                                objData.requestor_id == authStore.user.profile.id && 
                                                    formObjData.status ==
                                                    'pending'
                                                "
                                                @click="
                                                    changeRequestStatus(
                                                        'cancelled'
                                                    )
                                                "
                                                color="warning"
                                                >Cancel request</v-btn
                                            >
                                            <v-btn
                                                v-if="
                                                   objData.requestor_id == authStore.user.profile.id &&  formObjData.status ==
                                                    'cancelled'
                                                "
                                                @click="
                                                    changeRequestStatus(
                                                        'pending'
                                                    )
                                                "
                                                size="small"
                                                color="info"
                                                >Change status to Pending</v-btn
                                            >
                                        </div>
                                    </div>
                                </v-row>
                            </div>
                            <div class="v-col-12 v-col-md-6">
                                <v-autocomplete
                                    :items="requestTypeList"
                                    v-model="objData.request_type_id"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    item-value="id"
                                    :disabled="isEdit"
                                    item-title="title"
                                    label="TYPE OF REQUEST*"
                                    @update:modelValue="setupApprovals"
                                ></v-autocomplete>
                            </div>
                            <div class="v-col-12 v-col-md-6">
                                <v-autocomplete
                                    :items="companyList"
                                    v-model="objData.company_id"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    item-value="id"
                                    item-title="title"
                                    label="COMPANY*"
                                ></v-autocomplete>
                            </div>
                            <div class="v-col-12 v-col-md-6">
                                <v-autocomplete
                                    :items="locationList"
                                    v-model="objData.transferred_from"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    item-value="id"
                                    item-title="title"
                                    label="LOCATION FROM*"
                                ></v-autocomplete>
                            </div>
                            <div class="v-col-12 v-col-md-6 d-flex">
                                <v-autocomplete
                                    :items="locationList"
                                    v-model="objData.transferred_to"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    item-value="id"
                                    item-title="title"
                                    label="LOCATION TO*"
                                ></v-autocomplete> 
                                <v-btn size="small" :loading="isLoading" @click="updateLocation" color="success" class="mx-2 my-auto" v-if="isEdit && adminAuthority.includes(authStore.user.profile.role)">Save</v-btn>
                            </div>
                            <div class="v-col-12">
                                <v-text-field
                                    v-model="objData.subject"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    clearable
                                    label="SUBJECT*"
                                ></v-text-field>
                            </div>
                        </v-row>
                        <!-- For printing only -->
                        <v-row class="for-print-flex mx-2 mb-1">
                            <div class="wd-100">
                                REQUEST NO:  {{ formObjData.id ? "SN-5" + pad(formObjData.id) : '' }}
                            </div>
                            <div class="wd-60">
                                TYPE OF REQUEST: {{ requestTypeTitle }}
                            </div>
                            <div class="wd-40">
                                COMPANY: {{ formObjData?.company?.title }}
                            </div>
                            <div class="wd-60">
                                LOCATION FROM: {{ locationTitleFrom }}
                            </div>
                            <div class="wd-40">
                                LOCATION TO: {{ locationTitleTo }}
                            </div>
                            <div class="wd-100">
                                SUBJECT: {{ formObjData.subject }}
                            </div>
                        </v-row>
                        <!-- End row for printing -->
                    </v-card-text>
                </v-card>
                <v-card class="my-2">
                    <v-card-text>
                        <!-- <v-row v-if="!is_receiving_releasing"> -->
                        <v-row>
                            <div class="v-col-12"
                            v-if=" 
                                formObjData.status != 'complete' &&
                                (!route.params.id || ( objData.requestor_id == authStore.user.profile.id && 
                                    props.objectdata?.status ==
                                        'pending' ||
                                    authStore.user.profile.role ==
                                        'superadmin' ||
                                    authStore.user.profile.role ==
                                        'administrator'))
                                    ">
                                <v-btn
                                size="small"
                                    color="primary"
                                    class="no-print"
                                    @click="AddAsset"
                                    
                                    >Add</v-btn
                                > 
                            </div>
                        </v-row>
                        <!-- This row is for print -->
                        <v-row class="for-print-flex mx-2 header mb-1 mt-0">
                            <div class="wd-30">ITEM DESCRIPTION</div>
                            <div class="wd-15">ASSET CODE</div>
                            <div class="wd-5">QTY</div>
                            <div class="wd-10">UOM</div>
                            <div class="wd-10">WEIGHT</div>
                            <div class="wd-10">VALUE</div>
                            <div class="wd-15">REASON FOR REQUEST</div>
                        </v-row>
                        <v-row
                            v-for="(item, index) in assetDataObj"
                            :key="index"
                            class="for-print-flex mx-2 row-bordered mb-1"
                        >
                            <div class="wd-30">{{ item.item_description }}</div>
                            <div class="wd-15">{{ item.asset_code }}</div>
                            <div class="wd-5">{{ item.qty }}</div>
                            <div class="wd-10">{{ item.uom }}</div>
                            <div class="wd-10">{{ item.weight }}</div>
                            <div class="wd-10">{{ item.item_value }}</div>
                            <div class="wd-15">
                                {{ item.reason_for_request }}
                            </div>
                        </v-row>
                        <!-- End for print -->
                        <v-row
                            v-for="(item, index) in assetDataObj"
                            :key="index"
                            class="no-print"
                        >
                            <div class="v-col-12 v-col-md-1 px-1">
                                <v-row v-if="item.attachment?.id" class="px-1">
                                    <div
                                        class="v-col-12 v-col-md-12 pa-2"
                                        style="position: relative"
                                    >
                                        <v-text-field
                                            style="display: none"
                                            type="hidden"
                                            class="hidden"
                                            v-model="item.attachment.id"
                                        ></v-text-field>
                                        <v-btn
                                        v-if="!isEdit || (isEdit && objData.requestor_id == authStore.user.profile.id)"
                                            style="
                                                position: absolute;
                                                top: 0;
                                                right: 0;
                                                z-index: 1;
                                            "
                                            :icon="mdiClose"
                                            size="16px"
                                            color="error"
                                            @click="
                                                () =>
                                                    removeAttachment(
                                                        index,
                                                        item.attachment.id
                                                    )
                                            "
                                        >
                                        </v-btn>
                                        <v-card
                                            @click="() => openAttachment(item)"
                                            maxHeight="40"
                                            style="
                                                background-image: url('/assets/images/fav.png');
                                                background-size: cover;
                                                height: 100px;
                                                width: 100px;
                                            "
                                        >
                                        </v-card>
                                    </div>

                                    <v-dialog
                                        v-model="dialogAttachment"
                                        width="95%"
                                        max-width="900"
                                    >
                                        <v-card class="bg-black">
                                            <div
                                                style="
                                                    height: 680px;
                                                    width: 100%;
                                                "
                                                class="d-flex align-center justify-center"
                                            >
                                                <v-img
                                                    :src="
                                                        baseURL +
                                                        '/file/' +
                                                        itemPath.attachment.path
                                                    "
                                                ></v-img>
                                            </div>
                                        </v-card>
                                    </v-dialog>
                                </v-row>
                                <v-row v-else class="mt-0">
                                    <div class="v-col-12 pt-0 pb-0">
                                        <v-sheet
                                            color="grey-lighten-4"
                                            class="text-center"
                                        >
                                            <Studio
                                                :options="{
                                                    multiSelect: false,
                                                    type: 'request-asset',
                                                    userID: authStore.user.profile.id
                                                }"
                                                @select="
                                                    (e) =>
                                                        studioSelectResponse(
                                                            index,
                                                            e
                                                        )
                                                "
                                            />
                                        </v-sheet>
                                    </div>
                                </v-row>
                            </div>
                            <div class="v-col-12 v-col-md-1 custom-width-description">
                                <v-textarea
                                    v-model="item.item_description"
                                    variant="outlined"
                                    density="compact"
                                    hide-details 
                                    rows="2"
                                    label="ITEM DESC*"
                                    @update:modelValue="requiredData"
                                ></v-textarea>
                            </div>
                            <div class="v-col-12 v-col-md-2 d-flex"> 
                                <v-text-field
                                    v-model="item.asset_code"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    label="ASSET CODE"
                                    :append-inner-icon="mdiMagnify"
                                    @click:appendInner="onDecode(item.asset_code, index)"
                                ></v-text-field> 
                                <v-icon
                                v-if=" 
                                        ( !route.params.id || (
                                            objData.requestor_id == authStore.user.profile.id && 
                                            props.objectdata?.status ==
                                                'pending' || 
                                            authStore.user.profile.role ==
                                                'superadmin' ||
                                            authStore.user.profile.role ==
                                                'administrator') ) &&
                                        formObjData.status != 'complete'
                                    "
                                    @click="enableBarcodeFn(index)"
                                    class="ml-2 my-auto"
                                    size="x-small"
                                    :icon="mdiBarcodeScan"
                                ></v-icon>
                            </div>
                            <div class="v-col-12 v-col-md-1">
                                <v-text-field
                                    type="number"
                                    v-model="item.qty" 
                                    @update:modelValue="requiredData"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    label="QTY*" 
                                ></v-text-field>
                            </div>
                            <div class="v-col-12 v-col-md-1 custom-width-uom">
                                <v-autocomplete
                                        :items="listUom"
                                        v-model="item.uom"
                                        variant="outlined"
                                        density="compact"
                                        hide-details 
                                        label="UOM*"
                                        @update:modelValue="requiredData"
                                        :disabled="item.status == 'done'"
                                    >
                                    </v-autocomplete>
                            
                            </div>
                            <div class="v-col-12 v-col-md-1 mt-3">
                                 {{item.weight ? item.weight : 0}}<br/><small>(KG)</small>
                            </div>
                            <div class="v-col-12 v-col-md-1 mt-3">
                                {{item.item_value ? item.item_value : 0}}<br/><small>(AED)</small>
                            </div>
                            <div class="v-col-12 v-col-md-2 d-flex">
                                <v-textarea
                                    v-model="item.reason_for_request"
                                    variant="outlined"
                                    density="compact"
                                    hide-details
                                    rows="2"
                                    label="REASON*"
                                ></v-textarea>
                                <v-icon
                                    size="small"
                                    @click="() => deleteData(item.id, index)"
                                    :icon="mdiTrashCan"
                                    class="my-auto ml-2 no-print"
                                    v-if="
                                        !isEdit ||
                                        (isEdit &&
                                            authStore.user.profile.id ==
                                                props.objectdata.profile_id &&
                                            props.objectdata?.status ==
                                                'pending')
                                    "
                                    color="error"
                                />
                            </div>
                        </v-row>
                    </v-card-text>
                </v-card>
               
                <v-card v-if="objData.extra_attachment">
                    <v-card-text >
                        <div v-if="!isEdit || (isEdit && objData.requestor_id == authStore.user.profile.id)">
                        <div>Additional File can be upload here</div>
                            <Studio
                                :options="{
                                    multiSelect: true,
                                    type: 'request-asset',
                                }"
                                @select="studioSelectResponseMultiple"
                            />
                        </div>
                        <v-row v-if="selectedFiles?.length > 0" class="px-1 mt-4">
                            <div
                                v-for="(file, index) in selectedFiles"
                                :key="file.id"
                                class="v-col-12 v-col-md-2 pa-2"
                                style="position: relative"
                            >
                                <v-btn
                                    v-if="
                                        isEdit &&
                                        (formObjData.status == 'pending' ||
                                            formObjData.status == 'cancelled')
                                    "
                                    style="
                                        position: absolute;
                                        top: 0;
                                        right: 0;
                                        z-index: 1;
                                    "
                                    :icon="mdiClose"
                                    size="20px"
                                    color="error"
                                    @click="
                                        () => removeAttachmentMultiple(file.id)
                                    "
                                >
                                </v-btn>
                                
                                <v-card
                                    @click="() => openAttachmentMultiple(file,index)"
                                >
                                    <v-img
                                        v-if="
                                            file.mime == 'image/jpeg' ||
                                            file.mime == 'image/png'
                                        "
                                        :src="baseURL + '/file/' + file.path"
                                        cover
                                        aspect-ratio="1"
                                    >
                                    </v-img>
                                    <v-img
                                        v-else
                                        :src="
                                            baseURL +
                                            '/assets/images/pdf-image.png'
                                        "
                                        cover
                                        aspect-ratio="1"
                                    >
                                    </v-img>
                                </v-card>
                            </div>

                            <v-dialog
                                v-model="dialogAttachment"
                                width="95%"
                                max-width="900"
                            >
                                <v-card class="bg-black">
                                    <v-carousel
                                        hide-delimiter-background
                                        show-arrows="hover"
                                        height="680px"
                                        v-model="currentSlider"
                                    >
                                        <v-carousel-item
                                            v-for="(item, i) in selectedFiles"
                                            :key="i"
                                            reverse-transition="fade"
                                            transition="fade"
                                        >
                                            <div
                                                :style="`${
                                                    fileType == 'pdf'
                                                        ? 'height: 980px;'
                                                        : 'height: 680px;'
                                                } 
                                                        width: 100%;
                                                        `"
                                                class="d-flex align-center justify-center"
                                            >
                                                <v-img
                                                    v-if="
                                                        fileType == 'jpg' ||
                                                        fileType == 'jpeg' ||
                                                        fileType == 'png'
                                                    "
                                                    :src="
                                                        baseURL +
                                                        '/file/' +
                                                        item.path
                                                    "
                                                ></v-img>
                                                <object
                                                    v-if="fileType == 'pdf'"
                                                    :data="
                                                        baseURL +
                                                        '/file/' +
                                                        item.path
                                                    "
                                                    type="application/pdf"
                                                    width="100%"
                                                    height="800px"
                                                ></object>
                                            </div>
                                        </v-carousel-item>
                                    </v-carousel>
                                </v-card>
                            </v-dialog>
                        </v-row>
                    </v-card-text>
                </v-card>
                <v-card class="my-2">
                    <v-card-text>
                        <v-row>
                            <div
                                class="v-col-12 font-weight-bold text-uppercase d-flex justify-space-between"
                            >
                                <div>APPROVAL SETUP</div>
                                <div class="no-print" v-if="route.params.id">
                                    <v-btn
                                        color="info"
                                        size="small"
                                        target="_blank"
                                        :href="requestSignatureUrl"
                                        >View Approval Page</v-btn
                                    >
                                </div>
                            </div>
                            <v-divider></v-divider>
                        </v-row>
                        <v-row class="no-print">
                            <div class="v-col-12 v-col-md-2">Requestor</div>
                            <div class="v-col-12 v-col-md-6 d-flex">
                                {{ formObjData?.profile?.display_name }} 
                            </div> 
                            <div class="v-col-12 v-col-md-2">Date Approved</div>
                            <div class="v-col-12 v-col-md-2" v-if="isEdit && adminAuthority.includes(authStore.user.profile.role)">Change To</div>
                        </v-row>
                        <!-- for printing -->
                        <v-row class="for-print-flex mx-2 mt-5 mb-1">
                            <div class="wd-20">Requestor</div>
                            <div class="wd-50">
                                {{ formObjData?.profile?.display_name }}
                            </div>
                            <div class="wd-10">Status</div>
                            <div class="wd-20">Date Approved</div>
                        </v-row>
                        <v-row
                            class="for-print-flex mx-2 mb-2 mt-0"
                            v-for="item in onUpdateApproval"
                            :key="item.id"
                        >
                            <div class="wd-20">
                                {{ statusTitle(item.approval_type) }}
                            </div>
                            <div class="wd-50">
                                {{ item?.profile?.display_name }}
                            </div>
                            <div class="wd-10">
                                <v-icon
                                    v-if="item.status"
                                    class="mx-2 my-0"
                                    :icon="
                                        item.status == 'awaiting-approval'
                                            ? mdiAccountClockOutline
                                            : item.status == 'pending'
                                            ? mdiAccountDetails
                                            : item.status == 'reject'
                                            ? mdiAccountCancel
                                            : mdiAccountCheck
                                    "
                                    :color="
                                        item.status == 'awaiting-approval'
                                            ? 'info'
                                            : item.status == 'pending'
                                            ? ''
                                            : item.status == 'reject'
                                            ? 'error'
                                            : 'success'
                                    "
                                >
                                </v-icon>
                            </div>
                            <div class="wd-20">{{ item.date_approved }}</div>
                        </v-row>
                        <!-- end for printing -->
                        <template v-if="hasSignatories">
                            <v-row
                                class="no-print"
                                v-for="(item, index) in approvalSetupList"
                                :key="item?.id"
                            >
                         
                                <div v-if="item.id" class="v-col-12 v-col-md-2">
                                    {{
                                        item.types
                                            ? statusTitle(item.types)
                                            : statusTitle(item.approval_type)
                                    }}
                                </div>
                                <div v-if="item.id" class="v-col-12 v-col-md-6 d-flex">
                                    <v-autocomplete
                                        :items="item.signatures"
                                        v-model="item.profile_id"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                        item-value="id"
                                        item-title="display_name"
                                        label="SELECT SIGNATORY*"
                                        @update:modelValue="requiredData"
                                        :disabled="item.status == 'done'"
                                    >
                                    </v-autocomplete>
                                    <v-btn variant="text" size="x-small">
                                        <v-icon
                                            v-if="item.status"
                                            class="mx-2 my-3 no-print"
                                            :icon="
                                                item.status ==
                                                'awaiting-approval'
                                                    ? mdiAccountClockOutline
                                                    : item.status == 'pending'
                                                    ? mdiAccountDetails
                                                    : item.status == 'reject'
                                                    ? mdiAccountCancel
                                                    : mdiAccountCheck
                                            "
                                            :color="
                                                item.status ==
                                                'awaiting-approval'
                                                    ? 'info'
                                                    : item.status == 'pending'
                                                    ? ''
                                                    : item.status == 'reject'
                                                    ? 'error'
                                                    : 'success'
                                            "
                                        >
                                        </v-icon>

                                        <v-tooltip
                                            activator="parent"
                                            location="top"
                                            max-width="400"
                                            >{{
                                                item.status ==
                                                "awaiting-approval"
                                                    ? "Awaiting Approval"
                                                    : item.status == "pending"
                                                    ? "Pending"
                                                    : item.status == "reject"
                                                    ? item.reason_rejected
                                                    : "Already Signed"
                                            }}</v-tooltip
                                        >
                                    </v-btn>
                                </div>
                                <div v-if="item.id" class="my-auto v-col-12 v-col-md-2">
                                    {{
                                        item.date_approved
                                            ? useFormatDate(item.date_approved)
                                            : ""
                                    }}
                                </div>
                                <!-- Change to -->
                                <div class="v-col-12 v-col-md-2" v-if="item.id && isEdit && adminAuthority.includes(authStore.user.profile.role)">
                                    <v-autocomplete
                                        :items="item.signatures"
                                        v-model="item.profile_id"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                        item-value="id"
                                        item-title="display_name"
                                        label="SELECT SIGNATORY*"
                                        @update:modelValue="updateApprover(item.profile_id, index)"
                                        
                                    >
                                    </v-autocomplete>
                                </div>
                            </v-row>
                            <v-row>
                                <v-divider></v-divider>
                                <!-- v-if="!is_receiving_releasing" -->
                                <div
                                    class="v-col-12"
                                >
                                    <v-btn
                                   
                                        :disabled="!isValidate"
                                        @click="submitRequest"
                                        class="no-print"
                                        color="primary"
                                        v-if="
                                        
                                            ( !route.params.id || (
                                              objData.requestor_id == authStore.user.profile.id && 
                                                props.objectdata?.status ==
                                                    'pending' || 
                                                authStore.user.profile.role ==
                                                    'superadmin' ||
                                                authStore.user.profile.role ==
                                                    'administrator') ) &&
                                            formObjData.status != 'complete'
                                        "
                                        >{{
                                            route.params.id
                                                ? "Update Request"
                                                : "Submit For Approval"
                                        }}</v-btn
                                    >

                                    <v-btn
                                        size="small" 
                                        class="mx-3"
                                        @click="saveDraft"
                                        color="warning"
                                        v-if="!route.params.id"
                                        >Save as Draft</v-btn
                                    >
                                    <v-btn
                                        size="small" 
                                        class="mx-3"
                                        @click="deleteDraft"
                                        color="info"
                                        v-if="isDraft"
                                        >Delete Draft</v-btn
                                    >
                                </div>
                            </v-row>
                        </template>
                        <v-row v-else class="no-print">
                            <div class="v-col-12 text-error">
                                {{ errorMsg }}
                            </div>
                        </v-row>
                        <v-row>
                            <div
                                class="v-col-12 v-col-md-12 text-info no-print"
                            >
                                Note: Once submitted and approval is on-going,
                                you will no longer allowed to update the
                                request.
                            </div>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <AppSnackBar :options="sbOptions" />
        <v-dialog width="500" v-model="enableBarcode">
      <v-card>
        <v-card-text>
            <qrcode-stream @decode="onDecode"></qrcode-stream>
        </v-card-text>
        
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text="Close" @click="enableBarcode = false"></v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    </v-container>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";

import AppPageHeader from "@/components/ApppageHeader.vue";
import { useRoute, useRouter } from "vue-router";
import Studio from "@/studio/Studio.vue";
import { useAuthStore } from "@/stores/auth";
import AppSnackBar from "@/components/AppSnackBar.vue";
import { clientKey } from "@/services/axiosToken";
import { QrcodeStream } from 'qrcode-reader-vue3'
import { encryptData, decryptData } from "@/composables/encrypt";
import {
    mdiTrashCan,
    mdiAccountCheck,
    mdiAccountClockOutline,
    mdiAccountDetails,
    mdiClose,
    mdiBarcodeScan, mdiMagnify,
    mdiAccountCancel,
} from "@mdi/js";
import { useFormatDate } from "@/composables/formatDate.js";
import { randomAlphaString } from "@/composables/generateRandomString.js";
 
const emit = defineEmits(["deleted"]);
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

const isValidate = ref(false);
const route = useRoute();
const router = useRouter();
const objData = ref({});
const isEdit = ref(false);
const isLoading = ref(false);
const assetDataObj = ref([{ qty: 1, attachment: {} }]);
const currentDate = ref(new Date());
const adminAuthority = ref(['commercial-manager', 'superadmin']);
const onUpdateApproval = ref([]);
const formObjData = ref({});
const getCurrentApprover = ref({});

const baseURL = ref(window.location.origin);
const studioSelectResponse = (index, v) => {
    assetDataObj.value[index].attachment = v[0];
};

const selectedFiles = ref([]);
const studioSelectResponseMultiple = (v) => {
    let fileExist = null;
    v.map((sudioFile) => {
        fileExist = selectedFiles.value.find((f) => f.id == sudioFile.id);
        if (!fileExist) {
            selectedFiles.value.push(sudioFile);
        }
    });
};

const removeAttachmentMultiple = (theId) => {
    selectedFiles.value = selectedFiles.value.filter((f) => f.id !== theId);
};
const currentSlider = ref(1);
const fileType = ref("image");
const openAttachmentMultiple = (file,index) => {
    let mimeType = file.path.split(".");
    fileType.value = mimeType[mimeType.length - 1];

    currentSlider.value = index;
    dialogAttachment.value = true;
};

const dialogAttachment = ref(false);

const itemPath = ref(1);
const openAttachment = (index) => {
    itemPath.value = index;
    dialogAttachment.value = true;
};
const removeAttachment = (index) => {
    assetDataObj.value[index].attachment = "";
};

const statusTitle = (v) => {
    if (v == "approve") {
        return "Approved By";
    } else if (v == "reviewer") {
        return "Reviewed By";
    } else if (v == "receiver") {
        return "Received By";
    } else if (v == "transport") {
        return "Transport Arranged By";
    } else if (v == "releasing") {
        return "Released By";
    } else if (v == "verify") {
        return "Verified By";
    }
};
const requestTypeTitle = ref("");
const requestTypeList = ref([]);
const fetchSetupRequest = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch/approval-setups/non-paginated/data/request")
        .then((res) => {
            requestTypeList.value = res.data;
            requestTypeTitle.value = res.data.filter(
                (e) => e.id == formObjData.value.request_type_id
            )?.[0]?.title;
        })
        .catch((err) => {});
};

const sbOptions = ref({});
const companyList = ref([]);
const fetchCompanies = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch/companies")
        .then((res) => {
            companyList.value = res.data;
        })
        .catch((err) => {});
};

const enableBarcode = ref(false);
const rowIndex = ref(0);
const enableBarcodeFn = (index) => {
  rowIndex.value = index; 
  enableBarcode.value = true;
};

const onDecode = async (result, index) => { 
    let nIndex = rowIndex.value;
    if(index !== undefined){
        nIndex = index;
    } 
   
  await clientKey(authStore.token)
    .get("/api/fetch/asset-info/by/asset-code/" + result)
    .then((res) => {  
        assetDataObj.value[nIndex].item_description = res.data.asset_name; 
        assetDataObj.value[nIndex].asset_code = result;  
        enableBarcode.value = false; 
    })
    .catch((err) => {});
}; 
 
const locationTitleFrom = ref("");
const locationTitleTo = ref("");
const locationList = ref([]);
const fetchLocations = async () => {
    await clientKey(authStore.token)
        .get("/api/fetch/locations/non-paginated/data")
        .then((res) => {
            locationList.value = res.data;

            locationTitleFrom.value = res.data.filter(
                (e) => e.id == formObjData.value.transferred_from
            )?.[0]?.title;
            locationTitleTo.value = res.data.filter(
                (e) => e.id == formObjData.value.transferred_to
            )?.[0]?.title;
        })
        .catch((err) => {});
};

const AddAsset = () => {
    assetDataObj.value.push({ qty: 1, attachment: {} });
    requiredData();
};

const updateLocation = () => {
    isLoading.value = true;
    sbOptions.value = {
            status: true,
            type: 'info',
            text: 'Updating location...',
        };

        let newObjFrm = {
            authID: authStore.user.profile.id,
            data: objData.value
        };
  
     clientKey(authStore.token)
        .post("/api/request-update/location", newObjFrm)
        .then((res) => {
           setTimeout(() => {
            isLoading.value = false;
                sbOptions.value = {
                    status: true,
                    type: 'success',
                    text: 'Location updated.',
                };
           }, 1000);
        })
        .catch((err) => {});
}

const updateApprover = (user,index) => {
    sbOptions.value = {
        status: true,
        type: 'info',
        text: 'Updating User...',
    };
    let newObjFrm = {
        authID: authStore.user.profile.id,
        id: objData.value.id,
        profile_id: user,
        orders: index
    }
    clientKey(authStore.token)
        .post("/api/request-update/approver", newObjFrm)
        .then((res) => {
           setTimeout(() => {
                sbOptions.value = {
                    status: true,
                    type: 'success',
                    text: 'User has been updated.',
                };
           }, 800);
        })
        .catch((err) => {});
}

const deleteData = (id, index) => {
    assetDataObj.value.splice(index, 1);
    requiredData();
};

const deleteDraft = () => {
    sbOptions.value = {
            status: true,
            type: 'success',
            text: 'Draft has been removed!',
        };

    localStorage.removeItem("draft-request");
    setTimeout(() => { 
        location.reload();
    }, 500);
    
};

const saveDraft = () => {
    sbOptions.value = {
        status: true,
        type: "info",
        text: "Please wait...",
    };

    let formData = {
        data: objData.value,
        assets: assetDataObj.value,
        approval: approvalSetupList.value,
        profile_id: authStore.user.profile.id,
        role: authStore.user.profile.role,
        type: "request",
    };

    localStorage.setItem("draft-request", encryptData(formData)); 

    setTimeout(() => {
        isDraft.value = true;
        sbOptions.value = {
                status: true,
                type: 'success',
                text: 'Data has been saved to Draft',
            };
    }, 500);
 
}

const errorMsg = ref("Note: Administrator needs to setup signatories first.");
const selectedFilesIds = computed(() => selectedFiles.value.map((sf) => sf.id));
const submitRequest = () => {
    sbOptions.value = {
        status: true,
        type: "info",
        text: "Please wait...",
    };

    let newAssetDataObj = assetDataObj.value.map((o, i) => {
        o.file_id = "";
        if (o.attachment) {
            o.file_id = o.attachment.id;
        }
        delete o.assets;
        delete o.updated_at;
        delete o.created_at;
        delete o.id;
        delete o.attachment;
        return o;
    });

    let formData = {
        data: objData.value,
        assets: newAssetDataObj,
        approval: approvalSetupList.value,
        profile_id: authStore.user.profile.id,
        type: "request",
        role: authStore.user.profile.role,
        additionalFiles: selectedFilesIds.value,
    };

    clientKey(authStore.token)
        .post("/api/request-asset/store-update/data", formData)
        .then((res) => {
            let msg = "";
            let typeError = "";
            if (res.data.message) {
                msg = res.data.message;
                typeError = "success";
            } else if (res.data.error) {
                msg = res.data.error;
                typeError = "error";
            }
            sbOptions.value = {
                status: true,
                type: typeError,
                text: msg,
            };

            localStorage.removeItem("draft-request");

            if (!route.params.id) {
                setTimeout(() => {
                    router
                        .push({
                            name: "EditRequestAsset",
                            params: {
                                id: res.data.id,
                            },
                        })
                        .catch((err) => {});
                }, 800);
            }
        })
        .catch((err) => {
          sbOptions.value = {
                status: true,
                type: 'error',
                text: "Failed to update data, kindly refresh the page and check fields if it's empty",
            };
           
        });
};

const approvalSetupList = ref([]);
const hasSignatories = ref(true);

const listUom = ref(['Nos', 'Set', 'Pcs','Pack', 'Pallet', 'SqM']); 

const setupApprovals = async () => {
    let upIsEdit = 0;
    if (route.params.id) {
        upIsEdit = 1;
    }
    await clientKey(authStore.token)
        .get(
            "/api/fetch/request-asset/approval-setup-fetch/" +
                objData.value.request_type_id+"?isEdit="+upIsEdit
        )
        .then((res) => { 
           
            objData.value.extra_attachment = res.data.enable_attachment; 
            approvalSetupList.value = res.data?.stages;
            if (route.params.id && res.data?.stages.length > 0) {

                let currentApproval = onUpdateApproval.value.length - 1;
                let currentApprovalSetup = res.data?.stages.length; 
            
                if(currentApproval > currentApprovalSetup){  
                    approvalSetupList.value?.map((o, i) => { 
                        onUpdateApproval.value.map((oo,ii) => { 
                            if (o.sort === oo.orders) {
                                o.profile_id = oo.profile_id; 
                                o.status = oo.status;
                                o.date_approved = oo.date_approved;
                                o.reason_rejected = oo.reason_rejected;
                              
                                return o;
                            } 
                        })
                        return o;
                    }); 
              
                    if (approvalSetupList.value.length > 1) {
                    approvalSetupList.value[approvalSetupList.value.length] =
                        {
                            id: onUpdateApproval.value[
                            onUpdateApproval.value.length - 1
                            ].id,
                            profile_id:
                                onUpdateApproval.value[
                                onUpdateApproval.value.length - 1
                                ].profile_id,
                            status: onUpdateApproval.value[
                            onUpdateApproval.value.length - 1
                            ].status,
                            date_approved:
                                onUpdateApproval.value[
                                onUpdateApproval.value.length - 1
                                ].date_approved,
                            status: onUpdateApproval.value[
                            onUpdateApproval.value.length - 1
                            ].status,
                            reason_rejected: "",
                            types: onUpdateApproval.value[
                            onUpdateApproval.value.length - 1
                            ].approval_type,
                            sort: onUpdateApproval.value[
                            onUpdateApproval.value.length - 1
                            ].orders,
                            request_asset_id:
                                onUpdateApproval.value[
                                onUpdateApproval.value.length - 1
                                ].request_asset_id,
                            signatures: [
                                {
                                    id: onUpdateApproval.value[
                                    onUpdateApproval.value.length - 1
                                    ]?.profile.id,
                                    display_name:
                                        onUpdateApproval.value[
                                        onUpdateApproval.value.length - 1
                                        ]?.profile.display_name,
                                    first_name:
                                        onUpdateApproval.value[
                                        onUpdateApproval.value.length - 1
                                        ]?.profile.first_name,
                                    last_name:
                                        onUpdateApproval.value[
                                        onUpdateApproval.value.length - 1
                                        ]?.profile.last_name,
                                },
                            ],
                        };
                    } 
                   
                }else{ 
                    approvalSetupList.value.map((o, i) => {
                        o.profile_id = onUpdateApproval.value[i].profile_id;
                        o.status = onUpdateApproval.value[i].status;
                        o.date_approved = onUpdateApproval.value[i].date_approved;
                        o.reason_rejected =
                            onUpdateApproval.value[i].reason_rejected;
                        return o;
                    });

                    if (approvalSetupList.value.length > 1) {
                        approvalSetupList.value[onUpdateApproval.value.length - 1] =
                        {
                            id: onUpdateApproval.value[
                                onUpdateApproval.value.length - 1
                            ].id,
                            profile_id:
                                onUpdateApproval.value[
                                    onUpdateApproval.value.length - 1
                                ].profile_id,
                            status: onUpdateApproval.value[
                                onUpdateApproval.value.length - 1
                            ].status,
                            date_approved:
                                onUpdateApproval.value[
                                    onUpdateApproval.value.length - 1
                                ].date_approved,
                            status: onUpdateApproval.value[
                                onUpdateApproval.value.length - 1
                            ].status,
                            reason_rejected: "",
                            types: onUpdateApproval.value[
                                onUpdateApproval.value.length - 1
                            ].approval_type,
                            sort: onUpdateApproval.value[
                                onUpdateApproval.value.length - 1
                            ].orders,
                            request_asset_id:
                                onUpdateApproval.value[
                                    onUpdateApproval.value.length - 1
                                ].request_asset_id,
                            signatures: [
                                {
                                    id: onUpdateApproval.value[
                                        onUpdateApproval.value.length - 1
                                    ]?.profile.id,
                                    display_name:
                                        onUpdateApproval.value[
                                            onUpdateApproval.value.length - 1
                                        ]?.profile.display_name,
                                    first_name:
                                        onUpdateApproval.value[
                                            onUpdateApproval.value.length - 1
                                        ]?.profile.first_name,
                                    last_name:
                                        onUpdateApproval.value[
                                            onUpdateApproval.value.length - 1
                                        ]?.profile.last_name,
                                },
                            ],
                        };
                    }
                } 
            }
           
           
            if (res.data?.stages.length > 0) {
                hasSignatories.value = true;
            } else {
                hasSignatories.value = false;
            }
        })
        .catch((err) => {});
}; 
 
const isDraft = ref(false);

const requiredData = () => {
    let checkObj = false;
    isValidate.value = false;
    if (
        objData.value.company_id &&
        objData.value.request_type_id &&
        objData.value.subject &&
        objData.value.transferred_from &&
        objData.value.transferred_to
    ) {
        checkObj = true;
    }

    let checkAsset = true;
    assetDataObj.value.map((o) => {
        if (!o.item_description || !o.reason_for_request || !o.uom || (!o.qty || o.qty <= 0)) {
            checkAsset = false;
        }  
    });

    let checkSignatories = true;
    approvalSetupList.value.map((o) => {
        if (!o.profile_id) {
            checkSignatories = false;
        }
    });

    if (checkSignatories && checkAsset && checkObj) {
        isValidate.value = true;
    }
}; 

const changeRequestStatus = (v) => {
    let dataForm = {
        status: v,
        profile_id: authStore.user.profile.id,
        ID: formObjData.value.id,
    };
    clientKey(authStore.token)
        .post("/api/request-asset/change-request/data", dataForm)
        .then((res) => {
            sbOptions.value = {
                status: true,
                type: "success",
                text: res.data.message,
            };
            formObjData.value.status = v;
            emit("saved", res.data.message);
        })
        .catch((err) => {});
};

const pad = (v, size = 6) => {
    let s = "00000" + v;
    return s.substring(s.length - size);
};


onMounted(() => {
    fetchSetupRequest().then(() => {
        fetchCompanies().then(() => {
            fetchLocations();
        });
    });

    if (route.params.id) {
        isEdit.value = true;

        let v = props.objectdata;
        formObjData.value = v;

        objData.value.company_id = v.company_id;
        objData.value.request_type_id = v.request_type_id;
        objData.value.subject = v.subject;
        objData.value.transferred_from = v.transferred_from;
        objData.value.transferred_to = v.transferred_to;
        objData.value.id = v.id;
        objData.value.requestor_id = v.profile_id;
        onUpdateApproval.value = v.request_approvals;

        assetDataObj.value = v.items;
        getCurrentApprover.value = v.request_approvals?.filter(
            (e) => e.status == "awaiting-approval"
        )?.[0];
        
       
        if(!getCurrentApprover.value && v.status == 'reject'){
            getCurrentApprover.value = v.request_approvals?.filter(
            (e) => e.status == "reject"
            )?.[0];
        }
        
        selectedFiles.value = v.attachment;
        setupApprovals();
    } else {
        objData.value.company_id = authStore.user.profile.company_id;

        let getDraft = localStorage.getItem("draft-request");
        if(getDraft){ 
            getDraft = decryptData(getDraft);
            isDraft.value = true;
            
            objData.value.request_type_id = getDraft?.data?.request_type_id;
            objData.value.transferred_from = getDraft?.data?.transferred_from;
            objData.value.transferred_to = getDraft?.data?.transferred_to;
            objData.value.subject = getDraft?.data?.subject;

            if(objData.value.request_type_id){
                setupApprovals().then(() =>{
                    onUpdateApproval.value = getDraft.approval;
                    approvalSetupList.value.map((o, i) => {
                        o.profile_id = onUpdateApproval.value[i].profile_id;
                        o.status = onUpdateApproval.value[i].status;
                        o.date_approved = onUpdateApproval.value[i].date_approved;
                        o.reason_rejected =
                            onUpdateApproval.value[i].reason_rejected;
                        return o;
                    });
                });
            }

            assetDataObj.value = getDraft?.assets;
        }
    }
});

watch(objData.value, async (newVal, oldVal) => {
    requiredData();
});
watch(assetDataObj.value, async (newVal, oldVal) => {
    requiredData();
});

// check if not receiving-releasing
const is_receiving_releasing = computed(() => {
    return authStore.user.role == "receiving-releasing" ||
        authStore.user.role == "facility"
        ? true
        : false;
});
const appURL = ref(import.meta.env.VITE_APP_URL);
const requestSignatureUrl = computed(() => {
    let url = null;
    let baseURL = appURL.value + "/pv/employee-signatory";
    let randomKey = randomAlphaString(50);
    let randomKey2 = randomAlphaString(50);
    let pid = 95;
    let order = 99;

    if (authStore.user?.profile?.id == getCurrentApprover.value?.profile_id) {
        pid = getCurrentApprover.value?.profile_id;
        order = getCurrentApprover.value?.orders;
    }

    if (formObjData.value) {
        url =
            baseURL +
            "/transfer" +
            "/approvals?o=" +
            order +
            "&key=" +
            randomKey +
            "&pid=" +
            pid +
            "&pv=" +
            randomKey2 +
            "&id=" +
            formObjData.value.id;
    }

    return url;
});
</script>
<style>
.custom-width-description{
    max-width:20.6666%;
    flex: 0 0 20.6666666667%
}
.custom-width-uom{
    max-width:10.6666%;
    flex: 0 0 16.6666666667%
}
@media only screen and (max-width: 600px) {
    
.custom-width-description, .custom-width-uom{
    max-width:100%;
    flex: 0 0 100%
} 
}
</style>