<script setup>
import customInput from "../../components/Input.vue";
import { reactive, ref } from "vue";

const props = defineProps({
    nowDate: null
})

const local_data = reactive({
    order: {
        firm: null,
        city: "",
        locomotive: [],

        budget: null,
        daily_cost: null,
        housing: "",
        rent: null,
        payment: null,

        tangen: null,
        cup: null,

        contact: {
            id: null,
            fio: "",
            phone: null,
        },

        created_at: props.nowDate,

        executor: null,
    },

    locomotives: [],
    newLocomotive: {
        id: null,
        count: null,
        wheel_pairs:null,
    },
    errorLocomotive: false,
    errorWheelPairs: false,

    newModel: {
        name: null,
        wheel_pairs: null
    },
    errorNewModel: false,

    error_list: {

        errorBudget: false,
        errorRent: false,
        errorDailyCost: false,
        errorPayment:false,

        errorTangen: false,
        errorCup: false,

        errorFio: false,
        errorPhone: false,

        errorDate: false,
    },
    createError: false,

    

    contacts: [],

    executors: [],

    optionContact: true,
});

const emit = defineEmits(["openPage", "modal"]);

function returnToCabinet(index) {
    emit("openPage", index);
}

function getLocomotives() {
    fetch("/Get_locomotives", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.locomotives.push(element);
            });
        });
}
function getContacts() {
    fetch("/Get_contacts", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.contacts.push(element);
            });
        });
}
function getExecutors(params) {
    fetch("/Get_executors", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((response) => {
            response.forEach((element) => {
                local_data.executors.push(element);
            });
        });
}

function createModel() {
    if (local_data.newModel.name != null && /^\d+$/.test(local_data.newModel.wheel_pairs)) {
        fetch("/Create_model", {
            method: "POST",
            body: JSON.stringify({
                name: local_data.newModel.name,
                wheel_pairs: local_data.newModel.wheel_pairs
            }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                if (response == false) {
                    local_data.errorNewModel = true;
                } else {
                    local_data.errorNewModel = false;
                     let obj = {
                        id: response,
                        model:local_data.newModel.name,
                        wheel_pairs: local_data.newModel.wheel_pairs
                     };
                     local_data.locomotives.push(obj);
                     local_data.newModel.name = null;
                        local_data.newModel.wheel_pairs = null;
                }
            });
    }else{
        local_data.errorNewModel = true;
    }
}

function calcWheels() {
    local_data.newLocomotive.wheel_pairs = local_data.newLocomotive.count * local_data.locomotives[local_data.newLocomotive.id-1].wheel_pairs
}
function addLocomotive() {
    if (/^\d+$/.test(local_data.newLocomotive.count) && /^\d+$/.test(local_data.newLocomotive.wheel_pairs) && local_data.newLocomotive.id != null ) {
        local_data.order.locomotive.push({
            id: local_data.newLocomotive.id,
            name: local_data.locomotives[local_data.newLocomotive.id - 1].model,
            count: local_data.newLocomotive.count,
            wheel_pairs: local_data.newLocomotive.wheel_pairs,
        });
        local_data.newLocomotive.id = null;
        local_data.newLocomotive.count = null;
        local_data.newLocomotive.wheel_pairs = null;
        local_data.errorLocomotive = false;
        local_data.errorWheelPairs = false;
    }else{
        local_data.errorLocomotive = true;
        local_data.errorWheelPairs = true;
    }
}
function deleteLocomotive(params) {
    local_data.order.locomotive.splice([params],1);
}

function toggleOptionContact() {
    local_data.optionContact = !local_data.optionContact;
    local_data.order.contact.id = null;
    local_data.order.contact.fio = null;
    local_data.order.contact.phone = null;
}

function Validate() {
    local_data.error_list.errorBudget =
        !/^\d+$/.test(local_data.order.budget) &&
        local_data.order.budget != null;
    local_data.error_list.errorRent =
        !/^\d+$/.test(local_data.order.rent) && local_data.order.rent != null;
    local_data.error_list.errorDailyCost =
        !/^\d+$/.test(local_data.order.daily_cost) &&
        local_data.order.daily_cost != null;
    local_data.error_list.errorPayment =
        !/^\d+$/.test(local_data.order.payment) &&
        local_data.order.payment != null;

    local_data.error_list.errorTangen =
        !/^\d+$/.test(local_data.order.tangen) &&
        local_data.order.tangen != null;
    local_data.error_list.errorCup =
        !/^\d+$/.test(local_data.order.cup) && local_data.order.cup != null;

    local_data.error_list.errorFio =
        !local_data.optionContact && local_data.order.contact.fio == "";
    local_data.error_list.errorPhone =
        !local_data.optionContact &&
        !/^(8|\+7)(\d){10}$/.test(local_data.order.contact.phone);

    if (
        !local_data.error_list.errorBudget &&
        !local_data.error_list.errorRent &&
        !local_data.error_list.errorDailyCost &&
        !local_data.error_list.errorTangen &&
        !local_data.error_list.errorCup &&
        !local_data.error_list.errorFio &&
        !local_data.error_list.errorPhone &&
        !local_data.error_list.errorDate
    ) {
        return true;
    } else {
        return false;
    }
}
function contact() {
    if (!local_data.optionContact) {
        return fetch("/Create_contact", {
            method: "POST",
            body: JSON.stringify({
                fio: local_data.order.contact.fio,
                phone: local_data.order.contact.phone,
            }),
            headers: {
                "X-CSRF-TOKEN": document.querySelector('[name="_token"]').value,
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((response) => {
                return response;
            });
    } else {
        return Promise.resolve(local_data.order.contact.id);
    }
}
function SendData() {
    if (Validate()) {
        contact().then((response) => {
            const datuum = {
                firm: local_data.order.firm,
                city: local_data.order.city,
                locomotive: [],

                budget: local_data.order.budget,
                daily_cost: local_data.order.daily_cost,
                housing: local_data.order.housing,
                rent: local_data.order.rent,
                payment: local_data.order.payment,

                tangen: local_data.order.tangen,
                cup: local_data.order.cup,

                contact: response,

                created_at: local_data.order.created_at,

                executor: local_data.order.executor,
            };
            local_data.order.locomotive.forEach(element => {
                let obj ={
                    id: element.id,
                    name: element.name,
                    count: element.count,
                    wheel_pairs: element.wheel_pairs}
                datuum.locomotive.push(obj)

            });
            console.log(datuum);
            fetch("/Create_order", {
                method: "POST",
                body: JSON.stringify(datuum),
                headers: {
                    "X-CSRF-TOKEN":
                        document.querySelector('[name="_token"]').value,
                    "Content-Type": "application/json",
                },
            })
                .then((response) => response.json())
                .then((response) => {
                    if (response == true) {
                        returnToCabinet("order-main");
                    }
                });
        });
    } else {
        emit("modal", [
            "confirm",
            "Не все поля заполнены верно.",
        ]);
    }
}

getLocomotives();
getContacts();
getExecutors();
</script>
<template>
    <div class="page">
        <div>Создание</div>
        <div class="block">
            <div class="block_header">Место жительства</div>
            <customInput
                v-model="local_data.order.firm"
                inputname="Фирма"
                typeIn="text"
                :ifError="false"
            />
            <customInput
                v-model="local_data.order.city"
                inputname="Город"
                typeIn="text"
                :ifError="local_data.error_list.errorCity"
            />
            <customInput
                v-model="local_data.order.housing"
                inputname="Адрес жилья"
                typeIn="text"
                :ifError="false"
            />
        </div>
        <div class="block"> 
            <div class="block_header">Список локомотивов</div>
            <div class="block_list_line">
                <div >Название</div>
                <div >Количество </div>
                <div > КП</div>
                <div ></div>
            </div>
            <div class="block_list_line" v-for="elem,index in local_data.order.locomotive">
                <div >{{ elem.name }}</div>
                <div >{{ elem.count }} </div>
                <div > {{ elem.wheel_pairs }}</div>
                <div class="smallButtons" @click="deleteLocomotive(index)">&#215</div>
            </div>
            <div class="block_header">Добавить Локомотивы</div>
            <div class="block_add_line">
                <select  v-model="local_data.newLocomotive.id">
                    <option
                        v-for="option in local_data.locomotives"
                        :value="option.id"
                    >
                        {{ option.model }}
                    </option>
                </select>

                <customInput  @change="calcWheels"
                    v-model="local_data.newLocomotive.count"
                    inputname="Количество локомотивов"
                    typeIn="text"
                    :ifError="local_data.errorLocomotive"
                />
                <customInput 
                    v-model="local_data.newLocomotive.wheel_pairs"
                    inputname="Колесные пары"
                    typeIn="text"
                    :ifError="local_data.errorWheelPairs"
                />
                <button  @click="addLocomotive">Добавить</button>
            </div>
            <div class="block_header">Добавить Модель</div>
            <div class="block_line">
                 <customInput  
                    v-model="local_data.newModel.name"
                    inputname="Название"
                    typeIn="text"
                    :ifError="local_data.errorNewModel"
                />
                <customInput  
                    v-model="local_data.newModel.wheel_pairs"
                    inputname="Количество КП"
                    typeIn="text"
                    :ifError="local_data.errorNewModel"
                />
                <button   @click="createModel">Добавить</button>
            </div>
        </div>
        <div class="block">
            <div class="block_header">Финансы</div>
            <customInput
                v-model="local_data.order.budget"
                inputname="Бюджет"
                typeIn="text"
                :ifError="local_data.error_list.errorBudget"
            />
            <customInput
                v-model="local_data.order.daily_cost"
                inputname="Суточные"
                typeIn="text"
                :ifError="local_data.error_list.errorDailyCost"
            />
            <customInput
                v-model="local_data.order.rent"
                inputname="Рента"
                typeIn="text"
                :ifError="local_data.error_list.errorRent"
            />
            <customInput
                v-model="local_data.order.payment"
                inputname="Оплата"
                typeIn="text"
                :ifError="local_data.error_list.errorPayment"
            />
        </div>
        <div class="block">
            <div class="block_header">Резцы</div>
            <customInput
                    v-model="local_data.order.tangen"
                    inputname="Тангец"
                    typeIn="text"
                    :ifError="local_data.error_list.errorTangen"
                />
                <customInput
                    v-model="local_data.order.cup"
                    inputname="Чашка"
                    typeIn="text"
                    :ifError="local_data.error_list.errorCup"
                />
        </div>
        <div class="block">
            <div class="block_header">Контактное лицо</div>
            <div>
                <div class="block_line">
                    <button @click="toggleOptionContact()">
                        Выбрать /Добавить
                    </button>
                </div>
                <div class="block_line">
                    <div v-show="local_data.optionContact == true" >
                        <select v-model="local_data.order.contact.id">
                            <option
                                v-for="option in local_data.contacts"
                                :value="option.id"
                            >
                                {{ option.phone + " " + option.fio }}
                            </option>
                        </select>
                    </div>
                    <div v-show="local_data.optionContact == false">
                        <div class="block_line">
                            <customInput
                                v-model="local_data.order.contact.phone"
                                inputname="Телефон"
                                typeIn="text"
                                :ifError="local_data.error_list.errorPhone"
                            />
                            <customInput
                                v-model="local_data.order.contact.fio"
                                inputname="Фио"
                                typeIn="text"
                                :ifError="local_data.error_list.errorFio"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="block_header">Исполнитель</div>
            <div class="block_line">
                <select v-model="local_data.order.executor">
                    <option
                        v-for="option in local_data.executors"
                        :value="option.id"
                    >
                        {{ option.name + " " + option.surname }}
                    </option>
                </select>
            </div>
        </div>
        <customInput
                v-model="local_data.order.created_at"
                inputname="Дата начала"
                typeIn="date"
                :ifError="local_data.error_list.errorDate"
            />
        
            

        <div class="buttons">
            <button @click="SendData">Создать</button>
            <button @click="returnToCabinet('order-main')">Назад</button>
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
    grid-template-columns: 50% 20% 20% 10%;
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

.block_line{
    display:flex;
    flex-direction:row;
    align-items:center;
    justify-content:center;
    gap:10px;
    margin-bottom: 15px;
}

.buttons {
    gap: 10px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}


</style>
