<script setup>
import { reactive } from "vue";
import customInput from "../../components/Input.vue";


const emit = defineEmits(["openPage"]);
const props = defineProps({
    nowDate: null,
    orderId: null,
});
const local_data = reactive({
    report: {
        tangen: 0,
        cup: 0,
        wheel_pairs:[],
        costs: [],
        activities: [],
        comment: null,
        date: null,
        id:props.orderId,
    },
    orderStatus: null,

    newActivity: null,
    newCost: {
        id: null,
        price: null,
    },


    errorCost: false,


    activities: [],
    costs: [],
    inner_costs: [],

    createNewCost:"",
    errorNewCost : false,

    createNewActivity:"",
    errorNewActivity : false,
});

function updateStatus() {
    let statusId = 2;
    let nowDate = "";
    if (local_data.wheel_pair_left == local_data.report.wheel_pairs) {
        statusId = 3
    }
    const statusAndId = {id : props.orderId, status : statusId, date : props.nowDate}
    fetch("/Update_status", {
        method: "POST",
        body: JSON.stringify(statusAndId),
        headers: {
            "X-CSRF-TOKEN":
                document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            if (response) {
                returnToCabinet();
            }
        });
}

function returnToCabinet() {
    emit("openPage", 'order-show');
}

function createCost() {
    local_data.errorNewCost = local_data.createNewCost == "";
    if (!local_data.errorNewCost) {
        fetch("/Create_cost", {
            method: "POST",
            body: JSON.stringify({
                name: local_data.createNewCost,
            }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                if (response == false) {
                    local_data.errorNewCost = true;
                } else {
                    local_data.costs.push({
                        id: response,
                        name: local_data.createNewCost,
                    });
                    local_data.createNewCost = "";
                }
            });
    }
}
function createActivity() {
    local_data.errorNewActivity = local_data.createNewActivity == "";
    if (!local_data.errorNewActivity) {
        fetch("/Create_activity", {
            method: "POST",
            body: JSON.stringify({
                name: local_data.createNewActivity,
            }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                if (response == false) {
                    local_data.errorNewActivity = true;
                } else {
                    local_data.activities.push({
                        id: response,
                        name: local_data.createNewActivity,
                    });
                    local_data.createNewActivity = "";
                }
            });
    }
}

function getActivities() {
    local_data.activities = [];
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
    local_data.costs = [];
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
function getWheelPair() {
    fetch("/Get_wheel_pair_left", {
        method: "POST",
        body: JSON.stringify({ id: props.orderId }),
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            local_data.report.wheel_pairs = response;
        });
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
    if (local_data.report.wheel_pairs != local_data.wheel_pair_left) {
        local_data.report.wheel_pairs += 0.5
    }
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
        .then((response) => {
            if (response) {
                updateStatus();
            }
        });
}


getCosts();
getActivities();
getOrderCosts();
getWheelPair();
</script>
<template>
    <div class="page">
        <div class="block_header">Дневной отчет от {{props.nowDate}}</div>
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

            <div class="block_list_line" v-for="elem,index in local_data.report.costs">
                <div>{{ elem.name }}</div>
                <div>{{ elem.price }} р.</div>
                <div class="smallButtons" @click="deleteCost(index)">&#215</div>
            </div>
            <div class="block_header">Добавить траты</div>
            <div class="block_add_line">
                <select v-model="local_data.newCost.id" class="block_add_line_elem">
                    <option
                        v-for="option in local_data.costs"
                        :value="option.id"
                    >
                        {{ option.name }}
                    </option>
                </select>
                <customInput class="block_add_line_elem"
                    v-model="local_data.newCost.price"
                    inputname="Сумма"
                    typeIn="text"
                    :ifError="local_data.errorCost"
                />
                <button @click="addCost">Добавить</button>
            </div>
            <div class="block_header">Добавить вид трат</div>
            <div class="block_add_line">
                <customInput
                    v-model="local_data.createNewCost"
                    inputname="Затрата"
                    typeIn="text"
                    :ifError="local_data.errorNewCost"
                />
                <button @click="createCost">Добавить</button>
            </div>
        </div>
       
        <div class="block">
            <div class="block_header">Занятость</div>
            <div class="display_line">
                <div class="display_line_first">Обточка КП</div>
                <div class="wheelPairs">
                    <div class="smallButtons" @click="lessPairs()">-</div>
                    <div>{{ local_data.report.wheel_pairs }}</div>
                    <div class="smallButtons" @click="morePairs()">+</div>
                </div>
                
            </div>
            <div class="display_line">
                <div class="display_line_first">Израсходовано чашечных резцов</div>
                <div class="wheelPairs">
                    <div class="smallButtons" @click="lessCup()">-</div>
                    <div>{{ local_data.report.cup }}</div>
                    <div class="smallButtons" @click="moreCup()">+</div>
                </div>
                
            </div>
            <div class="display_line">
                <div class="display_line_first">Израсходовано тангецных резцов</div>
                <div class="wheelPairs">
                    <div class="smallButtons" @click="lessTangen()">-</div>
                    <div>{{ local_data.report.tangen }}</div>
                    <div class="smallButtons" @click="moreTangen()">+</div>
                </div>
                
            </div>

            <div class="display_line" v-for="elem,index in local_data.report.activities">
                <div class="display_line_first">{{ elem.name }}</div>
                <div class="smallButtons" @click="deleteActivity(index)">&#215</div>
            </div>



            <div class="display_line_act">Добавить занятия</div>
            <div class="addCost">
                <select class="addCostId" v-model="local_data.newActivity">
                    
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
            <div class="block_header">Введите название нового занятия, только если его нет в списке выше!</div>
            <div class="display_line">
                <customInput
                    v-model="local_data.createNewActivity"
                    inputname="Активность"
                    typeIn="text"
                    :ifError="local_data.errorNewActivity"
                />
                <button @click="createActivity" class="addCostButton">Добавить</button>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Комментарий</div>
            <textarea class="comment" v-model="local_data.report.comment"> </textarea>
        </div>
        <div class="buttons">
            <button @click="SendData">Создать отчет</button>
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
    display:flex;
    flex-direction:column;
    gap: 15px;

    border-style: solid;
    border-radius: 10px;
    border-color: var(--color-input);
    border-width:1px;

    padding-bottom: 20px;
    padding-left: 15px;
    padding-right: 15px;
    padding-top: 30px;
}
.block_header{
    margin-bottom: 15px;
}
.block_list_line{ 
    display:grid;
    grid-template-columns: 50% 40% 10%;
    align-items:center;
    margin-left: 10px;
    }
.smallButtons{
    font-size:30px;
}
.smallButtons:hover{
    cursor: pointer;
}
.block_add_line{
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    gap:10px;
}

select{
    min-width: 170px;
}
/* .smallButtons{
    font-size:30px;
    align-self:right;
}

.smallButtons:hover{
    cursor: pointer;
}

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
    align-items:center;
    margin-left: 10px;
    margin-bottom: 10px;
}
.display_line_act{
    margin-top: 30px;
    margin-left: 10px;
    margin-bottom: 15px;
}
.display_line_first {
    width: 50%;
}
.display_line_second {
    width: 30%;
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
    align-items: center;
}

.buttons {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
} */


</style>
