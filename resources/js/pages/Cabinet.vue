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
    if (props.roleId == 1) {
        fetch("/Get_orders", {
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

getOrders();
</script>
<template>
    <div>
        <div class="header">
            <div v-show="props.roleId == 1">Личный кабинет менеджера</div>
            <div v-show="props.roleId == 2">Личный кабинет исполнителя</div>
        </div>

        <div class="field">
            <div v-for="option in local_data.orders">
                <div v-show="option.status === 1">
                    <button @click="showOrder(option.id)" class="button-order">
                        Новый заказ #{{ option.id }}
                    </button>
                </div>
                <div v-show="option.status === 2">
                    <button @click="showOrder(option.id)" class="button-order">
                        Работающий заказ #{{ option.id }}
                    </button>
                </div>
                <div v-show="option.status === 3">
                    <button @click="showOrder(option.id)" class="button-order">
                        Выполненный заказ #{{ option.id }}
                    </button>
                </div>
                <div v-show="option.status === 4">
                    <button @click="showOrder(option.id)" class="button-order">
                        Отмененный заказ #{{ option.id }}
                    </button>
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
