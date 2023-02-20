<script setup>
import { reactive } from "vue";
const local_data = reactive({
    users: [],
});
const props = defineProps({
    roleId: null,
});
const emit = defineEmits(["openPage", "displayUser"]);

function returnToCabinet(index) {
    emit("openPage", index);
}
function displayUser(index) {
    emit("displayUser", index);
}

function getUsers() {
    local_data.users = [];
    fetch("/Get_userList", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                let send = {};
                send.fio = element.name + " " + element.surname;
                if (element.role_id == 2) {
                    send.role = "Исполнитель";
                } else {
                    send.role = "Менеджер";
                }
                send.banned = element.banned;
                send.id = element.id;
                local_data.users.push(send);
            });
        });
}

function deleteUser(userId) {
    let confirmation = confirm(
        "Вы уверены? Данная команда окончательно удалит этого пользователя."
    );
    if (confirmation) {
        fetch("/Delete_user", {
            method: "POST",
            body: JSON.stringify({ id: userId }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                if (response) {
                    getUsers();
                }
            });
    }
}

function ban(user) {
    if (user.banned == 0) {
        fetch("/Ban", {
            method: "POST",
            body: JSON.stringify({ id: user.id }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                user.banned = 1;
            });
    } else {
        fetch("/UnBan", {
            method: "POST",
            body: JSON.stringify({ id: user.id }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                user.banned = 0;
            });
    }
}

getUsers();
</script>
<template>
    <div>
        <div class="list">
            <div v-for="user in local_data.users" class="user">
                <button @click="displayUser(user.id)" class="user-btn">
                    <div class="user-fio">{{ user.fio }}</div>
                    <div class="user-role">{{ user.role }}</div>
                </button>
                <div
                    class="switch_btn"
                    :class="{ switch_on: user.banned == 0 }"
                    @click="ban(user)"
                ></div>
                <img
                    @click="deleteUser(user.id)"
                    class="close_pict"
                    src="../../img/delete.png"
                />
            </div>
        </div>

        <div class="buttons">
            <button @click="returnToCabinet(6)">Создать пользователя</button>
        </div>
    </div>
</template>
<style scoped>
.list {
    width: 100%;
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
    gap: 15px;
}

.user {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    width: 100%;
}
.user-btn {
    display: flex;
    background-color: white;
    justify-content: space-between;
    flex-direction: row;
    gap: 10px;
    min-width: 65%;
    border-color: var(--color-accent);
    color: var(--color-accent);
}

.user-btn:hover {
    background-color: var(--color-accent);
    color: white;
    transition: 1s;
}

.buttons {
    gap: 10px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.close_pict {
    height: 20px;
}

.close_pict:hover {
    cursor: pointer;
}

.switch_btn {
    display: inline-block;
    width: 72px; /* ширина */
    height: 24px; /* высота */
    border-radius: 19px; /* радиус скругления */
    background: #bfbfbf; /* цвет фона */
    z-index: 0;
    margin: 0;
    padding: 0;
    border: none;
    cursor: pointer;
    position: relative;
    transition-duration: 300ms; /* анимация */
}
.switch_btn::after {
    content: "";
    height: 18px; /* высота кнопки */
    width: 18px; /* ширина кнопки */
    border-radius: 17px;
    background: #fff; /* цвет кнопки */
    top: 3px; /* положение кнопки по вертикали относительно основы */
    left: 3px; /* положение кнопки по горизонтали относительно основы */
    transition-duration: 300ms; /* анимация */
    position: absolute;
    z-index: 1;
}
.switch_on {
    background: var(--color-accent);
}
.switch_on::after {
    left: 50px;
}

@media only screen and (max-width: 425px) {
    .switch_on::after {
        left: 16px;
    }
}
</style>
