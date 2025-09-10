<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    maxRooms: Number,
});

const form = useForm({
    room: '',
});
</script>

<template>
    <div>
        <h1 class="text-xl font-bold">Create Surgery</h1>
        <form @submit.prevent="form.post(route('surgeries.store'))" class="mt-4">
            <div>
                <label for="room" class="block font-medium">Room (1 - {{ maxRooms }})</label>
                <input id="room" type="number" min="1" :max="maxRooms" v-model="form.room" class="mt-1 border rounded w-full" />
                <div v-if="form.errors.room" class="text-red-500 text-sm mt-1">{{ form.errors.room }}</div>
            </div>
            <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded" :disabled="form.processing">
                Save
            </button>
        </form>
    </div>
</template>
