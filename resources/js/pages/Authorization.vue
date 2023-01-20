<script setup>
import customInput from "../components/Input.vue";
import { reactive, ref } from "vue";

const local_data = reactive({
    phone: null,
    password: null,

    fieldType: "password",

    errorPhone: false,
    errorPassword: false,
});

//и регулярка//
function Validate() {
    if (local_data.phone != null) {
        if (
            (local_data.phone.length === 12 ||
                local_data.phone.length === 11) &&
            (local_data.phone[0] === "8" ||
                (local_data.phone[0] === "+" && local_data.phone[1] === "7"))
        ) {
            local_data.errorPhone = false;
        } else {
            local_data.errorPhone = true;
        }
    } else {
        local_data.errorPhone = true;
    }

    if (local_data.password != null) {
        local_data.errorPassword = false;
    } else {
        local_data.errorPassword = true;
    }
    return !(local_data.errorPhone || local_data.errorPassword);
}
function Authorize() {
    if (Validate()) {
        const datuum = {
            phone: local_data.phone,
            password: local_data.password,
        };

        fetch("/Authorizate", {
            method: "POST",
            body: JSON.stringify(datuum),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => console.log(response));
    }
}

function RevealPassword() {
    if (local_data.fieldType === "password") {
        local_data.fieldType = "text";
    } else {
        local_data.fieldType = "password";
    }
}
</script>
<template>
    <div class="page">
        <customInput
            v-model="local_data.phone"
            inputname="Телефон"
            typeIn="text"
            :ifError="local_data.errorPhone"
        />
        <div class="part">
            <customInput
                v-model="local_data.password"
                inputname="Пароль"
                :typeIn="local_data.fieldType"
                :ifError="local_data.errorPassword"
            />
            <img @click="RevealPassword" class="pict" src="../../img/eye.png" />
        </div>

        <button @click="Authorize">Войти</button>
    </div>
</template>
<style scoped>
.page {
    display: flex;
    flex-direction: column;
    gap: 30px;
}
.pict {
    width: 50px;
    height: 50px;
    position: relative;
}
.part {
    display: flex;
    flex-direction: row;
    width: 100%;
}
</style>
