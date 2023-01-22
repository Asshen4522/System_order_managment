<script setup>
import customInput from "../components/Input.vue";
import { reactive, ref, computed } from "vue";

const local_data = reactive({
    order: {
        city: "",
        locomotive: null,
        wheel_pairs: null,

        budget: null,
        housing: "",

        tangen: null,
        cup: null,

        contact: {
            id: null,
            fio: "",
            phone: null,
        },

        created_at: "2023-01-01",

        executor: null,
    },

    error_list: {
        errorCity: false,
        errorWheelPairs: false,
        errorHousing: false,
        errorBudget: false,
        errorTangen: false,
        errorCup: false,
        errorFio: false,
        errorPhone: false,
        errorDate: false,
    },

    locomotives: [],

    contacts: [],

    executors: [],

    optionContact: "Существующий",
});

const wheel_pairs_text = computed(() => {
    if (local_data.order.locomotive == null) {
        return "Количество колесных пар";
    } else {
        return (
            "Количество колесных пар: " +
            String(
                local_data.locomotives[local_data.order.locomotive - 1]
                    .wheel_pairs
            )
        );
    }
});

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

function pickOptionContact(param) {
    local_data.optionContact = param;
}

function Validate() {}
function SendData() {}

getLocomotives();
getContacts();
getExecutors();
</script>
<template>
    <div class="page">
        <div></div>
        <div>
            <customInput
                v-model="local_data.order.city"
                inputname="Город"
                typeIn="text"
                :ifError="local_data.error_list.errorCity"
            />
        </div>

        <div class="line">
            <div>
                <div>Модель локомотива</div>
                <select v-model="local_data.order.locomotive">
                    <option disabled value="">Локомотив</option>
                    <option
                        v-for="option in local_data.locomotives"
                        :value="option.id"
                    >
                        {{ option.model }}
                    </option>
                </select>
            </div>
            <div>
                <customInput
                    v-model="local_data.order.wheel_pairs"
                    :inputname="wheel_pairs_text"
                    typeIn="text"
                    :ifError="local_data.error_list.errorWheelPairs"
                />
            </div>
        </div>

        <div class="line">
            <customInput
                v-model="local_data.order.budget"
                inputname="Бюджет"
                typeIn="text"
                :ifError="local_data.error_list.errorBudget"
            />
            <customInput
                v-model="local_data.order.housing"
                inputname="Рента"
                typeIn="text"
                :ifError="local_data.error_list.errorHousing"
            />
        </div>

        <div>
            Резцы
            <div class="line">
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
        </div>

        <div>
            Контактное лицо
            <div>
                <div class="contact_buttons">
                    <button
                        @click="pickOptionContact('Существующий')"
                        class="button-selector"
                        :class="{
                            active: local_data.optionContact == 'Существующий',
                        }"
                    >
                        Выбрать из существующих
                    </button>
                    <button
                        @click="pickOptionContact('Новый')"
                        class="button-selector"
                        :class="{ active: local_data.optionContact == 'Новый' }"
                    >
                        Создать нового
                    </button>
                </div>
                <div class="contact_input">
                    <div v-show="local_data.optionContact == 'Существующий'">
                        <select v-model="local_data.order.contact.id">
                            <option disabled value="">Контакт</option>
                            <option
                                v-for="option in local_data.contacts"
                                :value="option.id"
                            >
                                {{ option.phone + " " + option.fio }}
                            </option>
                        </select>
                    </div>
                    <div v-show="local_data.optionContact == 'Новый'">
                        <div class="line">
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
        </div>

        <div>
            <customInput
                v-model="local_data.order.created_at"
                inputname="Дата начала"
                typeIn="date"
                :ifError="local_data.error_list.errorDate"
            />
        </div>

        <div>
            Исполнитель
            <select v-model="local_data.order.executor">
                <option disabled value="">Персона</option>
                <option
                    v-for="option in local_data.executors"
                    :value="option.id"
                >
                    {{ option.name + " " + option.surname }}
                </option>
            </select>
        </div>
    </div>
</template>
<style scoped>
.page {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.line {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.contact_buttons {
    display: flex;
    flex-direction: row;
    gap: 50px;
    justify-content: space-around;
}

.contact_input {
    margin-top: 30px;
}

.button-selector {
    color: var(--color-object);
    background-color: white;
    border-color: var(--color-object);
    padding: 2px;
    width: 100%;
}
.button-selector:hover {
    color: white;
}
.active {
    color: white;
    background-color: var(--color-object);
}
</style>
