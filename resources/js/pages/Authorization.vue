<script setup>
import customInput from "../components/Input.vue";
import { reactive, ref } from "vue";

const emit = defineEmits(["auth", "modal"]);

const local_data = reactive({
    phone: null,
    password: "",

    fieldType: "password",

    errorPhone: false,
    errorPassword: false,
});

function Validate() {
    local_data.errorPhone = !/^(8|\+7)(\d){10}$/.test(local_data.phone);
    local_data.errorPassword = local_data.password == "";
    return !local_data.errorPhone && !local_data.errorPassword;
}
function Authorize() {
    if (Validate()) {
        fetch("/Authorizate", {
            method: "POST",
            body: JSON.stringify({
                phone: local_data.phone,
                password: local_data.password,
            }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                if (response == "no") {
                    emit("modal", ["confirm", "Неверная пара логин-парполь"]);
                } else if (response == "banned") {
                    emit("modal", ["confirm", "Ваш аккаунт заблокирован"]);
                } else {
                    emit("auth", response);
                }
            });
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
            class="inputs"
            v-model="local_data.phone"
            inputname="Телефон"
            typeIn="text"
            :ifError="local_data.errorPhone"
        />
        <div class="part">
            <customInput
                class="inputs"
                v-model="local_data.password"
                inputname="Пароль"
                :typeIn="local_data.fieldType"
                :ifError="local_data.errorPassword"
            />
            <img
                v-if="local_data.fieldType == 'password'"
                @click="RevealPassword"
                class="pict"
                src="../../img/eye.png"
            />
            <img
                v-if="local_data.fieldType == 'text'"
                @click="RevealPassword"
                class="pict"
                src="../../img/hidden.png"
            />
        </div>
        <div class="for_button">
            <button @click="Authorize">Войти</button>
        </div>
    </div>
</template>
<style scoped>
@media only screen and (min-width: 1441px) {
    .page {
        display: flex;
        flex-direction: column;
        gap: 30px;
        margin-left: 300px;
        margin-right: 300px;
    }
}
@media only screen and (max-width: 425px) {
    .page {
        display: flex;
        flex-direction: column;
        gap: 30px;
        margin-top: 100px;
    }
}

.for_button {
    display: flex;
    justify-content: center;
}
.page {
    display: flex;
    flex-direction: column;
    gap: 30px;
}
.pict {
    width: 25px;
    height: 25px;
    position: relative;
    margin-left: 20px;
}
.part {
    display: flex;
    flex-direction: row;
    align-items: center;
    width: 100%;
}

.error {
    text-align: center;
    font-size: 20px;
    color: red;
}
.inputs {
    width: 75%;
    margin-left: 10%;
}

img:hover {
    cursor: pointer;
}
</style>
