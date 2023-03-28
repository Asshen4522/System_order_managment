<script setup>
import Authorization from "./pages/Authorization.vue";
import Header from "./pages/Header.vue";
import Modal from "./components/Modal.vue";

import Cabinet from "./pages/Order/Cabinet.vue";
import CreateOrder from "./pages/Order/CreateOrder.vue";
import DisplayOrder from "./pages/Order/DisplayOrder.vue";
import EditOrder from "./pages/Order/EditOrder.vue";

import ListReport from "./pages/Report/ListReport.vue";
import CreateReport from "./pages/Report/CreateReport.vue";
import DisplayReport from "./pages/Report/DisplayReport.vue";
import EditReport from "./pages/Report/EditReport.vue";

import ListUser from "./pages/User/ListUser.vue";
import CreateUser from "./pages/User/CreateUser.vue";
import DisplayUser from "./pages/User/DisplayUser.vue";
import EditUser from "./pages/User/EditUser.vue";

import Stats from "./pages/Statistiks/Stats.vue";

import { reactive } from "vue";

const local_data = reactive({
    currentPage: null,
    pickUser: null,
    pickOrder: null,
    pickReport: null,
    roleId: null,
    nowDate: null,
    userFia: null,

    showModal: false,
    typeModal: "confirm",
    textModal: "",
});

function modal(data) {
    local_data.showModal = true;
    local_data.typeModal = data[0];
    local_data.textModal = data[1];
}
function closeModal() {
    local_data.showModal = false;
}
function yesModal() {
    local_data.showModal = true;
}
function noModal() {
    local_data.showModal = true;
}

function curDate() {
    let date = new Date();
    let nowDate = String(date.getFullYear()) + "-";
    if (String(date.getMonth() + 1).length == 1) {
        nowDate += "0" + String(date.getMonth() + 1) + "-";
    } else {
        nowDate += String(date.getMonth() + 1) + "-";
    }
    if (String(date.getDate()).length == 1) {
        nowDate += "0" + String(date.getDate());
    } else {
        nowDate += String(date.getDate());
    }
    local_data.nowDate = nowDate;
}

function ifAuth() {
    return fetch("/If_auth", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            if (response) {
                local_data.currentPage = "order-main";
                local_data.roleId = response[0];
                local_data.userFia = response[1] + " " + response[2];
            } else {
                local_data.currentPage = "auth";
            }
        });
}

function changePage(index) {
    local_data.currentPage = index;
}
function editOrder(orderId) {
    local_data.currentPage = "order-edit";
    local_data.pickOrder = orderId;
}
function writeReport(index) {
    local_data.pickOrder = index;
    local_data.currentPage = "report-add";
}
function showOrder(index) {
    local_data.currentPage = "order-show";
    local_data.pickOrder = index;
}
function editReport(orderId) {
    local_data.currentPage = "report-edit";
    local_data.pickOrder = orderId;
}
function displayReport(reportId) {
    local_data.currentPage = "report-show";
    local_data.pickReport = reportId;
}
function displayReports(index) {
    local_data.currentPage = "report-main";
    local_data.pickOrder = index;
}
function displayUser(index) {
    local_data.currentPage = "user-show";
    local_data.pickUser = index;
}

curDate();
ifAuth();
</script>

<template>
    <div class="zone">
        <Modal
            v-if="local_data.showModal"
            @close="closeModal"
            @yes="yesModal"
            @no="noModal"
            :type="local_data.typeModal"
            :text="local_data.textModal"
        />
        <div v-if="local_data.currentPage != 'auth'">
            <Header
                @openPage="changePage"
                :roleId="local_data.roleId"
                :currentPage="local_data.currentPage"
                :userFia="local_data.userFia"
            />
        </div>
        <div v-if="local_data.currentPage === 'auth'">
            <Authorization @auth="ifAuth" @modal="modal" />
        </div>
        <div v-if="local_data.currentPage === 'order-main'">
            <Cabinet
                @openPage="changePage"
                @displayOrder="showOrder"
                @displayReports="displayReports"
                :roleId="local_data.roleId"
                :nowDate="local_data.nowDate"
            />
        </div>
        <div v-if="local_data.currentPage === 'order-add'">
            <CreateOrder
                @openPage="changePage"
                @modal="modal"
                :nowDate="local_data.nowDate"
            />
        </div>
        <div v-if="local_data.currentPage === 'order-show'">
            <DisplayOrder
                @createReport="writeReport"
                @openPage="changePage"
                @editOrder="editOrder"
                @editReport="editReport"
                @modal="modal"
                :orderId="local_data.pickOrder"
                :roleId="local_data.roleId"
                :nowDate="local_data.nowDate"
            />
        </div>
        <div v-if="local_data.currentPage === 'order-edit'">
            <EditOrder
                @openPage="changePage"
                @modal="modal"
                :orderId="local_data.pickOrder"
            />
        </div>

        <div v-if="local_data.currentPage === 'report-main'">
            <ListReport
                @openPage="changePage"
                @displayReport="displayReport"
                :orderId="local_data.pickOrder"
            />
        </div>
        <div v-if="local_data.currentPage === 'report-add'">
            <CreateReport
                :orderId="local_data.pickOrder"
                :nowDate="local_data.nowDate"
                @openPage="changePage"
            />
        </div>
        <div v-if="local_data.currentPage === 'report-show'">
            <DisplayReport
                @openPage="changePage"
                :reportId="local_data.pickReport"
            />
        </div>
        <div v-if="local_data.currentPage === 'report-edit'">
            <EditReport
                @openPage="changePage"
                :orderId="local_data.pickOrder"
                :nowDate="local_data.nowDate"
            />
        </div>

        <div v-if="local_data.currentPage === 'user-main'">
            <ListUser
                @openPage="changePage"
                @displayUser="displayUser"
                :roleId="local_data.roleId"
            />
        </div>
        <div v-if="local_data.currentPage === 'user-add'">
            <CreateUser @openPage="changePage" @modal="modal" />
        </div>
        <div v-if="local_data.currentPage === 'user-show'">
            <DisplayUser @openPage="changePage" :userId="local_data.pickUser" />
        </div>
        <div v-if="local_data.currentPage === 'user-edit'">
            <EditUser
                @openPage="changePage"
                @modal="modal"
                :userId="local_data.pickUser"
            />
        </div>

        <div v-if="local_data.currentPage === 'stat'">
            <Stats @openPage="changePage" :nowDate="local_data.nowDate" />
        </div>
    </div>
</template>

<style scoped></style>
