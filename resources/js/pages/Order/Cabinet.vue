<script setup>
import { reactive } from "vue";

const props = defineProps({
    roleId: null,
    nowDate: null,
});
const local_data = reactive({
    orders: [],
    showFilter: false,
    filterStatus: 0,
    dateStart: "",
    dateEnd: "",
});

const emit = defineEmits(["openPage", "displayOrder", "displayReports"]);

function getOrders() {
    local_data.orders = [];
    if (props.roleId == 1) {
        fetch("/Get_orders", {
            method: "POST",
            body: JSON.stringify({ date: props.nowDate }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                response.forEach((element) => {
                    const elem = {
                        city: element.city,
                        status: element.status_id,
                        id: element.id,
                        fio: element.name + " " + element.surname,
                        date: element.created_at,
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
                        city: element.city,
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
function displayReports(index) {
    emit("displayReports", index);
}

function deleteOrder(orderId) {
    let confirmation = confirm(
        "Вы уверены? Данная команда окончательно удалит этот заказ."
    );
    if (confirmation) {
        fetch("/Delete_order", {
            method: "POST",
            body: JSON.stringify({ id: orderId }),
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
            <div class="field_filter">
                <img
                    v-show="props.roleId == 1"
                    src="../../../img/filter.png"
                    class="btn_pict"
                    @click="local_data.showFilter = !local_data.showFilter"
                />
                <div
                    class="filters"
                    v-show="props.roleId == 1"
                    v-if="local_data.showFilter"
                >
                    <div class="filter_elem">
                        <div>Фильтровать по статусу</div>
                        <select v-model="local_data.filterStatus">
                            <option :value="0">Без фильтрации</option>
                            <option :value="1">Новые</option>
                            <option :value="2">В работе</option>
                            <option :value="3">Законченные</option>
                            <option :value="4">Отмененные</option>
                        </select>
                    </div>

                    <div class="filter_elem">
                        <div>Фильтровать по дате</div>
                        <div class="date_filter">
                            <div class="date_lines">
                                <div>От</div>
                                <input
                                    v-model="local_data.dateStart"
                                    type="date"
                                />
                            </div>
                            <div class="date_lines">
                                <div>До</div>
                                <input
                                    v-model="local_data.dateEnd"
                                    type="date"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="orders">
                <div v-for="option in local_data.orders">
                    <div
                        class="order_buttons"
                        v-if="
                            (local_data.filterStatus == 0 ||
                                local_data.filterStatus == option.status) &&
                            (local_data.dateStart == '' ||
                                local_data.dateStart <= option.date) &&
                            (local_data.dateEnd == '' ||
                                local_data.dateEnd >= option.date)
                        "
                    >
                        <button
                            @click="showOrder(option.id)"
                            class="button-order"
                        >
                            <div v-if="option.status === 1">
                                <img class="pict" src="../../../img/new.png" />
                            </div>
                            <div v-else-if="option.status === 2">
                                <img
                                    class="pict"
                                    src="../../../img/working.png"
                                />
                            </div>
                            <div v-else-if="option.status === 3">
                                <img
                                    class="pict"
                                    src="../../../img/ready.png"
                                />
                            </div>
                            <div v-else>
                                <img
                                    class="pict"
                                    src="../../../img/cancelled.png"
                                />
                            </div>

                            <div>заказ №{{ option.id }}</div>
                            <div class="addition">
                                {{ option.city }}
                            </div>
                            <div v-if="props.roleId == 1" class="addition">
                                {{ option.fio }}
                            </div>
                            <div class="addition">
                                {{ option.date }}
                            </div>
                        </button>
                        <button
                            v-show="props.roleId == 1"
                            @click="displayReports(option.id)"
                        >
                            Отчеты
                        </button>
                        <img
                            @click="deleteOrder(option.id)"
                            v-show="props.roleId == 1"
                            class="btn_pict"
                            src="../../../img/delete.png"
                        />
                    </div>
                </div>
            </div>
            <div class="buttons" v-show="props.roleId == 1">
                <button @click="nextPage('order-add')">Добавить заказ</button>
            </div>
        </div>
    </div>
</template>
<style scoped>
.field_filter {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.orders {
    height: 600px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    overflow-y: scroll;
}
.field {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.filters {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 10%;
    margin-bottom: 15px;
}
.filter_elem {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 45%;
}
.date_filter {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
.date_lines {
    display: flex;
    flex-direction: row;
    gap: 5px;
}

.order_buttons {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.btn_pict {
    height: 20px;
    width: 20px;
    border-style: solid;
    border-width: 1px;
    border-color: var(--color-accent);
    padding: 5px;
    border-radius: 8px;
}

.btn_pict:hover {
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

.header {
    text-align: center;
    margin-bottom: 30px;
    font-size: 30px;
}

.button-order {
    width: 75%;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: left;
    gap: 20px;
    background-color: white;
    border-color: var(--color-accent);
    color: var(--color-accent);
}
.addition {
    width: 30%;
}

.button-order:hover {
    background-color: var(--color-accent);
    color: white;
    transition: 1s;
}

@media only screen and (min-width: 1441px) {
}
@media only screen and (max-width: 636px) {
    .addition {
        display: none;
    }
    .date_filter {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
}
</style>
