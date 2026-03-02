<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    sucursales: Array,
    empresas:   Array,
})

const form = useForm({
    empresa_id: '', nombre: '', direccion: '', telefono: ''
})

const editando = ref(null)
const formEdit = useForm({
    nombre: '', direccion: '', telefono: ''
})

const filtroEmpresa = ref('todas')

const sucursalesFiltradas = computed(() => {
    if (filtroEmpresa.value === 'todas') return props.sucursales
    return props.sucursales.filter(s => s.empresa_id == filtroEmpresa.value)
})

function guardar() {
    form.post(route('admin.sucursales.store'), {
        onSuccess: () => form.reset()
    })
}

function iniciarEdicion(s) {
    editando.value     = s.id
    formEdit.nombre    = s.nombre
    formEdit.direccion = s.direccion
    formEdit.telefono  = s.telefono
}

function guardarEdicion(s) {
    formEdit.put(route('admin.sucursales.update', s.id), {
        onSuccess: () => editando.value = null
    })
}

function desactivar(s) {
    if (confirm(`¿Desactivar sucursal "${s.nombre}"?`))
        useForm({}).delete(route('admin.sucursales.destroy', s.id))
}
</script>

<script>
import { computed } from 'vue'
export default { setup() {} }
</script>

<template>
    <Head title="Sucursales" />
    <AdminLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-800">🏪 Sucursales</h1>
        </template>

        <!-- Formulario nuevo -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="font-bold text-gray-700 mb-4">Nueva Sucursal</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Empresa *</label>
                    <select v-model="form.empresa_id"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400">
                        <option value="">Seleccionar...</option>
                        <option v-for="e in empresas" :key="e.id" :value="e.id">
                            {{ e.nombre }}
                        </option>
                    </select>
                    <p v-if="form.errors.empresa_id" class="text-red-500 text-xs mt-1">
                        {{ form.errors.empresa_id }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nombre *</label>
                    <input v-model="form.nombre" type="text"
                           placeholder="Ej: Sucursal Central"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    <p v-if="form.errors.nombre" class="text-red-500 text-xs mt-1">
                        {{ form.errors.nombre }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Teléfono</label>
                    <input v-model="form.telefono" type="text"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Dirección</label>
                    <input v-model="form.direccion" type="text"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                </div>
            </div>
            <button @click="guardar" :disabled="form.processing"
                    class="mt-4 bg-indigo-700 text-white px-6 py-2 rounded-lg hover:bg-indigo-800 text-sm font-medium disabled:opacity-50">
                + Agregar Sucursal
            </button>
        </div>

        <!-- Filtro por empresa -->
        <div class="flex gap-2 mb-4 flex-wrap">
            <button @click="filtroEmpresa = 'todas'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                    :class="filtroEmpresa === 'todas'
                        ? 'bg-indigo-700 text-white'
                        : 'bg-white text-gray-600 hover:bg-gray-50 shadow'">
                Todas ({{ sucursales.length }})
            </button>
            <button v-for="e in empresas" :key="e.id"
                    @click="filtroEmpresa = e.id"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                    :class="filtroEmpresa === e.id
                        ? 'bg-indigo-700 text-white'
                        : 'bg-white text-gray-600 hover:bg-gray-50 shadow'">
                {{ e.nombre }} ({{ sucursales.filter(s => s.empresa_id === e.id).length }})
            </button>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow overflow-hidden overflow-x-auto">
            <table class="w-full text-sm min-w-[650px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Sucursal</th>
                        <th class="text-left px-4 py-3">Empresa</th>
                        <th class="text-left px-4 py-3">Dirección</th>
                        <th class="text-left px-4 py-3">Teléfono</th>
                        <th class="text-center px-4 py-3">Tickets</th>
                        <th class="text-center px-4 py-3">Estado</th>
                        <th class="text-center px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="s in sucursalesFiltradas" :key="s.id">
                        <tr class="border-t hover:bg-gray-50">
                            <template v-if="editando === s.id">
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.nombre"
                                           class="border rounded px-2 py-1 text-sm w-full" />
                                </td>
                                <td class="px-4 py-2 text-gray-400 text-xs">
                                    {{ s.empresa?.nombre }}
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.direccion"
                                           class="border rounded px-2 py-1 text-sm w-full" />
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.telefono"
                                           class="border rounded px-2 py-1 text-sm w-full" />
                                </td>
                                <td></td>
                                <td></td>
                                <td class="px-4 py-2 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <button @click="guardarEdicion(s)"
                                                class="text-green-600 hover:underline text-xs font-medium">
                                            Guardar
                                        </button>
                                        <button @click="editando = null"
                                                class="text-gray-400 hover:underline text-xs">
                                            Cancelar
                                        </button>
                                    </div>
                                </td>
                            </template>
                            <template v-else>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-800">{{ s.nombre }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-1 rounded-full">
                                        {{ s.empresa?.nombre }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ s.direccion || '—' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ s.telefono || '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full font-bold">
                                        {{ s.tickets_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="s.activo
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-500'"
                                          class="text-xs px-2 py-1 rounded-full font-semibold">
                                        {{ s.activo ? '✅ Activa' : '❌ Inactiva' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <button @click="iniciarEdicion(s)"
                                                class="text-blue-500 hover:underline text-xs">
                                            Editar
                                        </button>
                                        <button v-if="s.activo" @click="desactivar(s)"
                                                class="text-red-400 hover:underline text-xs">
                                            Desactivar
                                        </button>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    </template>
                    <tr v-if="!sucursalesFiltradas?.length">
                        <td colspan="7" class="text-center py-10 text-gray-400">
                            No hay sucursales registradas
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>