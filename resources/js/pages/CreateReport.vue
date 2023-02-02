<script setup>
import { reactive } from "vue";
import customInput from "../components/Input.vue";


const emit = defineEmits(["openPage"]);
const props = defineProps({
    orderId: null,
});
const local_data = reactive({
    report: {
        wheel_pairs: 0,
        tangen: 0,
        cup: 0,
        costs: [],
        activities: [],
        comment: null,
        date: null,
        id:props.orderId,
    },

    newActivity: null,
    newCost: {
        id: null,
        price: null,
    },


    errorCost: false,


    activities: [],
    costs: [],
    inner_costs: [],
});

function returnToCabinet() {
    emit("openPage", 3);
}

function getActivities() {
    fetch("/Get_activities", {
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
    fetch("/Get_costs", {
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
    fetch("/Get_costs_order", {
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
                local_data.inner_costs.push(element);
            });
        });
}
function getDate() {
    let date = new Date()
    local_data.report.date = String(date.getFullYear())+'-'+String(date.getMonth()+1)+'-'+String(date.getDate())
}

function valCost() {
    local_data.errorCost = !/^\d+$/.test(local_data.newCost.price);
    return !local_data.errorCost && local_data.newCost.id
}
function addCost() {
    if (valCost()) {
        local_data.report.costs.push({
        id: local_data.newCost.id,
        name: local_data.costs[local_data.newCost.id - 1].name,
        price: local_data.newCost.price,
    });
    local_data.newCost.id = null;
    local_data.newCost.price = null;
    }
    
}
function deleteCost(params) {
    local_data.report.costs.splice([params],1);
}

function addActivity() {
    if (local_data.newActivity) {
        local_data.report.activities.push({
        id: local_data.newActivity,
        name: local_data.activities[local_data.newActivity - 1].name,
    });
    local_data.newActivity = null;
    }
    
}
function deleteActivity(params) {
    local_data.report.activities.splice([params],1)
}

function lessPairs() {
    if (local_data.report.wheel_pairs != 0) {
        local_data.report.wheel_pairs-=0.5
    }
}
function morePairs() {
    local_data.report.wheel_pairs+=0.5
}

function lessCup() {
    if (local_data.report.cup != 0) {
        local_data.report.cup-=1
    }
}
function moreCup() {
    local_data.report.cup+=1
}

function lessTangen() {
    if (local_data.report.tangen != 0) {
        local_data.report.tangen-=1
    }
}
function moreTangen() {
    local_data.report.tangen+=1
}

function SendData() {
    fetch("/Create_report", {
        method: "POST",
        body: JSON.stringify(local_data.report),
        headers: {
            "X-CSRF-TOKEN":
                document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => console.log(response));
}


getCosts();
getActivities();
getOrderCosts();
getDate();
</script>
<template>
    <div>
        <div>Дневной отчет от {{local_data.report.date}}</div>
        <div class="block">
            <div class="block_header">Финансовые затраты</div>
            <div class="display_line">
                <div class="display_line_first">Суточные</div>
                <div>{{ local_data.inner_costs[0]?.daily_cost }} р.</div>
            </div>
            <div class="display_line">
                <div class="display_line_first">Квартира</div>
                <div>{{ local_data.inner_costs[0]?.rent }} р.</div>
            </div>

            <div class="display_line" v-for="elem,index in local_data.report.costs">
                <div class="display_line_first">{{ elem.name }}</div>
                <div>{{ elem.price }} р.</div>
                <div @click="deleteCost(index)">&#215</div>
            </div>



            <div class="display_line_act">Добавить траты</div>
            <div class="addCost">
                <select class="addCostId" v-model="local_data.newCost.id">
                    <option disabled value="">Траты</option>
                    <option
                        v-for="option in local_data.costs"
                        :value="option.id"
                    >
                        {{ option.name }}
                    </option>
                </select>
                <customInput
                    v-model="local_data.newCost.price"
                    inputname="Сумма"
                    typeIn="text"
                    :ifError="local_data.errorCost"
                />
                <button @click="addCost" class="addCostButton">Добавить</button>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Занятость</div>
            <div class="display_line">
                <div class="display_line_first">Обточка КП</div>
                <div class="wheelPairs">
                    <div @click="lessPairs()">-</div>
                    <div>{{ local_data.report.wheel_pairs }}</div>
                    <div @click="morePairs()">+</div>
                </div>
                
            </div>
            <div class="display_line">
                <div class="display_line_first">Израсходовано чашечных резцов</div>
                <div class="wheelPairs">
                    <div @click="lessCup()">-</div>
                    <div>{{ local_data.report.cup }}</div>
                    <div @click="moreCup()">+</div>
                </div>
                
            </div>
            <div class="display_line">
                <div class="display_line_first">Израсходовано тангецных резцов</div>
                <div class="wheelPairs">
                    <div @click="lessTangen()">-</div>
                    <div>{{ local_data.report.tangen }}</div>
                    <div @click="moreTangen()">+</div>
                </div>
                
            </div>

            <div class="display_line" v-for="elem,index in local_data.report.activities">
                <div class="display_line_first">{{ elem.name }}</div>
                <div @click="deleteActivity(index)">&#215</div>
            </div>



            <div class="display_line_act">Добавить занятия</div>
            <div class="addCost">
                <select class="addCostId" v-model="local_data.newActivity">
                    <option disabled value="">Активность</option>
                    <option
                        v-for="option in local_data.activities"
                        :value="option.id"
                    >
                        {{ option.name }}
                    </option>
                </select>
                <button @click="addActivity" class="addCostButton">Добавить</button>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Комментарий</div>
            <textarea class="comment" v-model="local_data.report.comment"> </textarea>
        </div>
        <div class="buttons">
            <button @click="SendData">Создать отчет</button>
        <button @click="returnToCabinet">Вернуться в кабинет</button>
        </div>
        
    </div>
</template>
<style scoped>
.block {
    border-style: solid;
    border-radius: 10px;
    margin-bottom: 20px;
    margin-top: 20px;
}
.block_header {
    margin-bottom: 15px;
    margin-top: 5px;
    margin-left: 10px;
}

.comment{
    margin-left: 10px;
    margin-bottom: 10px;
    width:75%;
    height:80px;
    resize : none;
}

.display_line {
    display: flex;
    flex-direction: row;
    margin-left: 10px;
    margin-bottom: 10px;
}
.display_line_act{
    margin-top: 30px;
    margin-left: 10px;
}
.display_line_first {
    width: 50%;
}

.addCost {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
.addCostId {
    width: 50%;
    margin: 5px;
    height: 75%;
    align-self: center;
}
.addCostButton {
    margin: 10px;
}

.wheelPairs{
    display:flex;
    flex-direction:row;
    justify-content:space-around;
    width:25%;
}

.buttons {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}


</style>
