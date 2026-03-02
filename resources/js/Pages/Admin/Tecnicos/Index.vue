<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ tecnicos: Array })

const form = useForm({
    nombre: '', apellido: '', email: '',
    password: '', telefono: '', especialidad: ''
})

const editando = ref(null)
const formEdit = useForm({
    nombre: '', apellido: '', telefono: '', especialidad: ''
})

function guardar() {
    form.post(route('admin.tecnicos.store'), { onSuccess: () => form.reset() })
}

function iniciarEdicion(t) {
    editando.value        = t.id
    formEdit.nombre       = t.nombre
    formEdit.apellido     = t.apellido
    formEdit.telefono     = t.telefono
    formEdit.especialidad = t.especialidad
}

function guardarEdicion(t) {
    formEdit.put(route('admin.tecnicos.update', t.id), {
        onSuccess: () => editando.value = null
    })
}

function desactivar(t) {
    if (confirm(`¿Desactivar a ${t.nombre} ${t.apellido}?`))
        useForm({}).delete(route('admin.tecnicos.destroy', t.id))
}
</script>

<template>
    <Head title="Técnicos" />
    <AdminLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-800">👨‍💻 Técnicos</h1>
        </template>

        <!-- Formulario nuevo -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="font-bold text-gray-700 mb-4">Nuevo Técnico</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nombre *</label>
                    <input v-model="form.nombre" type="text"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    <p v-if="form.errors.nombre" class="text-red-500 text-xs mt-1">{{ form.errors.nombre }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Apellido *</label>
                    <input v-model="form.apellido" type="text"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    <p v-if="form.errors.apellido" class="text-red-500 text-xs mt-1">{{ form.errors.apellido }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Especialidad</label>
                    <input v-model="form.especialidad" type="text"
                           placeholder="Ej: Redes, Hardware, Software..."
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Teléfono</label>
                    <input v-model="form.telefono" type="text"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Email (login) *</label>
                    <input v-model="form.email" type="email"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Contraseña *</label>
                    <input v-model="form.password" type="password"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                    <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                </div>
            </div>
            <button @click="guardar" :disabled="form.processing"
                    class="mt-4 bg-indigo-700 text-white px-6 py-2 rounded-lg hover:bg-indigo-800 text-sm font-medium disabled:opacity-50">
                + Agregar Técnico
            </button>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow overflow-hidden overflow-x-auto">
            <table class="w-full text-sm min-w-[700px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Técnico</th>
                        <th class="text-left px-4 py-3">Especialidad</th>
                        <th class="text-left px-4 py-3">Teléfono</th>
                        <th class="text-left px-4 py-3">Email</th>
                        <th class="text-center px-4 py-3">Tickets</th>
                        <th class="text-center px-4 py-3">Estado</th>
                        <th class="text-center px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="t in tecnicos" :key="t.id">
                        <tr class="border-t hover:bg-gray-50">
                            <template v-if="editando === t.id">
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.nombre"
                                           class="border rounded px-2 py-1 text-sm w-full mb-1" placeholder="Nombre" />
                                    <input v-model="formEdit.apellido"
                                           class="border rounded px-2 py-1 text-sm w-full" placeholder="Apellido" />
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.especialidad"
                                           class="border rounded px-2 py-1 text-sm w-full" />
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.telefono"
                                           class="border rounded px-2 py-1 text-sm w-full" />
                                </td>
                                <td class="px-4 py-2 text-xs text-gray-400">
                                    {{ t.user?.email || '—' }}
                                </td>
                                <td></td>
                                <td></td>
                                <td class="px-4 py-2 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <button @click="guardarEdicion(t)"
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
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center text-sm flex-shrink-0">
                                            👨‍💻
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800">
                                                {{ t.nombre }} {{ t.apellido }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span v-if="t.especialidad"
                                          class="bg-indigo-100 text-indigo-700 text-xs px-2 py-1 rounded-full">
                                        {{ t.especialidad }}
                                    </span>
                                    <span v-else class="text-gray-400 text-xs">—</span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ t.telefono || '—' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ t.user?.email || '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full font-bold">
                                        {{ t.tickets_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="t.activo
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-500'"
                                          class="text-xs px-2 py-1 rounded-full font-semibold">
                                        {{ t.activo ? '✅ Activo' : '❌ Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <button @click="iniciarEdicion(t)"
                                                class="text-blue-500 hover:underline text-xs">
                                            Editar
                                        </button>
                                        <button v-if="t.activo" @click="desactivar(t)"
                                                class="text-red-400 hover:underline text-xs">
                                            Desactivar
                                        </button>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    </template>
                    <tr v-if="!tecnicos.length">
                        <td colspan="7" class="text-center py-10 text-gray-400">
                            No hay técnicos registrados
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>