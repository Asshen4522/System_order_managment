<script setup>
import customInput from "../components/Input.vue";
import { reactive } from "vue";

const emit = defineEmits(["openPage"]);

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
        console.log(true);
        return true;
    } else {
        console.log(false);
        local_data.errorRegister = true;
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
    }
}
function returnToCabinet() {
    emit("openPage", 3);
}
</script>
<template>
    <div class="page">
        <div>
            <div class="header__head">Регистрация нового пользователя</div>
            <div class="header__text">
                Введите имя и фамилию нового пользователя, его номер телефона, а
                также пароль, по которому он будет входить в систему. Также
                выберите роль нового пользователя
            </div>
        </div>
        <div>
            <customInput
                v-model="local_data.name"
                inputname="Имя"
                typeIn="text"
                :ifError="local_data.errorName"
            />
            <customInput
                v-model="local_data.surname"
                inputname="Фамилия"
                typeIn="text"
                :ifError="local_data.errorSurname"
            />
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
        <div>
            <div>Роль пользователя</div>
            <select v-model="local_data.roleId">
                <option disabled value="">Роль</option>
                <option value="1">Менеджер</option>
                <option value="2">Исполнитель</option>
            </select>
        </div>
        <div class="buttons">
            <button @click="Register">Создать</button>
            <button @click="returnToCabinet">Назад</button>
        </div>

        <div class="error" v-show="local_data.errorRegister">
            Не все данные заполнены корректно или пользователя создать не
            удалось
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
.buttons {
    margin-top: 20px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 10px;
}

.error {
    text-align: center;
    font-size: 20px;
    color: red;
}
</style>
