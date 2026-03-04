<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="CYBERSEC | Login" />

        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#050c1a] via-[#0a1f3d] to-[#061226]">

            <div class="w-full max-w-md bg-[#0d1b2a]/90 backdrop-blur-lg shadow-2xl rounded-2xl p-8 border border-cyan-500/20">

                <!-- LOGO -->
                <div class="flex justify-center mb-6">
                    <!-- Cambiá la ruta si lo guardás en public/images -->
                    <img src="/images/cybersec.jpeg" class="w-28 drop-shadow-[0_0_15px_#00f5ff]" />
                </div>

                <h2 class="text-center text-2xl font-bold text-cyan-400 tracking-widest mb-6">
                    CYBERSEC LOGIN
                </h2>

                <div v-if="status" class="mb-4 text-sm font-medium text-green-400 text-center">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">

                    <!-- EMAIL -->
                    <div>
                        <InputLabel for="email" value="Email" class="text-cyan-300" />

                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full bg-[#112240] border-cyan-500/40 text-white focus:ring-cyan-400 focus:border-cyan-400 rounded-lg"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />

                        <InputError class="mt-2 text-red-400" :message="form.errors.email" />
                    </div>

                    <!-- PASSWORD -->
                    <div class="mt-4">
                        <InputLabel for="password" value="Password" class="text-cyan-300" />

                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full bg-[#112240] border-cyan-500/40 text-white focus:ring-cyan-400 focus:border-cyan-400 rounded-lg"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />

                        <InputError class="mt-2 text-red-400" :message="form.errors.password" />
                    </div>

                    <!-- REMEMBER -->
                    <div class="mt-4 block">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" class="text-cyan-400 focus:ring-cyan-400" />
                            <span class="ms-2 text-sm text-gray-300">
                                Remember me
                            </span>
                        </label>
                    </div>

                    <!-- ACTIONS -->
                    <div class="mt-6 flex items-center justify-between">

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-cyan-400 hover:text-cyan-300 underline"
                        >
                            Forgot password?
                        </Link>

                        <button
                            type="submit"
                            class="px-6 py-2 bg-cyan-500 hover:bg-cyan-400 text-black font-semibold rounded-lg shadow-lg shadow-cyan-500/40 transition-all duration-300"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Log in
                        </button>

                    </div>
                </form>

            </div>
        </div>
    </GuestLayout>
</template>