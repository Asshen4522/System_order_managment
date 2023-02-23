<script setup>
import customInput from "../components/Input.vue";
import { reactive } from "vue";

const emit = defineEmits(["openPage", "modal"]);

const local_data = reactive({
    name: "",
    surname: "",
    phone: "",
    password: "",
    roleId: null,

    fieldType: "password",

    errorName: false,
    errorSurname: false,
    errorPhone: false,
    errorPassword: false,

    errorRegister: false,
});

function RevealPassword() {
    if (local_data.fieldType === "password") {
        local_data.fieldType = "text";
    } else {
        local_data.fieldType = "password";
    }
}
function Validate() {
    local_data.errorName = local_data.name == "";
    local_data.errorSurname = local_data.surname == "";
    local_data.errorPhone = !/^(8|\+7)(\d){10}$/.test(local_data.phone);
    local_data.errorPassword = local_data.password == "";
    if (
        !local_data.errorName &&
        !local_data.errorSurname &&
        !local_data.errorPhone &&
        !local_data.errorPassword &&
        local_data.roleId != null
    ) {
        return true;
    } else {
        return false;
    }
}
function Register() {
    if (Validate()) {
        fetch("/Register", {
            method: "POST",
            body: JSON.stringify({
                phone: local_data.phone,
                password: local_data.password,
                name: local_data.name,
                surname: local_data.surname,
                roleId: +local_data.roleId,
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
                    returnToCabinet();
                }
            });
    } else {
        emit("modal", [
            "confirm",
            "Не все данные заполнены корректно или пользователя создать не удалось",
        ]);
    }
}
function returnToCabinet() {
    emit("openPage", 11);
}
</script>
<template>
    <div class="page">
        <div>
            <customInput
                class="inputs"
                v-model="local_data.name"
                inputname="Имя"
                typeIn="text"
                :ifError="local_data.errorName"
            />
            <customInput
                class="inputs"
                v-model="local_data.surname"
                inputname="Фамилия"
                typeIn="text"
                :ifError="local_data.errorSurname"
            />
        </div>
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
        <div class="selector">
            <div>Роль пользователя</div>
            <select v-model="local_data.roleId">
                <option value="1">Менеджер</option>
                <option value="2">Исполнитель</option>
            </select>
        </div>
        <div class="buttons">
            <button @click="Register">Создать</button>
            <button @click="returnToCabinet">Назад</button>
        </div>
    </div>
</template>
<style scoped>
.selector {
    margin-left: 10%;
}
.page {
    display: flex;
    flex-direction: column;
    gap: 30px;
}
.pict {
    width: 25px;
    height: 25px;
    margin-left: 20px;
    position: relative;
}
.part {
    display: flex;
    flex-direction: row;
    align-items: center;
    width: 100%;
}

.buttons {
    margin-top: 20px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 10px;
}
.inputs {
    width: 75%;
    margin-left: 10%;
}

img:hover {
    cursor: pointer;
}
</style>
