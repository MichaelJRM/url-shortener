<script setup>
import {onMounted, ref} from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

</script>

<template>
    <input
        ref="input"
        v-model="model"
    />
</template>

<style scoped>
input {
    @apply border-zinc-300 min-w-4 dark:border-zinc-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-white text-zinc-950 dark:bg-black dark:text-white autofill:text-white;
}
</style>
