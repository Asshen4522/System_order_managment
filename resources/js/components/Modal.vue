<script setup>
const props = defineProps({
    type: null,
    text: "",
});
const emit = defineEmits(["close", "yes", "no"]);
</script>
<template>
    <div class="modal-mask">
        <div class="modal-wrapper">
            <div class="modal-container">
                <div class="modal-text">{{ props.text }}</div>
                <button v-if="props.type == 'confirm'" @click="emit('close')">
                    OK
                </button>
            </div>
        </div>
    </div>
</template>
<style scoped>
.modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: table;
    transition: opacity 0.3s ease;
}

.modal-wrapper {
    display: table-cell;
    vertical-align: middle;
}

.modal-container {
    width: 200px;
    margin: 0px auto;
    padding: 20px 30px;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
    transition: all 0.3s ease;
}

.modal-default-button {
    float: right;
}

.modal-text {
    margin-bottom: 20px;
}
</style>
