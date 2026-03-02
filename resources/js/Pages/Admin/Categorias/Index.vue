<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ categorias: Array })

const form = useForm({
    nombre: '', descripcion: '', tipo: 'remoto',
    precio_base: '', precio_variable: false
})

const editando = ref(null)
const formEdit = useForm({
    nombre: '', descripcion: '', tipo: '',
    precio_base: '', precio_variable: false
})

const tipoLabel = {
    remoto:        '💻 Remoto',
    presencial:    '🏢 Presencial',
    instalacion:   '🔧 Instalación',
    mantenimiento: '🛠️ Mantenimiento',
}

const tipoClass = {
    remoto:        'bg-blue-100 text-blue-700',
    presencial:    'bg-green-100 text-green-700',
    instalacion:   'bg-orange-100 text-orange-700',
    mantenimiento: 'bg-purple-100 text-purple-700',
}

function guardar() {
    form.post(route('admin.categorias.store'), { onSuccess: () => form.reset() })
}

function iniciarEdicion(c) {
    editando.value          = c.id
    formEdit.nombre         = c.nombre
    formEdit.descripcion    = c.descripcion
    formEdit.tipo           = c.tipo
    formEdit.precio_base    = c.precio_base
    formEdit.precio_variable= c.precio_variable
}

function guardarEdicion(c) {
    formEdit.put(route('admin.categorias.update', c.id), {
        onSuccess: () => editando.value = null
    })
}

function desactivar(c) {
    if (confirm(`¿Desactivar "${c.nombre}"?`))
        useForm({}).delete(route('admin.categorias.destroy', c.id))
}

function fmt(val) {
    return new Intl.NumberFormat('es-GT', {
        style: 'currency', currency: 'GTQ'
    }).format(val || 0)
}
</script>

<template>
    <Head title="Categorías de Soporte" />
    <AdminLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-800">🔧 Categorías de Soporte</h1>
        </template>

        <!-- Formulario nuevo -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="font-bold text-gray-700 mb-4">Nueva Categoría</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nombre *</label>
                    <input v-model="form.nombre" type="text"
                           placeholder="Ej: Soporte Remoto Básico"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    <p v-if="form.errors.nombre" class="text-red-500 text-xs mt-1">{{ form.errors.nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Tipo *</label>
                    <select v-model="form.tipo"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400">
                        <option value="remoto">💻 Remoto</option>
                        <option value="presencial">🏢 Presencial</option>
                        <option value="instalacion">🔧 Instalación</option>
                        <option value="mantenimiento">🛠️ Mantenimiento</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Precio base *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400 text-sm">Q</span>
                        <input v-model="form.precio_base" type="number" step="0.01" min="0"
                               class="w-full border rounded-lg pl-7 pr-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    </div>
                    <p v-if="form.errors.precio_base" class="text-red-500 text-xs mt-1">{{ form.errors.precio_base }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">Descripción</label>
                    <input v-model="form.descripcion" type="text"
                           placeholder="Descripción breve del tipo de soporte"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                </div>
                <div class="flex items-center gap-3 pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" v-model="form.precio_variable" class="rounded w-4 h-4" />
                        <span class="text-sm text-gray-600">Precio variable</span>
                    </label>
                    <p class="text-xs text-gray-400">
                        (El precio puede variar por ticket)
                    </p>
                </div>
            </div>
            <button @click="guardar" :disabled="form.processing"
                    class="mt-4 bg-indigo-700 text-white px-6 py-2 rounded-lg hover:bg-indigo-800 text-sm font-medium disabled:opacity-50">
                + Agregar Categoría
            </button>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow overflow-hidden overflow-x-auto">
            <table class="w-full text-sm min-w-[700px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Nombre</th>
                        <th class="text-left px-4 py-3">Descripción</th>
                        <th class="text-center px-4 py-3">Tipo</th>
                        <th class="text-center px-4 py-3">Precio Base</th>
                        <th class="text-center px-4 py-3">Variable</th>
                        <th class="text-center px-4 py-3">Tickets</th>
                        <th class="text-center px-4 py-3">Estado</th>
                        <th class="text-center px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="c in categorias" :key="c.id">
                        <tr class="border-t hover:bg-gray-50">
                            <template v-if="editando === c.id">
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.nombre"
                                           class="border rounded px-2 py-1 text-sm w-full" />
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.descripcion"
                                           class="border rounded px-2 py-1 text-sm w-full" />
                                </td>
                                <td class="px-4 py-2">
                                    <select v-model="formEdit.tipo"
                                            class="border rounded px-2 py-1 text-sm w-full">
                                        <option value="remoto">Remoto</option>
                                        <option value="presencial">Presencial</option>
                                        <option value="instalacion">Instalación</option>
                                        <option value="mantenimiento">Mantenimiento</option>
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.precio_base" type="number"
                                           class="border rounded px-2 py-1 text-sm w-24" />
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <input type="checkbox" v-model="formEdit.precio_variable" class="rounded" />
                                </td>
                                <td></td>
                                <td></td>
                                <td class="px-4 py-2 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <button @click="guardarEdicion(c)"
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
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ c.nombre }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs max-w-xs truncate">
                                    {{ c.descripcion || '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="tipoClass[c.tipo]"
                                          class="text-xs px-2 py-1 rounded-full font-medium">
                                        {{ tipoLabel[c.tipo] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-indigo-700">
                                    {{ fmt(c.precio_base) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span v-if="c.precio_variable"
                                          class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">
                                        Sí
                                    </span>
                                    <span v-else class="text-gray-400 text-xs">Fijo</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full font-bold">
                                        {{ c.tickets_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="c.activo
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-500'"
                                          class="text-xs px-2 py-1 rounded-full font-semibold">
                                        {{ c.activo ? '✅ Activa' : '❌ Inactiva' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <button @click="iniciarEdicion(c)"
                                                class="text-blue-500 hover:underline text-xs">Editar</button>
                                        <button v-if="c.activo" @click="desactivar(c)"
                                                class="text-red-400 hover:underline text-xs">Desactivar</button>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    </template>
                    <tr v-if="!categorias.length">
                        <td colspan="8" class="text-center py-10 text-gray-400">
                            No hay categorías registradas
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>