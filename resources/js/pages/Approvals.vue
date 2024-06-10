<template>
    <v-container style="max-width: 90%" class="mt-0 pt-0">
        <v-row class="mt-0 pt-0 no-print mt-2" v-if="assetsOnly?.length > 0">
            <div>
                <v-btn color="primary" size="small" @click="printFn"
                    >Print</v-btn
                >
            </div>
        </v-row>
        <v-row class="mt-0 pt-0">
            <div class="v-col-12 v-col-md-3 mt-auto font-weight-bold">
                <div class="d-flex">
                    <div class="mr-2">FROM:</div>
                    <div>{{ dataObj.data?.transfer_from?.title }}</div>
                </div>
                <div class="d-flex">
                    <div class="mr-6 pr-2">TO:</div>
                    <div>{{ dataObj.data?.transfer_to?.title }}</div>
                </div>
            </div>
            <div class="v-col-12 v-col-md-6 text-center">
                <img
                    src="/assets/images/grandiose-logo.jpg"
                    style="max-height: 100px"
                />
                <br />
                <strong>{{ dataObj.data?.setup?.title }}</strong>
            </div>

            <div class="v-col-12 v-col-md-3 mt-auto approval-date font-weight-bold">
                SN-{{ dataObj?.data?.types == "request" ? "5" : "3"
                }}{{ pad(pvID) }}<br />
                {{ useFormatDate(dataObj.data?.created_at) }}
            </div>
        </v-row>
        <v-divider class="my-5"></v-divider>
        <v-row>
            <div class="v-col-1 v-col-md-1 py-1">IMG</div>
            <div class="v-col-1 v-col-md-1 py-1">ITEM DESC</div>
            <div class="v-col-1 v-col-md-1 py-1">ASSET NO</div>
            <div class="v-col-1 v-col-md-1 py-1">QTY</div>
            <div class="v-col-1 v-col-md-1 py-1">UOM</div>
            <div class="v-col-1 v-col-md-1 py-1">WEIGHT</div>
            <div class="v-col-1 v-col-md-1 py-1">ITEM VALUE</div>
            <div class="v-col-1 v-col-md-1 py-1">COUNTRY OF ORIGIN</div>
            <div class="v-col-2 v-col-md-2 py-1">
                REMARKS<br />REASON FOR REQUEST
            </div>
            <div class="v-col-2 v-col-md-2 py-1 d-flex justify-space-between">
                REMARKS
                <div style="position: relative">
                    <div v-if="noticeLoader" class="checkbox-notice"></div>
                    <v-checkbox
                        v-if="is_asset_supervisor || is_receiver"
                        v-model="checkAll"
                        @update:modelValue="checkUncheckBox"
                        class="pa-0 ma-0 v-col-1"
                        hide-details
                        :disabled="requestStatus == 'cancelled'"
                        variant="underlined"
                        density="compact"
                    ></v-checkbox>
                </div>
            </div>
        </v-row>
        <v-row
            class="mt-1 approval-form-pr"
            v-for="(item, index) in assetsOnly"
            :key="item.id"
        >
            <div class="v-col-12 v-col-md-2 py-1 px-1 d-flex">
                <div v-if="item.attachment?.id">
                    <v-card
                        @click="() => openAttachment(item)"
                        maxHeight="40"
                        variant="text"
                        class="ma-2"
                    >
                        <v-icon
                            size="large"
                            :icon="mdiImage"
                            color="success"
                        ></v-icon>
                    </v-card>
                    <v-dialog
                        v-model="dialogAttachment"
                        width="95%"
                        max-width="900"
                    >
                        <v-card class="bg-black">
                            <div
                                style="height: 680px; width: 100%"
                                class="d-flex align-center justify-center"
                            >
                                <v-img
                                    :src="
                                        baseURL +
                                        '/file/' +
                                        fileItem.attachment.path
                                    "
                                ></v-img>
                            </div>
                        </v-card>
                    </v-dialog>
                </div>
                <v-card v-else maxHeight="40" variant="text" class="ma-2">
                    <v-icon size="large" :icon="mdiImageOffOutline"></v-icon>
                </v-card>

                <v-textarea
                    :value="item.item_description"
                    variant="underlined"
                    density="compact"
                    hide-details
                    rows="2"
                    class="bg-light-gray d-flex flex-column-reverse"
                    :readonly="true"
                ></v-textarea>
            </div>
            <div class="v-col-12 v-col-md-1 py-1 px-1 d-flex">
                <v-text-field
                    v-model="item.asset_code"
                    variant="underlined"
                    density="compact"
                    hide-details
                    :readonly="
                        !is_asset_supervisor || requestStatus == 'cancelled'
                    "
                    :class="`${
                        !is_asset_supervisor || requestStatus == 'cancelled'
                            ? 'bg-light-gray d-flex flex-column-reverse'
                            : ''
                    }`"
                    v-if="is_asset_supervisor && requestStatus != 'cancelled'"
                ></v-text-field>

                <div
                    v-else
                    class="bg-light-gray d-flex flex-column-reverse flex-start-end border-bottom"
                >
                    {{ item.asset_code }}
                </div>
            </div>
            <div class="v-col-12 v-col-md-1 py-1 px-1 d-flex">
                <v-text-field
                    :value="item.qty"
                    variant="underlined"
                    density="compact"
                    hide-details
                    class="bg-light-gray d-flex flex-column-reverse"
                    type="number"
                    :readonly="true"
                ></v-text-field>
            </div>
            <div class="v-col-12 v-col-md-1 py-1 px-1 d-flex">
                <v-text-field
                    :value="item.uom"
                    variant="underlined"
                    density="compact"
                    hide-details
                    class="bg-light-gray d-flex flex-column-reverse"
                    :readonly="true"
                ></v-text-field>
            </div>
            <div class="v-col-12 v-col-md-1 py-1 px-1 d-flex">
                <v-text-field
                    v-model="item.weight"
                    variant="underlined"
                    density="compact"
                    hide-details
                    :readonly="
                        !is_asset_supervisor || requestStatus == 'cancelled'
                    "
                    :class="`${
                        !is_asset_supervisor || requestStatus == 'cancelled'
                            ? 'bg-light-gray d-flex flex-column-reverse'
                            : ''
                    }`"
                ></v-text-field>
            </div>
            <div class="v-col-12 v-col-md-1 py-1 px-1 d-flex">
                <v-text-field
                    v-model="item.item_value"
                    variant="underlined"
                    density="compact"
                    hide-details
                    :readonly="
                        !is_asset_supervisor || requestStatus == 'cancelled'
                    "
                    :class="`${
                        !is_asset_supervisor || requestStatus == 'cancelled'
                            ? 'bg-light-gray d-flex flex-column-reverse'
                            : ''
                    }`"
                ></v-text-field>
            </div>
            <div class="v-col-12 v-col-md-1 py-1 px-1 d-flex">
                <v-text-field
                    v-model="item.country_of_origin"
                    variant="underlined"
                    density="compact"
                    hide-details
                    :readonly="
                        !is_asset_supervisor || requestStatus == 'cancelled'
                    "
                    :class="`${
                        !is_asset_supervisor || requestStatus == 'cancelled'
                            ? 'bg-light-gray d-flex flex-column-reverse'
                            : ''
                    }`"
                    style="width: 100%"
                ></v-text-field>
            </div>
            <div class="v-col-12 v-col-md-2 py-1 px-1 d-flex">
                <v-textarea
                    :value="item.reason_for_request"
                    variant="underlined"
                    density="compact"
                    hide-details
                    class="bg-light-gray d-flex flex-column-reverse"
                    :readonly="true"
                    rows="2"
                ></v-textarea>
            </div>

            <div
                v-if="is_asset_supervisor"
                class="v-col-12 v-col-md-2 py-1 d-flex justify-space-between"
            >
                <v-textarea
                    v-model="item.remarks"
                    variant="underlined"
                    density="compact"
                    hide-details
                    class="text-remarks"
                    :class="`${
                        requestStatus == 'cancelled'
                            ? 'bg-light-gray d-flex flex-column-reverse'
                            : ''
                    }`"
                    placeholder="Add remarks here"
                    rows="2"
                ></v-textarea>
                <v-checkbox
                    :disabled="requestStatus == 'cancelled'"
                    v-model="item.is_available"
                    class="pa-0 ma-0 v-col-1"
                    hide-details
                    variant="underlined"
                    density="compact"
                    @update:modelValue="checkUncheckBoxSingle"
                ></v-checkbox>
            </div>
            <div
                v-if="is_commercial_manager"
                class="v-col-12 v-col-md-2 py-1 d-flex"
            >
                <div>
                    <div v-if="item.remarks">
                        <strong> Asset Supervisor - Projects</strong> <br />
                        <p>{{ item.remarks }}</p>
                        -------
                    </div>
                    <v-textarea
                        v-model="item.remarks_commercial"
                        variant="underlined"
                        density="compact"
                        hide-details
                        class="text-remarks"
                        placeholder="Add remarks here"
                        rows="2"
                    ></v-textarea>
                </div>
                <div v-if="dataObj?.data?.is_available">
                    <v-icon
                    style="position:absolute; right:60px;"
                        class="mt-3"
                        :icon="item.is_available ? mdiCheckBold : mdiCloseThick"
                        :color="`${item.is_available ? 'success' : 'error'}`"
                    ></v-icon>
                </div>
            </div>
            <div v-else-if="is_realeasing" class="v-col-12 v-col-md-2 py-1">
                <div>
                    <div v-if="item.remarks">
                        <strong> Asset Supervisor - Projects</strong> <br />
                        <pre style="text-wrap: wrap">{{ item.remarks }}</pre>
                        -------
                    </div>
                    <div v-if="item.remarks_commercial">
                        <strong> Commercial Manager - Projects</strong> <br />
                        <pre style="text-wrap: wrap">{{
                            item.remarks_commercial
                        }}</pre>
                        -------
                    </div>
                    <div v-if="item.remarks_transport">
                        <strong> Transport</strong> <br />
                        <pre style="text-wrap: wrap">{{
                            item.remarks_transport
                        }}</pre>
                        -------
                    </div>
                    <div v-if="item.remarks_release">
                        <strong> Releasing</strong> <br />
                        <pre style="text-wrap: wrap">{{
                            item.remarks_release
                        }}</pre>
                        -------
                    </div>
                </div>
                <v-textarea
                    v-model="item.remarks_release"
                    variant="underlined"
                    density="compact"
                    hide-details
                    class="text-remarks"
                    placeholder="Add remarks here"
                    rows="2"
                ></v-textarea>
            </div>
            <div v-else-if="is_transport" class="v-col-12 v-col-md-2 py-1">
                <div>
                    <div v-if="item.remarks">
                        <strong> Asset Supervisor - Projects</strong> <br />
                        <pre style="text-wrap: wrap">{{ item.remarks }}</pre>
                        -------
                    </div>
                    <div v-if="item.remarks_commercial">
                        <strong> Commercial Manager - Projects</strong> <br />
                        <pre style="text-wrap: wrap">{{
                            item.remarks_commercial
                        }}</pre>
                        -------
                    </div>
                    <div v-if="item.remarks_transport">
                        <strong> Transport</strong> <br />
                        <pre style="text-wrap: wrap">{{
                            item.remarks_transport
                        }}</pre>
                        -------
                    </div>
                </div>
                <v-textarea
                    v-model="item.remarks_transport"
                    variant="underlined"
                    density="compact"
                    hide-details
                    class="text-remarks"
                    placeholder="Add remarks here"
                    rows="2"
                ></v-textarea>
            </div>
            <div
                v-else-if="is_receiver"
                class="v-col-12 v-col-md-2 py-1 d-flex justify-space-between"
            >
                <div>
                    <div>
                        <div v-if="item.remarks">
                            <strong> Asset Supervisor - Projects</strong> <br />
                            <pre style="text-wrap: wrap">{{
                                item.remarks
                            }}</pre>
                            -------
                        </div>
                        <div v-if="item.remarks_commercial">
                            <strong> Commercial Manager - Projects</strong>
                            <br />
                            <pre style="text-wrap: wrap">{{
                                item.remarks_commercial
                            }}</pre>
                            -------
                        </div>
                        <div v-if="item.remarks_transport">
                            <strong> Transport</strong> <br />
                            <pre style="text-wrap: wrap">{{
                                item.remarks_transport
                            }}</pre>
                            -------
                        </div>
                        <div v-if="item.remarks_release">
                            <strong> Releasing</strong> <br />
                            <pre style="text-wrap: wrap">{{
                                item.remarks_release
                            }}</pre>
                            -------
                        </div>
                    </div>

                    <v-textarea
                        v-model="item.remarks_receive"
                        variant="underlined"
                        density="compact"
                        hide-details
                        class="text-remarks"
                        placeholder="Add remarks here"
                        rows="2"
                    ></v-textarea>
                </div>
                <div v-if="dataObj?.data?.is_available" class="my-auto">
                    <v-icon
                        class="mt-3"
                        :icon="item.is_available ? mdiCheckBold : mdiCloseThick"
                        :color="`${item.is_available ? 'success' : 'error'}`"
                    ></v-icon>
                </div>
                <div
                    class="pa-0 ma-0 v-col-1 my-auto"
                    style="position: relative"
                >
                    <div v-if="noticeLoader" class="checkbox-notice"></div>
                    <v-checkbox
                        v-model="item.is_received"
                        hide-details
                        variant="underlined"
                        density="compact"
                        @update:modelValue="checkUncheckBoxSingle"
                    ></v-checkbox>
                </div>
            </div>
            <div
                v-else-if="!is_asset_supervisor"
                class="v-col-12 v-col-md-2 py-1 d-flex justify-space-between ssss"
            >
                <div
                    style="border-bottom: 1px solid #000000"
                    class="pa-1"
                    v-if="
                        item.remarks ||
                        item.remarks_commercial ||
                        item.remarks_release ||
                        item.remarks_receive ||
                        item.remarks_transport
                    "
                >
                    <div v-if="item.remarks">
                        <strong> Asset Supervisor - Projects</strong> <br />
                        <pre style="text-wrap: wrap">{{ item.remarks }}</pre>
                        -------
                    </div>
                    <div v-if="item.remarks_commercial">
                        <strong> Commercial Manager - Projects</strong> <br />
                        <pre style="text-wrap: wrap">{{
                            item.remarks_commercial
                        }}</pre>
                        -------
                    </div>
                    <div v-if="item.remarks_transport">
                        <strong> Transport</strong> <br />
                        <pre style="text-wrap: wrap">{{
                            item.remarks_transport
                        }}</pre>
                        -------
                    </div>
                    <div v-if="item.remarks_release">
                        <strong> Releasing</strong> <br />
                        <pre style="text-wrap: wrap">{{
                            item.remarks_release
                        }}</pre>
                        -------
                    </div>
                    <div v-if="item.remarks_receive">
                        <strong> Receiver</strong>
                        <pre style="text-wrap: wrap">{{
                            item.remarks_receive
                        }}</pre>
                    </div>
                </div>
                <div v-else></div>
                <div v-if="dataObj?.data?.is_available">
                    <v-icon
                        class="mt-3"
                        :icon="item.is_available ? mdiCheckBold : mdiCloseThick"
                        :color="`${item.is_available ? 'success' : 'error'}`"
                    ></v-icon>
                </div>
                <div
                    v-if="
                        dataObj?.data?.status == 'complete' &&
                        checkLastApproval == 'receiver'
                    "
                >
                    <v-icon
                        class="mt-3"
                        :icon="
                            item.is_received ? mdiTruckDelivery : mdiTruckRemove
                        "
                        :color="`${item.is_received ? 'success' : 'error'}`"
                    ></v-icon>
                </div>
            </div>
        </v-row>
        <v-row class="mt-3" v-if="is_asset_supervisor">
            <div
                class="v-col-12 text-center font-weight-bold text-info"
                v-if="requestStatus !== 'cancelled'"
            >
                YOU CAN UPDATE SOME DATA AND<br />
                PLEASE TICK CHECKBOX IF ASSET IS AVAILABLE BEFORE CLICKING THE
                APPROVE BUTTON
            </div>
            <div class="v-col-12 text-center font-weight-bold text-info" v-else>
                THIS REQUEST HAS BEEN CANCELLED.
            </div>
        </v-row>
        <v-row
            v-if="dataObj?.data?.is_available && requestStatus !== 'cancelled'"
            class="mt-6 mb-2"
        >
            <v-card
                variant="text"
                class="v-col-12 text-center pt-2 font-weight-bold pb-0 d-flex justify-center"
            >
                <div>
                    <v-icon :icon="mdiCheckBold" color="success"></v-icon>
                    AVAILABLE
                    <v-icon
                        :icon="mdiCloseThick"
                        color="error"
                        class="ml-5"
                    ></v-icon>
                    NOT AVAILABLE
                </div>
                <div v-if="is_receiver" class="ml-5 d-flex">
                    <div class="px-2 mr-5">|</div>
                    <div>
                        <v-icon :icon="mdiCheckboxBlankOutline"></v-icon> TICK
                        RECEIVED ASSETS
                        <v-divider
                            :thickness="8"
                            color="error"
                            class="divider-notice"
                        ></v-divider>
                    </div>
                </div>
                <div
                    class="ml-6"
                    v-if="
                        dataObj.data?.status == 'complete' &&
                        checkLastApproval == 'receiver'
                    "
                >
                    <v-icon :icon="mdiTruckDelivery" color="success"></v-icon>
                    RECEIVED
                    <v-icon
                        :icon="mdiTruckRemove"
                        color="error"
                        class="ml-5"
                    ></v-icon>
                    NOT RECEIVE
                </div>
            </v-card>
        </v-row>
        <v-row class="mt-3">
            <div class="v-col-12 px-0 my-0">
                <v-divider></v-divider>
                <v-divider></v-divider>
            </div>
        </v-row>
        <!-- <v-row v-if="requestStatus != 'cancelled' && dataObj.data?.request_approvals[route.query.o].status == 'awaiting-approval' &&
                        route.query.pid == dataObj.data?.request_approvals[route.query.o].profile_id &&
                        route.query.o == dataObj.data?.request_approvals[route.query.o].orders">
            <v-col md="12" class="text-center text-info text-caption">
                By clicking the approve button,
            </v-col>
        </v-row> -->
        <v-row
            v-if="requestTypeId == 2 && selectedFiles?.length > 0"
            class="my-3 d-flex justify-center"
        >
            <div
                v-for="(file, index) in selectedFiles"
                :key="file.id"
                class="pa-2"
                style="position: relative"
            >
                <v-card @click="() => openAttachmentMultiple(file, index)">
                    <v-icon
                        size="large"
                        :icon="mdiImage"
                        color="success"
                    ></v-icon>
                </v-card>
            </div>

            <v-dialog v-model="dialogAttachment" width="95%" max-width="900">
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
                                    :src="baseURL + '/file/' + item.path"
                                ></v-img>
                                <object
                                    v-if="fileType == 'pdf'"
                                    :data="baseURL + '/file/' + item.path"
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

        <v-row v-if="dataObj.data?.request_approvals.length > 0">
            <div class="v-col-12 v-col-md-3 pa-0 mb-4">
                <div
                    class="text-center mx-2 py-4 font-weight-bold"
                    :style="`${
                        requestStatus == 'cancelled'
                            ? 'color: red;'
                            : 'color: rgb(3, 167, 3);'
                    } border: 1px solid #ccc; font-size: 17px;`"
                >
                    {{
                        requestStatus == "cancelled" ? "CANCELLED" : "APPROVED"
                    }}
                </div>
                <div class="text-center py-1 font-weight-bold">
                    Requested By
                </div>
                <div class="text-center py-1">
                    {{ dataObj.data?.profile?.display_name }}
                </div>
                <div class="text-center py-1">
                    {{ dataObj.data?.profile?.designation }}
                </div>
                <div class="text-center text-caption">
                    {{ useFormatDate(dataObj.data?.created_at) }}
                </div>
            </div>
            <div
                class="v-col-12 v-col-md-3 pa-0 mb-4"
                v-for="item in dataObj.data.request_approvals"
                :key="item.id"
            >
                <div
                    v-if="
                        (item.profile.ecode == '100884' ||
                            item.profile.ecode == '102587' ||
                            item.profile.ecode == '100316' ||
                            item.profile?.role == 'commercial-manager' ||
                            (item.profile?.role == 'technical-operation' &&
                                requestTypeId == 2)) &&
                        route.query.pid == item.profile_id &&
                        route.query.o == item.orders
                    "
                >
                    <div
                        class="d-flex"
                        v-if="item.status == 'awaiting-approval'"
                    >
                        <div
                            @click="rejectRequest(item)"
                            class="cursor-pointer text-center py-5 v-col-6"
                            :style="`border:1px solid #ccc; ${
                                route.query.pid == item.profile_id &&
                                route.query.o == item.orders
                                    ? 'background-color: rgb(182, 3, 3);color:#fff;'
                                    : 'background-color: none;'
                            }`"
                        >
                            REJECT
                        </div>
                        <div
                            @click="approvalFn(item, 'reject')"
                            class="cursor-pointer text-center py-5 v-col-6"
                            :style="`border:1px solid #ccc; ${
                                route.query.pid == item.profile_id &&
                                route.query.o == item.orders
                                    ? 'background-color: rgb(3, 167, 3);color:#fff;'
                                    : 'background-color: none'
                            }`"
                        >
                            APPROVE
                        </div>
                    </div>
                    <div
                        v-else-if="
                            item.status == 'reject' ||
                            item.status == 'done' ||
                            item.status == 'pending'
                        "
                    >
                        <div
                            @click="approvalFn(item)"
                            class="cursor-pointer text-center mx-2 py-5"
                            :style="`border:1px solid #ccc; ${
                                item.status == 'reject' &&
                                route.query.pid == item.profile_id &&
                                route.query.o == item.orders
                                    ? 'background-color: gray; color:#fff;'
                                    : item.status == 'done'
                                    ? 'background-color: none;color:rgb(3, 167, 3); font-weight:bold;font-size:17px;padding-top:16px !important;padding-bottom:16px !important;'
                                    : 'background-color: gray; color:#fff;'
                            }`"
                        >
                            {{
                                item.status == "done"
                                    ? "APPROVED"
                                    : item.status == "reject"
                                    ? "REJECTED"
                                    : "CLICK TO APPROVE"
                            }}
                        </div>
                    </div>
                </div>
                <div
                    v-else
                    @click="approvalFn(item)"
                    class="cursor-pointer text-center mx-2 py-5"
                    :style="`border:1px solid #ccc; ${
                        requestStatus != 'cancelled' &&
                        item.status == 'awaiting-approval' &&
                        route.query.pid == item.profile_id &&
                        route.query.o == item.orders
                            ? 'background-color: rgb(182, 3, 3);color:#fff;'
                            : item.status == 'done'
                            ? 'background-color: none;color:rgb(3, 167, 3); font-weight:bold;font-size:17px;padding-top:16px !important;padding-bottom:16px !important;'
                            : 'background-color: gray; color:#fff;'
                    }`"
                >
                    {{
                        item.status == "done" ? "APPROVED" : "CLICK TO APPROVE"
                    }}
                </div>
                <div
                    :class="`text-center py-1 font-weight-bold ${
                        item.status == 'reject'
                            ? 'text-error text-uppercase'
                            : ''
                    }`"
                >
                    {{
                        item.status == "reject"
                            ? "Rejected"
                            : statusFn(item.approval_type)
                    }}
                    By
                </div>
                <div class="text-center py-1">
                    {{ item.profile?.display_name }}
                </div>
                <div class="text-center py-1">
                    {{ item.profile?.designation }}
                </div>
                <div class="text-center text-caption">
                    {{
                        item.date_approved
                            ? useFormatDate(item.date_approved)
                            : ""
                    }}
                </div>
                <div
                    class="v-col-12 text-caption"
                    v-if="item.reason_rejected"
                    style="border: 1px solid red; border-radius: 5px"
                >
                    Reason rejected
                    <v-divider class="my-2"></v-divider>
                    {{ item.reason_rejected }}
                </div>
            </div>
        </v-row>
        <v-row v-if="dataObj?.data?.status !== 'complete'" class="no-print">
            <div class="v-col-12 text-center">
                <v-divider></v-divider>
                You will receive a notification once you click the approve
                button.
                <v-divider></v-divider>
            </div>
        </v-row>

        <v-dialog width="500" v-model="rejectReason" persistent>
            <v-card>
                <v-card-text>
                    <v-row>
                        <div class="v-col-12 text-center mt-3 font-weight-bold">
                            REASON FOR REJECTION
                        </div>
                        <div class="v-col-12">
                            <v-textarea
                                v-model="reasonOfReject"
                                label="STATE YOUR REASON HERE"
                                variant="outlined"
                            ></v-textarea>
                        </div>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="cancelReject">Cancel</v-btn>

                    <v-btn
                        color="success"
                        :disabled="!reasonOfReject"
                        @click="approvalFn(rejectItem, 'reject')"
                        >Confirm</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <AppSnackBar :options="sbOptions" />
    </v-container>
</template>

<script setup>
import { ref } from "vue";
import { useRoute } from "vue-router";
import { useFormatDate } from "@/composables/formatDate.js";
import AppSnackBar from "@/components/AppSnackBar.vue";
import {
    mdiCheckBold,
    mdiCloseThick,
    mdiCheckboxBlankOutline,
    mdiTruckDelivery,
    mdiTruckRemove,
    mdiImage,
    mdiImageOffOutline,
} from "@mdi/js";

const route = useRoute();
const isValid = ref(true);
const dataObj = ref({});
const sbOptions = ref({});
const is_asset_supervisor = ref(false);
const is_realeasing = ref(false);
const is_transport = ref(false);
const is_commercial_manager = ref(false);
const is_receiver = ref(false);
const assetsOnly = ref([]);
const rejectReason = ref(false);
const reasonOfReject = ref("");
const noticeLoader = ref(false);
const pvID = ref(route.query.id);
const requestStatus = ref("");
const requestTypeId = ref(0);
const selectedFiles = ref([]);
const queryData = async () => {
    let formData = {
        id: route.query.id,
        profile_id: route.query.pid,
        order: route.query.o,
    };

    if (!route.query.id || !route.query.pv) {
        isValid.value = false;
        return;
    }

    await axios
        .post("/api/pv/fetch/request-assets/data", formData)
        .then((res) => {
            dataObj.value = res.data;
            if (!dataObj.value.access) {
                isValid.value = false;
                return;
            }
            requestStatus.value = res.data?.data?.status;
            requestTypeId.value = res.data?.data?.request_type_id;
            selectedFiles.value = res.data?.data?.attachment;

            if (dataObj.value?.data?.request_approvals?.length > 0) {
                is_asset_supervisor.value =
                    dataObj.value.data.request_approvals.filter(
                        (o) =>
                            o.profile_id == route.query.pid &&
                            o.orders == route.query.o &&
                            o.status == "awaiting-approval" &&
                            o.profile?.role == "asset-supervisor"
                    )[0];
                is_realeasing.value =
                    dataObj.value.data.request_approvals.filter(
                        (o) =>
                            o.profile_id == route.query.pid &&
                            o.orders == route.query.o &&
                            o.status == "awaiting-approval" &&
                            o.approval_type == "releasing"
                    )[0];

                is_transport.value =
                    dataObj.value.data.request_approvals.filter(
                        (o) =>
                            o.profile_id == route.query.pid &&
                            o.orders == route.query.o &&
                            o.status == "awaiting-approval" &&
                            o.approval_type == "transport"
                    )[0];

                is_commercial_manager.value =
                    dataObj.value.data.request_approvals.filter(
                        (o) =>
                            o.profile_id == route.query.pid &&
                            o.orders == route.query.o &&
                            o.status == "awaiting-approval" &&
                            o.profile?.role == "commercial-manager"
                    )[0];

                is_receiver.value = dataObj.value.data.request_approvals.filter(
                    (o) =>
                        o.profile_id == route.query.pid &&
                        o.orders == route.query.o &&
                        o.status == "awaiting-approval" &&
                        o.approval_type == "receiver"
                )[0];

                checkLastApproval.value =
                    dataObj.value.data.request_approvals[
                        dataObj.value.data.request_approvals.length - 1
                    ].approval_type;

                if (is_receiver.value || is_asset_supervisor.value) {
                    if (requestStatus.value != "cancelled") {
                        noticeLoader.value = true;
                    }
                }
            }
            assetsOnly.value = dataObj.value?.data?.items;
        })
        .catch((err) => {
            console.log(err.response.data);
        });
};

const currentSlider = ref(1);
const fileType = ref("image");
const openAttachmentMultiple = (file, index) => {
    let mimeType = file.path.split(".");
    fileType.value = mimeType[mimeType.length - 1];

    currentSlider.value = index;
    dialogAttachment.value = true;
};

const printFn = () => {
    window.print();
};
const baseURL = ref(window.location.origin);
const dialogAttachment = ref(false);
const fileItem = ref(1);
const openAttachment = (item) => {
    fileItem.value = item;
    dialogAttachment.value = true;
};

const checkLastApproval = ref("");

const rejectItem = ref({});
const rejectRequest = (item) => {
    rejectReason.value = true;
    rejectItem.value = item;
};
const cancelReject = () => {
    rejectReason.value = false;
    reasonOfReject.value = "";
};
const approvalFn = (item, isReject = null) => {
    rejectReason.value = false;
    if (requestStatus.value == "cancelled" || item.status == "reject") {
        return;
    }
    if (
        (item.status == "awaiting-approval" || item.status == "reject") &&
        route.query.pid == item.profile_id &&
        route.query.o == item.orders
    ) {
        sbOptions.value = {
            status: true,
            type: "info",
            text: "Please wait...",
        };
        let fixeAsset = [];

        if (
            is_asset_supervisor.value ||
            is_commercial_manager.value ||
            is_receiver.value ||
            is_realeasing.value ||
            is_transport.value
        ) {
            fixeAsset = assetsOnly.value.map((o, i) => {
                delete o.created_at;
                delete o.assets;
                delete o.updated_at;
                delete o.attachment;
                return o;
            });
        }

        let formData = {};
        let is_reject = "";

        if (reasonOfReject.value && isReject) {
            is_reject = reasonOfReject.value;
        }

        formData = {
            profile_id: route.query.pid,
            id: route.query.id,
            order: route.query.o,
            type: dataObj.value.data.types,
            assets: fixeAsset,
            is_reject: is_reject,
            requestor_id: dataObj.value.data.profile_id,
        };

        axios
            .post("/api/pv/store/request-asset/approve-data", formData)
            .then((res) => {
                let typeInfo = "success";
                if (!res.data.success) {
                    typeInfo = "error";
                }
                sbOptions.value = {
                    status: true,
                    type: typeInfo,
                    text: res.data.message,
                };

                noticeLoader.value = false;
                setTimeout(() => {
                    queryData();
                }, 500);
            })
            .catch((err) => {
                console.log(err.response.data);
            });

        return;
    }
    return;
};

const statusFn = (v) => {
    if (v == "approve") {
        return "Approved";
    } else if (v == "reviewer") {
        return "Reviewed";
    } else if (v == "receiver") {
        return "Received";
    } else if (v == "transport") {
        return "Transport Arranged";
    } else if (v == "releasing") {
        return "Released";
    } else if (v == "verify") {
        return "Verified";
    }
};

const checkUncheckBox = () => {
    let isReceiver = is_receiver.value;
    noticeLoader.value = false;
    assetsOnly.value.map((o) => {
        if (isReceiver) {
            o.is_received = checkAll.value;
        } else {
            o.is_available = checkAll.value;
        }
    });
};

const pad = (v, size = 6) => {
    let s = "00000" + v;
    return s.substring(s.length - size);
};

const checkUncheckBoxSingle = () => {
    let validateBoxes = true;
    let isReceiver = is_receiver.value;
    assetsOnly.value.map((o) => {
        if (isReceiver) {
            if (!o.is_received) {
                validateBoxes = false;
            }
        } else {
            if (!o.is_available) {
                validateBoxes = false;
            }
        }
    });
    checkAll.value = validateBoxes;
};
const checkAll = ref(false);
queryData();
</script>
<style>
.bg-light-gray {
    background-color: #f3f3f3;
    width: 100%;
}
.bg-light-gray input.v-field__input {
    padding-left: 5px !important;
}
.border-bottom{
    border-bottom: 1px solid #a1a1a1;
}
div.py-1,
.approval-form-pr textarea,
.approval-form-pr input {
    font-size: 12px !important;
}

.divider-notice {
    animation: transform 2s infinite;
}

.approval-date{ text-align: right;}

@media only screen and (max-width: 600px) {
    .approval-date{ text-align: center;}
}


@keyframes transform {
    0% {
        transform: scale(0.2);
    }

    50% {
        transform: scale(1.3, 0.4);
    }

    100% {
        transform: scale(0.2);
    }
}

.checkbox-notice {
    background: rgba(250, 73, 73, 0.5);
    border-radius: 50%;
    margin: 10px;
    left: -6px;
    height: 20px;
    position: absolute;
    width: 20px;
    box-shadow: 0 0 0 0 rgb(250, 73, 73, 0);
    transform: scale(1);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.7);
    }

    70% {
        transform: scale(2);
        box-shadow: 0 0 0 10px rgba(255, 0, 0, 0);
    }

    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(255, 0, 0, 0);
    }
}
</style>
