<script setup>
import { reactive } from "vue";

const props = defineProps({
    roleId: null,
});
const local_data = reactive({
    orders: [],
});

const emit = defineEmits(["openPage", "displayOrder"]);

function getOrders() {
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
    local_data.orders = [];
    if (props.roleId == 1) {
        fetch("/Get_orders", {
            method: "POST",
            body: JSON.stringify({ date: nowDate }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                response.forEach((element) => {
                    const elem = {
                        status: element.status_id,
                        id: element.id,
                    };
                    local_data.orders.push(elem);
                });
            });
    } else {
        fetch("/Get_my_orders", {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                response.forEach((element) => {
                    const elem = {
                        status: element.status_id,
                        id: element.id,
                    };
                    local_data.orders.push(elem);
                });
            });
    }
}

function nextPage(index) {
    emit("openPage", index);
}

function showOrder(index) {
    emit("displayOrder", index);
}
function deleteOrder(elem) {
    let confirmation = confirm(
        "Вы уверены? Данная команда окончательно удалит этот заказ."
    );
    if (confirmation) {
        fetch("/Delete_order", {
            method: "POST",
            body: JSON.stringify({ id: elem }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                if (response) {
                    getOrders();
                }
            });
    }
}

getOrders();
</script>
<template>
    <div>
        <div class="field">
            <div v-for="option in local_data.orders">
                <div class="order_buttons" v-show="option.status === 1">
                    <button @click="showOrder(option.id)" class="button-order">
                        <img class="pict" src="../../img/new.png" /> заказ №{{
                            option.id
                        }}
                    </button>
                    <img
                        @click="deleteOrder(option.id)"
                        v-show="props.roleId == 1"
                        class="close_pict"
                        src="../../img/delete.png"
                    />
                </div>
                <div class="order_buttons" v-show="option.status === 2">
                    <button @click="showOrder(option.id)" class="button-order">
                        <img class="pict" src="../../img/working.png" /> заказ
                        №{{ option.id }}
                    </button>
                    <img
                        @click="deleteOrder(option.id)"
                        v-show="props.roleId == 1"
                        class="close_pict"
                        src="../../img/delete.png"
                    />
                </div>
                <div class="order_buttons" v-show="option.status === 3">
                    <button @click="showOrder(option.id)" class="button-order">
                        <img class="pict" src="../../img/ready.png" /> заказ №{{
                            option.id
                        }}
                    </button>
                    <img
                        @click="deleteOrder(option.id)"
                        v-show="props.roleId == 1"
                        class="close_pict"
                        src="../../img/delete.png"
                    />
                </div>
                <div class="order_buttons" v-show="option.status === 4">
                    <button @click="showOrder(option.id)" class="button-order">
                        <img class="pict" src="../../img/cancelled.png" /> заказ
                        №{{ option.id }}
                    </button>
                    <img
                        @click="deleteOrder(option.id)"
                        v-show="props.roleId == 1"
                        class="close_pict"
                        src="../../img/delete.png"
                    />
                </div>
            </div>
        </div>
        <div class="buttons" v-show="props.roleId == 1">
            <button @click="nextPage(2)">Добавить заказ</button>
            <button @click="nextPage(6)">Добавить пользователя</button>
        </div>
    </div>
</template>
<style scoped>
.order_buttons {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 20px;
}

.close_pict {
    height: 20px;
}

.close_pict:hover {
    cursor: pointer;
}

.pict {
    height: 20px;
    margin-right: 20px;
}

.buttons {
    margin-top: 20px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 10px;
}

.field {
    display: flex;
    flex-direction: column;
    margin-top: 30px;
    gap: 10px;
}

.header {
    text-align: center;
    margin-bottom: 30px;
    font-size: 30px;
}

.button-order {
    display: flex;
    flex-direction: row;
    align-items: center;
    background-color: white;
    border-color: var(--color-accent);
    color: var(--color-accent);
}

.button-order:hover {
    background-color: var(--color-accent);
    color: white;
    transition: 1s;
}
</style>
