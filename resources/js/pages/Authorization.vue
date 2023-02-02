<script setup>
import customInput from "../components/Input.vue";
import { reactive, ref } from "vue";

const emit = defineEmits(["auth"]);

const local_data = reactive({
    phone: null,
    password: "",

    fieldType: "password",

    errorPhone: false,
    errorPassword: false,

    errorLogin: false,
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
                if (response == false) {
                    local_data.errorLogin = true;
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
        <div>
            <div class="header__head">Вход в систему</div>
            <div class="header__text">
                Введите ваш номер телефона, без пробелов и иных знаков, в
                формате 8xxxxxxxxxx или +7xxxxxxxxxx, и выданный вам
                администратором пароль.
            </div>
        </div>
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

        <div class="error" v-show="local_data.errorLogin">
            Неверная пара телефон-пароль
        </div>
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

.header__head {
    font-size: 30px;
    text-align: center;
    margin-bottom: 20px;
}
.header__text {
    font-size: 20px;
    text-align: center;
    margin-bottom: 20px;
}

.error {
    text-align: center;
    font-size: 20px;
    color: red;
}
</style>
