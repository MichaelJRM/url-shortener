<script setup>

defineProps({
    content: {
        type: String
    },
    isBeingCopied: {
        type: Boolean
    }
});

</script>

<template>
    <div
        class="short-link">
        {{ content }}
        <div>
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-80"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-80"
                mode="out-in"
            >
                <div v-if="isBeingCopied" class="w-max">Short url copied!</div>
                <svg v-else>
                    <use href="assets/svg/copy.svg#copy"></use>
                </svg>
            </Transition>
        </div>
    </div>
</template>

<style scoped>
.short-link {
    @apply flex justify-between gap-4 rounded-lg break-all bg-white text-zinc-950 dark:text-white p-4 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20];
}

.short-link svg {
    @apply size-5 sm:size-6 fill-black/50 hover:fill-black dark:fill-white/50 dark:hover:fill-white transition duration-300 cursor-pointer;
}
</style>
