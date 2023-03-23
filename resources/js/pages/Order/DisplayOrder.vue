<script setup>
import { reactive } from "vue";

const props = defineProps({
    nowDate: null,
    orderId: null,
    roleId: null,
});
const local_data = reactive({
    order: {},
    reportDates: [],
    editOrCreate: "create",
});
const emit = defineEmits([
    "openPage",
    "editOrder",
    "createReport",
    "editReport",
    "modal",
]);

function returnToCabinet() {
    emit("openPage", "order-main");
}
function editOrder() {
    emit("editOrder", props.orderId);
}
function editReport() {
    emit("editReport", props.orderId);
}

function writeReport(index) {
    if (local_data.order.startAt <= props.nowDate) {
        emit("createReport", index);
    } else {
        emit("modal", ["confirm", "Заказ еще не начат"]);
    }
}

function getDisplayOrder() {
    fetch("/Get_display_order", {
        method: "POST",
        body: JSON.stringify({ id: props.orderId }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            local_data.order.startAt = response[0].created_at;
            local_data.order.id = props.orderId;
            local_data.order.city = response[0].city;
            local_data.order.model = response[0].model;
            local_data.order.housing = response[0].housing;
            local_data.order.fio = response[0].fio;
            local_data.order.phone = response[0].phone;
            local_data.order.budget = response[0].budget;

            if (response[1] == null) {
                local_data.order.moneyLeft = response[0].budget;
            } else {
                var spent =
                    (response[0].daily_cost + response[0].rent) *
                    response[1].length;
                response[2].forEach((elem) => {
                    spent += elem.price;
                });
                local_data.order.moneyLeft = response[0].budget - spent;
            }

            local_data.order.tangen = response[0].tangen;
            local_data.order.cup = response[0].cup;
            local_data.order.wheel_pair = response[0].wheel_pairs;

            if (response[1] == null) {
                local_data.order.tangenLeft = response[0].tangen;
                local_data.order.cupLeft = response[0].cup;
                local_data.order.wheel_pairs_ready = 0;
            } else {
                var spentTangen = 0;
                var spentCup = 0;
                var spentWheelPairs = 0;
                response[1].forEach((elem) => {
                    spentTangen += Number(elem.tangen);
                    spentCup += Number(elem.cup);
                    spentWheelPairs += Number(elem.wheel_pair);
                });
                local_data.order.tangenLeft = response[0].tangen - spentTangen;
                local_data.order.cupLeft = response[0].cup - spentCup;
                local_data.order.wheel_pairs_ready = spentWheelPairs;
            }
        });
}
function getReportDates() {
    fetch("/Get_order_report_dates", {
        method: "POST",
        body: JSON.stringify({ id: props.orderId }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.reportDates.push(element.date);
                if (local_data.reportDates.includes(props.nowDate)) {
                    local_data.editOrCreate = "edit";
                } else {
                    local_data.editOrCreate = "create";
                }
            });
        });
}

getReportDates();
getDisplayOrder();
</script>
<template>
    <div>
        <div class="header">Заказ № {{ local_data.order.id }}</div>
        <div class="line_fields">
            <div>Город</div>
            <div class="line_out">{{ local_data.order.city }}</div>
        </div>
        <div class="line_fields">
            <div>Модель</div>
            <div class="line_out">{{ local_data.order.model }}</div>
        </div>
        <div class="line_fields">
            <div>Жилье</div>
            <div class="line_out">{{ local_data.order.housing }}</div>
        </div>
        <div class="block">
            <div class="block_header">Контактное лицо</div>
            <div class="line_fields">
                <div class="line_in">ФИО</div>
                <div class="line_out">{{ local_data.order.fio }}</div>
            </div>
            <div class="line_fields">
                <div class="line_in">Телефон</div>
                <div class="line_out">{{ local_data.order.phone }}</div>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Бюджет</div>
            <table class="tables">
                <thead>
                    <tr>
                        <th>Выделено</th>
                        <th>Осталось</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>{{ local_data.order.budget }}</th>
                        <th>{{ local_data.order.moneyLeft }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="block">
            <div class="block_header">Резцы</div>
            <table class="tables">
                <thead>
                    <tr>
                        <th></th>
                        <th>Выделено</th>
                        <th>Осталось</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Тангец</th>
                        <th>{{ local_data.order.tangen }}</th>
                        <th>{{ local_data.order.tangenLeft }}</th>
                    </tr>
                    <tr>
                        <th>Чашка</th>
                        <th>{{ local_data.order.cup }}</th>
                        <th>{{ local_data.order.cupLeft }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="block">
            <div class="block_header">Колесные пары</div>
            <table class="tables">
                <thead>
                    <tr>
                        <th>Всего</th>
                        <th>Обточено</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>{{ local_data.order.wheel_pair }}</th>
                        <th>{{ local_data.order.wheel_pairs_ready }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="buttons">
            <button @click="editOrder" v-show="props.roleId == 1">
                Редактировать
            </button>
            <button
                @click="writeReport(local_data.order.id)"
                v-show="
                    props.roleId == 2 && local_data.editOrCreate == 'create'
                "
            >
                Создать отчет
            </button>
            <button
                @click="editReport"
                v-show="props.roleId == 2 && local_data.editOrCreate == 'edit'"
            >
                Редактировать отчет
            </button>
            <button @click="returnToCabinet">Назад</button>
        </div>
    </div>
</template>
<style scoped>
.header {
    display: flex;
    text-align: center;
    align-self: center;
    margin-bottom: 30px;
}

.buttons {
    gap: 10px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.line_fields {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    width: 100%;
    margin-bottom: 10px;
}

.line_out {
    width: 70%;
    text-align: center;
    border-bottom-style: solid;
    border-width: 2px;
    border-color: var(--color-input);
    margin-right: 10px;
}
.line_in {
    margin-left: 10px;
}

.block {
    border-style: solid;
    border-radius: 10px;
    margin-bottom: 20px;
    border-width: 1px;
    border-color: var(--color-input);
}
.block_header {
    margin-bottom: 15px;
    margin-top: 5px;
    margin-left: 10px;
}

.tables {
    width: 100%;
}

.error {
    margin-top: 20px;
    text-align: center;
    font-size: 20px;
    color: red;
}
</style>
