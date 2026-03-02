<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ empresas: Array })

const form = useForm({
    nombre: '', nit: '', telefono: '', email: '', direccion: ''
})

const editando  = ref(null)
const formEdit  = useForm({
    nombre: '', nit: '', telefono: '', email: '', direccion: ''
})

function guardar() {
    form.post(route('admin.empresas.store'), { onSuccess: () => form.reset() })
}

function iniciarEdicion(e) {
    editando.value   = e.id
    formEdit.nombre   = e.nombre
    formEdit.nit      = e.nit
    formEdit.telefono = e.telefono
    formEdit.email    = e.email
    formEdit.direccion= e.direccion
}

function guardarEdicion(e) {
    formEdit.put(route('admin.empresas.update', e.id), {
        onSuccess: () => editando.value = null
    })
}

function desactivar(e) {
    if (confirm(`¿Desactivar ${e.nombre}?`))
        useForm({}).delete(route('admin.empresas.destroy', e.id))
}
</script>

<template>
    <Head title="Empresas" />
    <AdminLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-800">🏢 Empresas</h1>
        </template>

        <!-- Formulario nuevo -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="font-bold text-gray-700 mb-4">Nueva Empresa</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nombre *</label>
                    <input v-model="form.nombre" type="text"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    <p v-if="form.errors.nombre" class="text-red-500 text-xs mt-1">{{ form.errors.nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">NIT</label>
                    <input v-model="form.nit" type="text"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Teléfono</label>
                    <input v-model="form.telefono" type="text"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Email</label>
                    <input v-model="form.email" type="email"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">Dirección</label>
                    <input v-model="form.direccion" type="text"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                </div>
            </div>
            <button @click="guardar" :disabled="form.processing"
                    class="mt-4 bg-indigo-700 text-white px-6 py-2 rounded-lg hover:bg-indigo-800 text-sm font-medium disabled:opacity-50">
                + Agregar Empresa
            </button>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow overflow-hidden overflow-x-auto">
            <table class="w-full text-sm min-w-[700px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Empresa</th>
                        <th class="text-left px-4 py-3">NIT</th>
                        <th class="text-left px-4 py-3">Contacto</th>
                        <th class="text-center px-4 py-3">Sucursales</th>
                        <th class="text-center px-4 py-3">Tickets</th>
                        <th class="text-center px-4 py-3">Estado</th>
                        <th class="text-center px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="e in empresas" :key="e.id">
                        <tr class="border-t hover:bg-gray-50">
                            <template v-if="editando === e.id">
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.nombre"
                                           class="border rounded px-2 py-1 text-sm w-full" />
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.nit"
                                           class="border rounded px-2 py-1 text-sm w-full" />
                                </td>
                                <td class="px-4 py-2" colspan="2">
                                    <input v-model="formEdit.telefono" placeholder="Teléfono"
                                           class="border rounded px-2 py-1 text-sm w-full mb-1" />
                                    <input v-model="formEdit.email" placeholder="Email"
                                           class="border rounded px-2 py-1 text-sm w-full" />
                                </td>
                                <td></td>
                                <td class="px-4 py-2 text-center" colspan="2">
                                    <div class="flex gap-2 justify-center">
                                        <button @click="guardarEdicion(e)"
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
                                    <p class="font-semibold text-gray-800">{{ e.nombre }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ e.nit || '—' }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-xs text-gray-600">{{ e.telefono || '—' }}</p>
                                    <p class="text-xs text-gray-400">{{ e.email || '—' }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-1 rounded-full font-bold">
                                        {{ e.sucursales_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full font-bold">
                                        {{ e.tickets_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="e.activo
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-500'"
                                          class="text-xs px-2 py-1 rounded-full font-semibold">
                                        {{ e.activo ? '✅ Activa' : '❌ Inactiva' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <a :href="route('admin.empresas.show', e.id)"
                                           class="text-indigo-500 hover:underline text-xs">Ver</a>
                                        <button @click="iniciarEdicion(e)"
                                                class="text-blue-500 hover:underline text-xs">Editar</button>
                                        <button v-if="e.activo" @click="desactivar(e)"
                                                class="text-red-400 hover:underline text-xs">Desactivar</button>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    </template>
                    <tr v-if="!empresas.length">
                        <td colspan="7" class="text-center py-10 text-gray-400">
                            No hay empresas registradas
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>