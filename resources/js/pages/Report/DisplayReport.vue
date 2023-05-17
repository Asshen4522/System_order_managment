<script setup>
import { reactive } from "vue";

const props = defineProps({
    reportId: null,
});
const local_data = reactive({
    report: {
        date: null,
        wheel_pairs: [],
        tangen: 0,
        cup: 0,
        costs: [],
        activities: [],
        comment: null,
        id: props.orderId,
    },

    activities: [],
    costs: [],
    inner_costs: [],
});

const emit = defineEmits(["openPage"]);

function returnToCabinet() {
    emit("openPage", "report-main");
}

function getActivities() {
    local_data.activities = [];
    return fetch("/Get_activities", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.activities.push(element);
            });
        });
}
function getCosts() {
    local_data.costs = [];
    return fetch("/Get_costs", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.costs.push(element);
            });
        });
}
function getOrderCosts() {
    fetch("/Get_inner_costs_report", {
        method: "POST",
        body: JSON.stringify({ id: props.reportId }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.inner_costs.push(element);
            });
        });
}
function getReport() {
    fetch("/Get_display_report", {
        method: "POST",
        body: JSON.stringify({ id: props.reportId }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            local_data.report.date = response[0].date;
            local_data.report.id = response[0].id;
            local_data.report.tangen = response[0].tangen;
            local_data.report.cup = response[0].cup;
            local_data.report.comment = response[0].comment;
            response[1].forEach((element) => {
                local_data.report.activities.push({
                    id: element.activity_id,
                    name: local_data.activities[element.activity_id - 1].name,
                });
            });
            response[2].forEach((element) => {
                local_data.report.costs.push({
                    id: element.cost_id,
                    price: element.price,
                    name: local_data.costs[element.cost_id - 1].name,
                });
            });
            response[3].forEach((element) => {
                local_data.report.wheel_pairs.push({
                    name: element.model,
                    count: element.amount,
                });
            });
        });
}

getOrderCosts();
Promise.all([getCosts(), getActivities()]).then(getReport);
</script>
<template>
    <div class="page">
        <div class="block_header">
            Дневной отчет от {{ local_data.report.date }}
        </div>
        <div class="block">
            <div class="block_header">Финансовые затраты</div>
            <div class="block_list_line">
                <div>Суточные</div>
                <div>{{ local_data.inner_costs[0]?.daily_cost }} р.</div>
            </div>
            <div class="block_list_line">
                <div>Квартира</div>
                <div>{{ local_data.inner_costs[0]?.rent }} р.</div>
            </div>

            <div
                class="block_list_line"
                v-for="elem in local_data.report.costs"
            >
                <div>{{ elem.name }}</div>
                <div>{{ elem.price }} р.</div>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Занятость</div>
            <div>Обточка КП</div>
            <div
                class="block_list_line"
                v-for="elem in local_data.report.wheel_pairs"
            >
                <div>{{ elem.name }}</div>
                <div>{{ elem.count }}</div>
            </div>
            <div>Расход резцов</div>
            <div class="block_list_line">
                <div>Чашка</div>
                <div>
                    <div>{{ local_data.report.cup }}</div>
                </div>
            </div>
            <div class="block_list_line">
                <div>Тангец</div>
                <div>
                    <div>{{ local_data.report.tangen }}</div>
                </div>
            </div>
            <div>Прочая занятость</div>
            <div
                class="block_list_line"
                v-for="elem in local_data.report.activities"
            >
                <div>{{ elem.name }}</div>
            </div>
        </div>

        <div class="block">
            <div class="block_header">Комментарий</div>
            <div class="comment">{{ local_data.report.comment }}</div>
        </div>
        <div class="buttons">
            <div></div>
            <button @click="returnToCabinet">Назад</button>
        </div>
    </div>
</template>
<style scoped>
.page {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.block {
    display: flex;
    flex-direction: column;
    gap: 15px;

    border-style: solid;
    border-radius: 10px;
    border-color: var(--color-input);
    border-width: 1px;

    padding-bottom: 20px;
    padding-left: 15px;
    padding-right: 15px;
    padding-top: 30px;
}
.block_header {
    margin-bottom: 15px;
    font-size: 20px;
    font-weight: bold;
}
.block_list_line {
    display: grid;
    grid-template-columns: 50% 50%;
    align-items: center;
    margin-left: 10px;
}

.comment {
    margin-left: 10px;
    margin-bottom: 10px;
    width: 75%;
    height: 80px;
    resize: none;
}

.buttons {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap: 10px;
}
</style>
