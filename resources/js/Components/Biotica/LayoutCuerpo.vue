<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import Titulo from '@/Components/Biotica/LayoutTitulo.vue';

    const props = defineProps({
            tituloArea: {
                type: String,
                required: true,
            },
            tituloPag: {
                type: String,
                required: true,
            }
        });
</script>

<template>
    <AppLayout :title="tituloPag">
        <div class="flex justify-center min-h-[90vh] p-4">
            <div class="container w-full h-full">
                <Titulo :titulo="tituloArea" />
                <br>
                <div class="bg-white shadow-md sm:rounded-lg w-full flex flex-col">
                    <div class="p-4 w-full h-full overflow=auto">
                        <el-card class="box-card w-full h-full flex flex-col">
                            <div class="w-full h-full flex flex-col flex-grow">
                                <slot></slot>
                            </div>
                        </el-card>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>