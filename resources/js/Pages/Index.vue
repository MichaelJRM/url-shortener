<script setup>
import {router, useForm} from '@inertiajs/vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import {onMounted, onUnmounted, ref} from "vue";
import ShortLink from "@/Pages/Partials/ShortLink.vue";

const MAX_SHORT_LINK_HISTORY_SIZE = 5;

const props = defineProps({
    shortUrl: {
        type: String
    },
});

const form = useForm({
    urlToShort: '',
});

const linksBeingCopiedById = ref({});
const shortLinksHistory = ref([]);

onMounted(() => {
    loadShortLinksHistory();
})

onUnmounted(
    router.on('success', (event) => {
        updateShortLinksHistory();
    })
)

function submit() {
    form.transform((data) => {
        if (!data.urlToShort.startsWith('http')) {
            data.urlToShort = `https://${data.urlToShort}`;
        }
        return data;
    })
        .post(route('short-urls.store'), {
            onFinish: () => form.reset('urlToShort'),
        });
}

function onShortLinkCopyButtonPressed(id, value) {
    navigator.clipboard.writeText(value)
        .then(() => {
            linksBeingCopiedById.value[id] = true;
            setTimeout(() => {
                linksBeingCopiedById.value[id] = false;
            }, 2000);
        })
        .catch((err) => {
            console.error("Error while copying shortLink: ", err);
        });
}

function loadShortLinksHistory() {
    const history = JSON.parse(localStorage.getItem('shortLinksHistory') ?? '[]');
    shortLinksHistory.value = history.slice(-(MAX_SHORT_LINK_HISTORY_SIZE + 1));
}

function updateShortLinksHistory() {
    const isSameUrl = shortLinksHistory.value.length !== 0 && shortLinksHistory.value[shortLinksHistory.value.length - 1].shortUrl === props.shortUrl;
    const canUpdate =
        props.shortUrl
        && form.urlToShort
        && !isSameUrl;

    if (!canUpdate) return;

    shortLinksHistory.value = [...shortLinksHistory.value, {
        url: form.urlToShort,
        shortUrl: props.shortUrl
    }].slice(-(MAX_SHORT_LINK_HISTORY_SIZE + 1));
    localStorage.setItem('shortLinksHistory', JSON.stringify(shortLinksHistory.value));
}
</script>

<template>
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white">
        <div
            class="relative min-h-screen flex flex-col items-center justify-center"
        >
            <div class="relative w-full max-w-3xl px-6 lg:max-w-7xl">
                <main
                    class="my-6 grid gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
                    <form @submit.prevent="submit">
                        <div class="text-black/50 ">
                            <InputLabel for="url" value="Paste your URL"/>

                            <div class="flex flex-col md:flex-row flex-wrap gap-4 mt-4">
                                <TextInput
                                    id="url"
                                    v-model="form.urlToShort"
                                    autocomplete="off"
                                    autofocus
                                    class="block flex-1"
                                    required
                                />

                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="text-nowrap w-full justify-center md:w-fit">
                                    Get your short url
                                </PrimaryButton>
                            </div>

                            <InputError :message="form.errors.urlToShort" class="mt-2"/>
                        </div>
                    </form>

                    <ShortLink
                        v-if="props.shortUrl"
                        :key="props.shortUrl"
                        :content="props.shortUrl"
                        :is-being-copied="linksBeingCopiedById[props.shortUrl]"
                        @click="() => onShortLinkCopyButtonPressed(props.shortUrl, props.shortUrl)"
                    />

                    <template v-if="shortLinksHistory.length > 1">
                        <hr class="border-zinc-700 my-2">
                        <p>Recent</p>
                    </template>

                    <ShortLink
                        v-for="(shortLink, index) in shortLinksHistory.slice(0, shortLinksHistory.length - 1).reverse()"
                        :key="`${shortLink.shortUrl}${index}`"
                        :content="`${shortLink.url} | ${shortLink.shortUrl}`"
                        :is-being-copied="linksBeingCopiedById[`${shortLink.shortUrl}${index}`]"
                        @click="() => onShortLinkCopyButtonPressed(`${shortLink.shortUrl}${index}`, shortLink.shortUrl)"
                    />
                </main>
            </div>
        </div>
    </div>
</template>
