<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
    tickets:   Object,
    tecnicos:  Array,
})

const modalNuevo    = ref(false)
const filtroEstado  = ref('todos')
const filtroPrioridad = ref('todos')

const form = useForm({
    empresa_id:   '',
    sucursal_id:  '',
    contacto_id:  '',
    categoria_id: '',
    titulo:       '',
    descripcion:  '',
    prioridad:    'media',
})

// Cargar empresas para el modal
const empresas    = ref([])
const sucursales  = ref([])
const categorias  = ref([])

async function cargarEmpresas() {
    const res = await fetch(route('admin.empresas.index'), {
        headers: { 'X-Inertia': 'true', 'Accept': 'application/json' }
    })
}

function crearTicket() {
    form.post(route('admin.tickets.store'), {
        onSuccess: () => {
            modalNuevo.value = false
            form.reset()
        }
    })
}

const estadoClass = {
    abierto:    'bg-red-100 text-red-700',
    asignado:   'bg-blue-100 text-blue-700',
    en_proceso: 'bg-yellow-100 text-yellow-700',
    en_espera:  'bg-orange-100 text-orange-700',
    resuelto:   'bg-green-100 text-green-700',
    cerrado:    'bg-gray-100 text-gray-500',
}

const prioridadClass = {
    baja:    'bg-gray-100 text-gray-500',
    media:   'bg-blue-100 text-blue-600',
    alta:    'bg-orange-100 text-orange-600',
    critica: 'bg-red-100 text-red-700',
}

const prioridadIcono = {
    baja: '🟢', media: '🔵', alta: '🟠', critica: '🔴'
}

function fmt(fecha) {
    if (!fecha) return '—'
    return new Date(fecha).toLocaleDateString('es-GT', {
        day: '2-digit', month: '2-digit', year: 'numeric'
    })
}
</script>

<template>
    <Head title="Tickets" />
    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h1 class="text-xl font-bold text-gray-800">🎫 Tickets</h1>
                <button @click="modalNuevo = true"
                        class="bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-800">
                    + Nuevo Ticket
                </button>
            </div>
        </template>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-medium text-gray-600">Estado:</span>
                    <button v-for="e in [
                        { val: 'todos',      label: 'Todos'      },
                        { val: 'abierto',    label: '🔴 Abierto' },
                        { val: 'asignado',   label: '🔵 Asignado'},
                        { val: 'en_proceso', label: '🟡 En Proceso'},
                        { val: 'en_espera',  label: '🟠 En Espera'},
                        { val: 'resuelto',   label: '🟢 Resuelto'},
                        { val: 'cerrado',    label: '⚫ Cerrado' },
                    ]" :key="e.val"
                            @click="filtroEstado = e.val; router.get(route('admin.tickets.index'), { estado: e.val }, { preserveState: true })"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition"
                            :class="filtroEstado === e.val
                                ? 'bg-indigo-700 text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        {{ e.label }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow overflow-hidden overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Código</th>
                        <th class="text-left px-4 py-3">Título</th>
                        <th class="text-left px-4 py-3">Empresa</th>
                        <th class="text-left px-4 py-3">Sucursal</th>
                        <th class="text-center px-4 py-3">Prioridad</th>
                        <th class="text-center px-4 py-3">Estado</th>
                        <th class="text-left px-4 py-3">Técnico</th>
                        <th class="text-center px-4 py-3">Fecha</th>
                        <th class="text-center px-4 py-3">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="t in tickets.data" :key="t.id"
                        class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-indigo-600 font-bold">{{ t.codigo }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800 max-w-xs truncate">{{ t.titulo }}</p>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ t.empresa?.nombre }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ t.sucursal?.nombre || '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span :class="prioridadClass[t.prioridad]"
                                  class="text-xs px-2 py-1 rounded-full capitalize font-medium">
                                {{ prioridadIcono[t.prioridad] }} {{ t.prioridad }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span :class="estadoClass[t.estado]"
                                  class="text-xs px-2 py-1 rounded-full capitalize">
                                {{ t.estado.replace('_', ' ') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ t.tecnico ? `${t.tecnico.nombre} ${t.tecnico.apellido}` : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-400">
                            {{ fmt(t.created_at) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a :href="route('admin.tickets.show', t.id)"
                               class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-indigo-700">
                                Ver →
                            </a>
                        </td>
                    </tr>
                    <tr v-if="!tickets.data?.length">
                        <td colspan="9" class="text-center py-12 text-gray-400">
                            <p class="text-4xl mb-2">🎫</p>
                            <p>No hay tickets con estos filtros</p>
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
                   :class="p.active ? 'bg-indigo-700 text-white' : 'text-gray-600 hover:bg-gray-50'" />
            </div>
        </div>

        <!-- Modal nuevo ticket -->
        <div v-if="modalNuevo"
             class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="bg-indigo-700 text-white p-5 rounded-t-xl sticky top-0">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-lg">🎫 Nuevo Ticket</h3>
                        <button @click="modalNuevo = false" class="text-2xl">✕</button>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    <p class="text-sm text-gray-500 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        💡 Para crear el ticket necesitás el ID de empresa. 
                        Podés buscarlo en la sección <a :href="route('admin.empresas.index')" class="text-indigo-600 underline">Empresas</a>.
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">ID Empresa *</label>
                            <input v-model="form.empresa_id" type="number"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                            <p v-if="form.errors.empresa_id" class="text-red-500 text-xs mt-1">{{ form.errors.empresa_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">ID Sucursal</label>
                            <input v-model="form.sucursal_id" type="number"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Prioridad *</label>
                            <select v-model="form.prioridad"
                                    class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400">
                                <option value="baja">🟢 Baja</option>
                                <option value="media">🔵 Media</option>
                                <option value="alta">🟠 Alta</option>
                                <option value="critica">🔴 Crítica</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">ID Categoría</label>
                            <input v-model="form.categoria_id" type="number"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Título *</label>
                        <input v-model="form.titulo" type="text"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400"
                               placeholder="Descripción breve del problema" />
                        <p v-if="form.errors.titulo" class="text-red-500 text-xs mt-1">{{ form.errors.titulo }}</p>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Descripción detallada *</label>
                        <textarea v-model="form.descripcion" rows="4"
                                  class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400"
                                  placeholder="Detallá el problema, cuándo ocurrió, qué equipos están afectados..."></textarea>
                        <p v-if="form.errors.descripcion" class="text-red-500 text-xs mt-1">{{ form.errors.descripcion }}</p>
                    </div>
                </div>
                <div class="p-5 border-t flex gap-3 sticky bottom-0 bg-white rounded-b-xl">
                    <button @click="modalNuevo = false"
                            class="flex-1 border border-gray-300 text-gray-600 py-2 rounded-lg text-sm hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button @click="crearTicket" :disabled="form.processing"
                            class="flex-1 bg-indigo-700 text-white py-2 rounded-lg text-sm font-bold hover:bg-indigo-800 disabled:opacity-50">
                        {{ form.processing ? '⏳ Creando...' : '✅ Crear Ticket' }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>