<script setup>
import { reactive } from "vue";

const props = defineProps({
    orderId: null,
});
const local_data = reactive({
    order: {},
});

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
            console.log(response);
            local_data.order.id = props.orderId;
            local_data.order.city = response[0].city;
            local_data.order.model = response[0].model;
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
                local_data.order.wheel_pairLeft = response[0].wheel_pairs;
            } else {
                var spentTangen = 0;
                var spentCup = 0;
                var spentWheelPairs = 0;
                response[1].forEach((elem) => {
                    spentTangen += elem.tangen;
                    spentCup += elem.cup;
                    spentWheelPairs += elem.wheel_pairs;
                });
                local_data.order.tangenLeft = response[0].tangen - spentTangen;
                local_data.order.cupLeft = response[0].cup - spentCup;
                local_data.order.wheel_pairLeft =
                    response[0].wheel_pairs - spentWheelPairs;
            }
        });
}

getDisplayOrder();
</script>
<template>
    <div></div>
</template>
<style scoped></style>
