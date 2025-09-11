<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    maxRooms: Number,
});

const form = ref({
    max_rooms: props.maxRooms || 1,
});

const submit = () => {
    router.post('/settings', form.value);
};
</script>

<template>
    <Head title="Configurações" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configurações</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="max_rooms" value="Número máximo de salas" />
                            <TextInput id="max_rooms" type="number" v-model="form.max_rooms" class="mt-1 block w-full" />
                        </div>
                        <PrimaryButton type="submit">Salvar</PrimaryButton>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
