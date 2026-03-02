<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

const props = defineProps({
    contactos: Array,
    empresas:  Array,
})

const form = useForm({
    empresa_id:  '',
    sucursal_id: '',
    nombre:      '',
    apellido:    '',
    telefono:    '',
    cargo:       '',
    email:       '',
    password:    '',
})

const editando = ref(null)
const formEdit = useForm({
    nombre: '', apellido: '', telefono: '',
    cargo: '', sucursal_id: ''
})

const filtroEmpresa    = ref('todas')
const sucursalesForm   = ref([])
const sucursalesEdit   = ref([])

// Cargar sucursales según empresa seleccionada en el form
watch(() => form.empresa_id, (id) => {
    form.sucursal_id = ''
    const empresa = props.empresas.find(e => e.id == id)
    sucursalesForm.value = empresa?.sucursales || []
})

function iniciarEdicion(c) {
    editando.value        = c.id
    formEdit.nombre       = c.nombre
    formEdit.apellido     = c.apellido
    formEdit.telefono     = c.telefono
    formEdit.cargo        = c.cargo
    formEdit.sucursal_id  = c.sucursal_id
    const empresa = props.empresas.find(e => e.id === c.empresa_id)
    sucursalesEdit.value  = empresa?.sucursales || []
}

const contactosFiltrados = computed(() => {
    if (filtroEmpresa.value === 'todas') return props.contactos
    return props.contactos.filter(c => c.empresa_id == filtroEmpresa.value)
})

function guardar() {
    form.post(route('admin.contactos.store'), {
        onSuccess: () => form.reset()
    })
}

function guardarEdicion(c) {
    formEdit.put(route('admin.contactos.update', c.id), {
        onSuccess: () => editando.value = null
    })
}

function desactivar(c) {
    if (confirm(`¿Desactivar a ${c.nombre} ${c.apellido}?`))
        useForm({}).delete(route('admin.contactos.destroy', c.id))
}
</script>

<template>
    <Head title="Contactos" />
    <AdminLayout>
        <template #header>
            <h1 class="text-xl font-bold text-gray-800">👤 Contactos</h1>
        </template>

        <!-- Formulario nuevo -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="font-bold text-gray-700 mb-4">Nuevo Contacto / Usuario Cliente</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Empresa *</label>
                    <select v-model="form.empresa_id"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400">
                        <option value="">Seleccionar empresa...</option>
                        <option v-for="e in empresas" :key="e.id" :value="e.id">
                            {{ e.nombre }}
                        </option>
                    </select>
                    <p v-if="form.errors.empresa_id" class="text-red-500 text-xs mt-1">
                        {{ form.errors.empresa_id }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Sucursal</label>
                    <select v-model="form.sucursal_id"
                            :disabled="!form.empresa_id"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 disabled:bg-gray-100">
                        <option value="">Sin sucursal específica</option>
                        <option v-for="s in sucursalesForm" :key="s.id" :value="s.id">
                            {{ s.nombre }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Cargo</label>
                    <input v-model="form.cargo" type="text"
                           placeholder="Ej: Gerente de IT"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400" />
                </div>
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
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-700">
                💡 Al crear el contacto se genera automáticamente un usuario con acceso al portal de clientes.
            </div>
            <button @click="guardar" :disabled="form.processing"
                    class="mt-4 bg-indigo-700 text-white px-6 py-2 rounded-lg hover:bg-indigo-800 text-sm font-medium disabled:opacity-50">
                + Crear Contacto y Usuario
            </button>
        </div>

        <!-- Filtro por empresa -->
        <div class="flex gap-2 mb-4 flex-wrap">
            <button @click="filtroEmpresa = 'todas'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                    :class="filtroEmpresa === 'todas'
                        ? 'bg-indigo-700 text-white'
                        : 'bg-white text-gray-600 hover:bg-gray-50 shadow'">
                Todos ({{ contactos.length }})
            </button>
            <button v-for="e in empresas" :key="e.id"
                    @click="filtroEmpresa = e.id"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                    :class="filtroEmpresa === e.id
                        ? 'bg-indigo-700 text-white'
                        : 'bg-white text-gray-600 hover:bg-gray-50 shadow'">
                {{ e.nombre }} ({{ contactos.filter(c => c.empresa_id === e.id).length }})
            </button>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow overflow-hidden overflow-x-auto">
            <table class="w-full text-sm min-w-[800px]">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="text-left px-4 py-3">Nombre</th>
                        <th class="text-left px-4 py-3">Empresa</th>
                        <th class="text-left px-4 py-3">Sucursal</th>
                        <th class="text-left px-4 py-3">Cargo</th>
                        <th class="text-left px-4 py-3">Teléfono</th>
                        <th class="text-center px-4 py-3">Acceso</th>
                        <th class="text-center px-4 py-3">Estado</th>
                        <th class="text-center px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="c in contactosFiltrados" :key="c.id">
                        <tr class="border-t hover:bg-gray-50">
                            <template v-if="editando === c.id">
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.nombre" placeholder="Nombre"
                                           class="border rounded px-2 py-1 text-sm w-full mb-1" />
                                    <input v-model="formEdit.apellido" placeholder="Apellido"
                                           class="border rounded px-2 py-1 text-sm w-full" />
                                </td>
                                <td class="px-4 py-2 text-xs text-gray-400">
                                    {{ c.empresa?.nombre }}
                                </td>
                                <td class="px-4 py-2">
                                    <select v-model="formEdit.sucursal_id"
                                            class="border rounded px-2 py-1 text-sm w-full">
                                        <option value="">Sin sucursal</option>
                                        <option v-for="s in sucursalesEdit" :key="s.id" :value="s.id">
                                            {{ s.nombre }}
                                        </option>
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input v-model="formEdit.cargo"
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
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-sm flex-shrink-0">
                                            👤
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800">
                                                {{ c.nombre }} {{ c.apellido }}
                                            </p>
                                            <p class="text-xs text-gray-400">{{ c.user?.email || '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-1 rounded-full">
                                        {{ c.empresa?.nombre }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    {{ c.sucursal?.nombre || '—' }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ c.cargo || '—' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ c.telefono || '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span v-if="c.user"
                                          class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">
                                        ✅ Activo
                                    </span>
                                    <span v-else class="text-gray-400 text-xs">Sin acceso</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="c.activo
                                            ? 'bg-green-100 text-green-700'
                                            : 'bg-red-100 text-red-500'"
                                          class="text-xs px-2 py-1 rounded-full font-semibold">
                                        {{ c.activo ? '✅ Activo' : '❌ Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex gap-2 justify-center">
                                        <button @click="iniciarEdicion(c)"
                                                class="text-blue-500 hover:underline text-xs">
                                            Editar
                                        </button>
                                        <button v-if="c.activo" @click="desactivar(c)"
                                                class="text-red-400 hover:underline text-xs">
                                            Desactivar
                                        </button>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    </template>
                    <tr v-if="!contactosFiltrados?.length">
                        <td colspan="8" class="text-center py-10 text-gray-400">
                            No hay contactos registrados
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>