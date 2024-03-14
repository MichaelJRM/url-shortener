<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label>
        <span v-if="value">{{ value }}</span>
        <span v-else><slot/></span>
    </label>
</template>


<style scoped>
label {
    @apply block text-xl font-semibold text-black dark:text-white;
}
</style>

