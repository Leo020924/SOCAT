<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue';
import ModalGenerico from '@/Components/Biotica/ModalGenerico.vue';
import axios from 'axios';
import { ElMessageBox, ElMessage } from 'element-plus';

const props = defineProps({
    visible: Boolean,
    gpoTaxEdit: {
        type: Object,
        default: null
    },
    accion: String,
});

const emit = defineEmits(['cerrar', 'resultadoAlta', 'resultadoEditar', 'reload']);

const form = ref({
    GrupoSCAT: '',
    GrupoAbreviado: '',
    GrupoSNIB: '',
});

const originalForm = ref(null);

const rules = {
    GrupoSCAT: [
        { required: true, message: 'Ingrese el nombre del grupo', trigger: 'blur' },
        { min: 1, max: 255, message: 'La longitud debe estar entre 1 y 255', trigger: 'blur' }
    ],
    GrupoAbreviado: [
        { max: 50, message: 'La longitud debe ser menor o igual a 50', trigger: 'blur' }
    ],
    GrupoSNIB: [
        { max: 50, message: 'La longitud debe ser menor o igual a 50', trigger: 'blur' }
    ],
};

const formRef = ref(null);

// MODIFICACIÓN IMPORTANTE: Añadimos un flag para evitar el bucle
const updatingForm = ref(false);

watch(
    () => props.gpoTaxEdit,
    (newVal) => {
        if (!updatingForm.value) { // Verificamos el flag
            updatingForm.value = true; // Activamos el flag

            if (newVal) {
                form.value = {
                    GrupoSCAT: newVal.GrupoSCAT,
                    GrupoAbreviado: newVal.GrupoAbreviado,
                    GrupoSNIB: newVal.GrupoSNIB,
                };
                originalForm.value = { ...form.value };
            } else {
                form.value = { GrupoSCAT: '', GrupoAbreviado: '', GrupoSNIB: '' };
                originalForm.value = null;
            }

            updatingForm.value = false; // Desactivamos el flag
        }
    },
    { immediate: true }
);

const cerrarModal = () => {
    if (originalForm.value) {
        form.value = { ...originalForm.value };
    } else {
        form.value = { GrupoSCAT: '', GrupoAbreviado: '', GrupoSNIB: '' };
    }
    emit('cerrar');
};

const submitForm = async () => {
    const isValid = await formRef.value.validate();
    if (isValid) {
        try {
            await ElMessageBox.confirm(
                '¿Estás seguro de que deseas guardar este registro?',
                'Confirmar guardado',
                {
                    confirmButtonText: 'Sí, guardar',
                    cancelButtonText: 'Cancelar',
                    type: 'warning',
                    draggable: true
                }
            )

            let response;
            if (props.accion === 'crear') {
                response = await axios.post('/grupos-taxonomicos', form.value);
                if (response.data.status === 200) {
                    emit('resultadoAlta', response.data);
                    emit('reload');
                    cerrarModal();
                } else {
                    ElMessage({
                        message: response.data.message,
                        type: 'warning',
                        duration: 5000,
                    });
                }
            } else {
                response = await axios.put(`/grupos-taxonomicos/${props.gpoTaxEdit.IdGrupoSCAT}`, form.value);
                if (response.status === 200) {
                    emit('resultadoEditar', response.data);
                    emit('reload');
                    cerrarModal();
                } else {
                    ElMessage({
                        message: response.data.message,
                        type: 'warning',
                        duration: 5000,
                    });
                }
            }
        } catch (error) {
            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                let errorMessage = '';
                for (const field in errors) {
                    errorMessage += errors[field].join('\n') + '\n';
                }
                ElMessage({
                    message: errorMessage,
                    type: 'error',
                    duration: 5000,
                });
            } else if (error.response && error.response.status === 400) {
                ElMessage({
                    message: error.response.data.message,
                    type: 'warning',
                    duration: 5000,
                });
            }
            else if (error.response && error.response.status === 500) {
                ElMessage({
                    message: "Ocurrió un error en el servidor. Por favor, inténtelo de nuevo más tarde.",
                    type: 'error',
                    duration: 5000,
                });
                console.error("Error del servidor:", error);
            }
            else if (error !== 'cancel') {
                console.error("Error al guardar el grupo:", error);
            }
        }
    }
};
</script>

<template>
    <ModalGenerico :visible="visible" @close="cerrarModal">
        <template #header>
            <h2>{{ accion === 'crear' ? 'Alta de Grupo Taxonómico' : 'Editar Grupo Taxonómico' }}</h2>
        </template>
        <el-card class="box-card">
            <el-form :model="form" ref="formRef" :rules="rules">
                <el-form-item label="Nombre del Grupo" prop="GrupoSCAT">
                    <el-input type="text" v-model="form.GrupoSCAT" />
                </el-form-item>
                <el-form-item label="Abreviado" prop="GrupoAbreviado">
                    <el-input type="text" v-model="form.GrupoAbreviado" />
                </el-form-item>
                <el-form-item label="Grupo SNIB" prop="GrupoSNIB">
                    <el-input type="text" v-model="form.GrupoSNIB" />
                </el-form-item>
            </el-form>

            <template #footer>
                <el-button @click="cerrarModal">Cancelar</el-button>
                <el-button type="primary" @click="submitForm">Confirmar</el-button>
            </template>
        </el-card>
    </ModalGenerico>
</template>

<style scoped>
.box-card {
    width: 100%;
}

.d-flex {
    display: flex;
}

.flex-row-reverse {
    flex-direction: row-reverse;
}
</style>