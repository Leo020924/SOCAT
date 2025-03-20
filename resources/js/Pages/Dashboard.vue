<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <!--template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template-->

        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-4 lg:px-12">
                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg w-full max-h-[80vh]">
                    <Welcome />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
