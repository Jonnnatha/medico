<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    maxRooms: Number,
});

const form = useForm({
    maxRooms: props.maxRooms,
});
</script>

<template>
    <div>
        <h1 class="text-xl font-bold">Settings</h1>
        <form @submit.prevent="form.post(route('settings.update'))" class="mt-4">
            <div>
                <label for="maxRooms" class="block font-medium">Max Rooms</label>
                <input id="maxRooms" type="number" min="1" v-model="form.maxRooms" class="mt-1 border rounded w-full" />
                <div v-if="form.errors.maxRooms" class="text-red-500 text-sm mt-1">{{ form.errors.maxRooms }}</div>
            </div>
            <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded" :disabled="form.processing">
                Save
            </button>
        </form>
    </div>
</template>
