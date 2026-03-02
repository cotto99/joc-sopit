<script setup>
import ClienteLayout from '@/Layouts/ClienteLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({ tickets: Object })

const estadoClass = {
    abierto:    'bg-red-100 text-red-700',
    asignado:   'bg-blue-100 text-blue-700',
    en_proceso: 'bg-yellow-100 text-yellow-700',
    en_espera:  'bg-orange-100 text-orange-700',
    resuelto:   'bg-green-100 text-green-700',
    cerrado:    'bg-gray-100 text-gray-500',
}

const estadoIcono = {
    abierto:    '🔴',
    asignado:   '🔵',
    en_proceso: '🟡',
    en_espera:  '🟠',
    resuelto:   '🟢',
    cerrado:    '⚫',
}

const prioridadClass = {
    baja:    'bg-gray-100 text-gray-500',
    media:   'bg-blue-100 text-blue-600',
    alta:    'bg-orange-100 text-orange-600',
    critica: 'bg-red-100 text-red-700',
}

function fmt(fecha) {
    if (!fecha) return '—'
    return new Date(fecha).toLocaleDateString('es-GT', {
        day: '2-digit', month: '2-digit', year: 'numeric'
    })
}
</script>

<template>
    <Head title="Mis Tickets" />
    <ClienteLayout>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-black text-gray-800">🎫 Mis Tickets</h2>
                <p class="text-gray-500 text-sm mt-1">Seguimiento de tus solicitudes de soporte</p>
            </div>
            <a :href="route('cliente.tickets.create')"
               class="bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-indigo-800 shadow-lg">
                + Nuevo Ticket
            </a>
        </div>

        <!-- Stats rápidas -->
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-2xl font-black text-red-500">
                    {{ tickets.data?.filter(t => ['abierto','asignado','en_proceso'].includes(t.estado)).length }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Activos</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-2xl font-black text-green-500">
                    {{ tickets.data?.filter(t => t.estado === 'resuelto').length }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Resueltos</p>
            </div>
            <div class="bg-white rounded-xl shadow p-4 text-center">
                <p class="text-2xl font-black text-gray-500">
                    {{ tickets.total || 0 }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Total</p>
            </div>
        </div>

        <!-- Lista de tickets DESKTOP -->
        <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Código</th>
                        <th class="text-left px-4 py-3">Título</th>
                        <th class="text-left px-4 py-3">Sucursal</th>
                        <th class="text-center px-4 py-3">Prioridad</th>
                        <th class="text-center px-4 py-3">Estado</th>
                        <th class="text-left px-4 py-3">Técnico</th>
                        <th class="text-center px-4 py-3">Fecha</th>
                        <th class="text-center px-4 py-3">Ver</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="t in tickets.data" :key="t.id"
                        class="border-t hover:bg-gray-50 cursor-pointer"
                        @click="$inertia.visit(route('cliente.tickets.show', t.id))">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-indigo-600 font-bold">
                                {{ t.codigo }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800 max-w-xs truncate">{{ t.titulo }}</p>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ t.sucursal?.nombre || '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span :class="prioridadClass[t.prioridad]"
                                  class="text-xs px-2 py-1 rounded-full capitalize">
                                {{ t.prioridad }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span :class="estadoClass[t.estado]"
                                  class="text-xs px-2 py-1 rounded-full capitalize">
                                {{ estadoIcono[t.estado] }} {{ t.estado.replace('_', ' ') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ t.tecnico
                                ? `${t.tecnico.nombre} ${t.tecnico.apellido}`
                                : 'Por asignar' }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-400">
                            {{ fmt(t.created_at) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a :href="route('cliente.tickets.show', t.id)"
                               class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-lg text-xs hover:bg-indigo-200">
                                Ver →
                            </a>
                        </td>
                    </tr>
                    <tr v-if="!tickets.data?.length">
                        <td colspan="8" class="text-center py-12 text-gray-400">
                            <p class="text-5xl mb-3">🎫</p>
                            <p class="font-medium text-gray-600">No tenés tickets aún</p>
                            <p class="text-sm mt-1">Creá tu primer ticket de soporte</p>
                            <a :href="route('cliente.tickets.create')"
                               class="inline-block mt-4 bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-800">
                                + Crear Ticket
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Paginación -->
            <div v-if="tickets.last_page > 1"
                 class="px-4 py-3 border-t flex gap-2 justify-end flex-wrap">
                <a v-for="p in tickets.links" :key="p.label"
                   :href="p.url" v-html="p.label"
                   class="px-3 py-1 rounded border text-sm"
                   :class="p.active
                       ? 'bg-indigo-700 text-white'
                       : 'text-gray-600 hover:bg-gray-50'" />
            </div>
        </div>

        <!-- Cards MÓVIL -->
        <div class="md:hidden space-y-3">
            <a v-for="t in tickets.data" :key="t.id"
               :href="route('cliente.tickets.show', t.id)"
               class="block bg-white rounded-xl shadow p-4 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-2">
                    <span class="font-mono text-xs text-indigo-600 font-bold">{{ t.codigo }}</span>
                    <span :class="estadoClass[t.estado]"
                          class="text-xs px-2 py-1 rounded-full capitalize">
                        {{ estadoIcono[t.estado] }} {{ t.estado.replace('_', ' ') }}
                    </span>
                </div>
                <p class="font-semibold text-gray-800 mb-1">{{ t.titulo }}</p>
                <div class="flex items-center justify-between text-xs text-gray-400">
                    <span>{{ t.sucursal?.nombre || 'Sin sucursal' }}</span>
                    <span>{{ fmt(t.created_at) }}</span>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <span :class="prioridadClass[t.prioridad]"
                          class="text-xs px-2 py-1 rounded-full capitalize">
                        {{ t.prioridad }}
                    </span>
                    <span class="text-xs text-gray-500">
                        👨‍💻 {{ t.tecnico
                            ? `${t.tecnico.nombre} ${t.tecnico.apellido}`
                            : 'Por asignar' }}
                    </span>
                </div>
            </a>

            <div v-if="!tickets.data?.length"
                 class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
                <p class="text-4xl mb-2">🎫</p>
                <p class="font-medium">No tenés tickets aún</p>
                <a :href="route('cliente.tickets.create')"
                   class="inline-block mt-4 bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm">
                    + Crear Ticket
                </a>
            </div>
        </div>
    </ClienteLayout>
</template>