<template>
    <ModalGenerico :visible="visible" :onClose="onCloseModal">
        <el-card class="box-card">
            <div>
                <el-form :model="autorTax" :rules="rules" ref="autorTaxFormRef">
                    <el-row :gutter="10">
                        <el-form-item label="Nombre de la autoridad" prop="nombreAutoridad">
                            <el-input type="text" maxlength="100" v-model="autorTax.nombreAutoridad"
                                aria-placeholder="Nombre de la autoridad" show-word-limit></el-input>
                        </el-form-item>

                        <el-form-item label="Nombre completo" prop="nombreCompleto">
                            <el-input type="text" maxlength="255" show-word-limit v-model="autorTax.nombreCompleto"
                                aria-placeholder="Nombre completo">
                            </el-input>
                        </el-form-item>

                        <el-form-item label="Grupo taxonómico" prop="grupoTaxonomico">
                            <el-input type="text" maxlength="255" show-word-limit v-model="autorTax.grupoTaxonomico"
                                aria-placeholder="Grupo taxonómico">
                            </el-input>
                        </el-form-item>

                    </el-row>
                </el-form>
            </div>
            <div class="d-flex flex-row-reverse">
                <el-row :gutter="10">
                    <div v-if="accion === 'editar'">
                        <el-tooltip class="item" effect="dark" content="Guardar" placement="right-start">
                            <el-button size="small" circle type="warning" @click="confirmarEdicion">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-usb-drive" viewBox="0 0 16 16">
                                    <path
                                        d="M6 .5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4H6v-4ZM7 1v1h1V1H7Zm2 0v1h1V1H9ZM6 5a1 1 0 0 0-1 1v8.5A1.5 1.5 0 0 0 6.5 16h4a1.5 1 0 0 0 1.5-1.5V6a1 1 0 0 0-1-1H6Zm0 1h5v8.5a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5V6Z" />
                                </svg>
                            </el-button>
                        </el-tooltip>
                    </div>
                    <div v-else>
                        <el-tooltip class="item" effect="dark" content="Guardar" placement="right-start">
                            <el-button size="small" circle type="warning" @click="confirmarEdicion">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-usb-drive" viewBox="0 0 16 16">
                                    <path
                                        d="M6 .5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4H6v-4ZM7 1v1h1V1H7Zm2 0v1h1V1H9ZM6 5a1 1 0 0 0-1 1v8.5A1.5 1.5 0 0 0 6.5 16h4a1.5 1 0 0 0 1.5-1.5V6a1 1 0 0 0-1-1H6Zm0 1h5v8.5a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5V6Z" />
                                </svg>
                            </el-button>
                        </el-tooltip>
                    </div>
                </el-row>
            </div>
        </el-card>
    </ModalGenerico>
</template>

<script setup>
import { ref, watch } from 'vue';
import ModalGenerico from '@/Components/Biotica/ModalGenerico.vue';
import axios from 'axios';
import { ElMessageBox, ElMessage } from 'element-plus';

const props = defineProps({
    visible: Boolean,
    accion: String,
    autTaxEdit: Object,
    paginaActual: Number,
    onCloseModal: Function, // Prop para cerrar el modal
});

const emit = defineEmits(['cerrar', 'resultadoAlta', 'resultadoEditar', 'reload']); // <- Agrega 'reload'

const autorTax = ref({
    nombreAutoridad: '',
    nombreCompleto: '',
    grupoTaxonomico: '',
});

const originalAutorTax = ref(null);

const rules = {
    nombreAutoridad: [
        { required: true, message: 'Ingrese un nombre de autoridad', trigger: 'blur' },
        { min: 1, max: 100, message: 'El tamaño debe ser entre 1 y 100', trigger: 'blur' }
    ],
    nombreCompleto: [
        { max: 255, message: 'El tamaño debe ser menor o igual a 255 caracteres', trigger: 'blur' }
    ],
    grupoTaxonomico: [
        { required: true, message: 'Ingrese un grupo taxonómico', trigger: 'blur' },
        { max: 255, message: 'El tamaño debe ser menor o igual a 255 caracteres', trigger: 'blur' }
    ]
};

watch(() => props.autTaxEdit, (newVal) => {
    console.log("props.autTaxEdit", newVal);
    if (newVal) {
        autorTax.value = {
            nombreAutoridad: newVal?.NombreAutoridad,
            nombreCompleto: newVal?.NombreCompleto,
            grupoTaxonomico: newVal?.GrupoTaxonomico,
        };
        originalAutorTax.value = { ...autorTax.value };
    } else {
        autorTax.value = {
            nombreAutoridad: '',
            nombreCompleto: '',
            grupoTaxonomico: '',
        };
        originalAutorTax.value = null;
    }
}, { immediate: true });

const cerrarModal = () => {
    if (originalAutorTax.value) {
        autorTax.value = { ...originalAutorTax.value };
    } else {
        autorTax.value = {
            nombreAutoridad: '',
            nombreCompleto: '',
            grupoTaxonomico: '',
        };
    }
    emit('cerrar');
};

const autorTaxFormRef = ref(null);
const confirmarEdicion = async () => {
    console.log('Confirmar Edicion');

    const isValid = await autorTaxFormRef.value.validate();
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
            console.log("Acción:", props.accion);
            console.log("autTaxEdit:", props.autTaxEdit);
            if (props.accion === 'crear') {
                response = await axios.post('/autores', autorTax.value);
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
                console.log("Datos enviados en PUT:", autorTax.value);
                const data = { ...autorTax.value, IdAutorTaxon: props.autTaxEdit?.IdAutorTaxon };
                console.log("Respuesta de la edición:", data);
                response = await axios.put(`/autores/${props.autTaxEdit?.IdAutorTaxon}`, data);
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
                    message: "El nombre que intenta ingresar no cumple con las reglas de ingreso o modificación",
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
                console.error("Error al guardar el autor:", error);
            }
        }
    }
}

</script>

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