<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    surgeries: Object,
    patients: Array,
    types: Array,
    rooms: Number,
});

const form = ref({
    patient: '',
    type: '',
    room: '',
    start: '',
    duration: '',
});

const user = usePage().props.auth.user;
const role = user.role;

const submit = () => {
    router.post('/surgeries', form.value);
};

const roomNumbers = computed(() => Array.from({ length: props.rooms || 0 }, (_, i) => i + 1));

const canEdit = (surgery) => role === 'admin' || (role === 'doctor' && surgery.created_by_id === user.id);
const canDelete = canEdit;
const canConfirm = (surgery) => role === 'admin' || role === 'nurse';
const canCancel = canConfirm;

const statusClass = (surgery) => {
    if (surgery.status === 'confirmed') return 'surgery-status-confirmed';
    if (surgery.status === 'canceled') return 'surgery-status-canceled';
    return 'surgery-status-scheduled';
};

const edit = (surgery) => router.visit(`/surgeries/${surgery.id}/edit`);
const destroy = (surgery) => {
    if (confirm('Excluir cirurgia?')) {
        router.delete(`/surgeries/${surgery.id}`);
    }
};
const confirmSurgery = (surgery) => router.post(`/surgeries/${surgery.id}/confirm`);
const cancelSurgery = (surgery) => router.post(`/surgeries/${surgery.id}/cancel`);
</script>

<template>
    <Head title="Cirurgias" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cirurgias</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <InputLabel for="patient" value="Paciente" />
                            <select id="patient" v-model="form.patient" class="mt-1 block w-full border-gray-300 rounded-md">
                                <option value="" disabled>Selecione</option>
                                <option v-for="p in patients" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="type" value="Tipo" />
                            <select id="type" v-model="form.type" class="mt-1 block w-full border-gray-300 rounded-md">
                                <option value="" disabled>Selecione</option>
                                <option v-for="t in types" :key="t.id" :value="t.id">{{ t.name }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="room" value="Sala" />
                            <select id="room" v-model="form.room" class="mt-1 block w-full border-gray-300 rounded-md">
                                <option value="" disabled>Selecione</option>
                                <option v-for="n in roomNumbers" :key="n" :value="n">{{ n }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="start" value="Início" />
                            <TextInput id="start" type="datetime-local" v-model="form.start" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel for="duration" value="Duração (min)" />
                            <TextInput id="duration" type="number" v-model="form.duration" class="mt-1 block w-full" />
                        </div>
                        <div class="col-span-full flex justify-end mt-4">
                            <PrimaryButton type="submit">Agendar</PrimaryButton>
                        </div>
                    </form>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="mb-4 flex space-x-4">
                        <div class="flex items-center"><span class="w-3 h-3 rounded-full mr-2 surgery-status-scheduled"></span>Agendada</div>
                        <div class="flex items-center"><span class="w-3 h-3 rounded-full mr-2 surgery-status-confirmed"></span>Confirmada</div>
                        <div class="flex items-center"><span class="w-3 h-3 rounded-full mr-2 surgery-status-canceled"></span>Cancelada</div>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Paciente</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Sala</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Início</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Duração</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Fim</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Criado por</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Confirmado por</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Cancelado por</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="surgery in surgeries.data" :key="surgery.id" :class="statusClass(surgery)">
                                <td class="px-3 py-2">{{ surgery.patient }}</td>
                                <td class="px-3 py-2">{{ surgery.type }}</td>
                                <td class="px-3 py-2">{{ surgery.room }}</td>
                                <td class="px-3 py-2">{{ surgery.start }}</td>
                                <td class="px-3 py-2">{{ surgery.duration }}</td>
                                <td class="px-3 py-2">{{ surgery.end }}</td>
                                <td class="px-3 py-2">{{ surgery.created_by_name }}</td>
                                <td class="px-3 py-2">{{ surgery.confirmed_by_name }}</td>
                                <td class="px-3 py-2">{{ surgery.canceled_by_name }}</td>
                                <td class="px-3 py-2 space-x-2">
                                    <template v-if="canEdit(surgery)">
                                        <SecondaryButton @click="edit(surgery)">Editar</SecondaryButton>
                                        <DangerButton @click="destroy(surgery)">Excluir</DangerButton>
                                    </template>
                                    <template v-if="canConfirm(surgery)">
                                        <PrimaryButton v-if="surgery.status !== 'confirmed'" @click="confirmSurgery(surgery)">Confirmar</PrimaryButton>
                                        <DangerButton v-if="surgery.status !== 'canceled'" @click="cancelSurgery(surgery)">Cancelar</DangerButton>
                                    </template>
                                    <span v-if="surgery.is_conflict" class="badge-conflict ml-2 px-2 py-0.5 rounded-full text-xs font-semibold">!</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <nav v-if="surgeries.links" class="mt-4 flex items-center space-x-2">
                        <template v-for="link in surgeries.links" :key="link.url || link.label">
                            <a v-if="link.url" :href="link.url" v-html="link.label" class="px-2 py-1 border rounded" :class="{ 'bg-gray-200': link.active }"></a>
                            <span v-else v-html="link.label" class="px-2 py-1 text-gray-500"></span>
                        </template>
                    </nav>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

