<script setup>
import { ref, watchEffect, onMounted } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import NuevoButton from '@/Components/Biotica/NuevoButton.vue';
import EditarButton from '@/Components/Biotica/EditarButton.vue';
import EliminarButton from '@/Components/Biotica/EliminarButton.vue';
import FiltrarPor from '@/Components/Biotica/FiltrarPorInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import { ElMessageBox } from 'element-plus';
import FormTipoDistribucion from '@/Pages/Socat/TiposDistribucion/FormTipoDistribucion.vue';

const { datosTipoDistribucion } = usePage().props;

const currentData = ref([]);
const currentPage = ref(1);
const totalItems = ref(0);
const itemsPerPage = 100;
const prevPageUrl = ref(null);
const nextPageUrl = ref(null);
const tipoDistribucion = ref('');
const modalVisible = ref(false);
const tipoDistribucionEditado = ref(null);
const sorting = ref({ column: null, order: null });

// Función para obtener los datos con el término de búsqueda y la página actual
const fetchFilteredData = async () => {
    try {
        const response = await axios.get('/busca-tipo-distribucion', {
            params: {
                tipoDistribucion: tipoDistribucion.value,
                page: currentPage.value,
                sortBy: sorting.value.column,
                sortOrder: sorting.value.order,
            }
        });
        console.log("Este es el response", response);
        currentData.value = response.data.data;
        totalItems.value = response.data.totalItems;
        prevPageUrl.value = response.data.prevPageUrl;
        nextPageUrl.value = response.data.nextPageUrl;
    } catch (error) {
        console.error('Error al obtener los datos:', error);
    }
};

// Esto es para el ordenamieto DESC y ASC
const handleSortChange = (column) => {
    sorting.value.column = column.prop;
    sorting.value.order = column.order;
    fetchFilteredData();
};

// Función para agregar un TipoDistribucion
const nuevoTipoDistribucion = () => {
    tipoDistribucionEditado.value = null;
    modalVisible.value = true;
    console.log("Modal visible:", modalVisible.value);
};

// Función para editar un TipoDistribucion
const editarTipoDistribucion = (tipoDistribucion) => {
    tipoDistribucionEditado.value = tipoDistribucion;
    modalVisible.value = true;
    console.log("Modal visible:", modalVisible.value);
    console.log('TipoDistribucion a editar', tipoDistribucion.IdTipoDistribucion);
};

// Función para eliminar un TipoDistribucion
const eliminarTipoDistribucion = async (idTipoDistribucion) => {
    console.log("ID del TipoDistribucion a eliminar", idTipoDistribucion);
    try {
        await ElMessageBox.confirm(
            '¿Estás seguro de que deseas eliminar este registro?',
            'Confirmar eliminación',
            {
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                type: 'warning',
                draggable: true
            }
        )

        await axios.delete(`/tipos-distribucion/${idTipoDistribucion}`);
        fetchFilteredData();
    } catch (error) {
        if (error !== 'cancel') {
            console.error("Error al eliminar el TipoDistribucion:", error);
        }
    }
};

// Función para buscar por TipoDistribucion
const buscarPor = () => {
    currentPage.value = 1;
    fetchFilteredData();
};

// Función para manejar el cambio de página en la paginación
const handlePageChange = (page) => {
    currentPage.value = page;
    fetchFilteredData();
};

const handleResultado = async (nuevoTipoDistribucion) => {
    console.log('handleResultado', nuevoTipoDistribucion);
    modalVisible.value = false;
    fetchFilteredData();
};

const cerrarModal = () => {
    modalVisible.value = false
}

const props = defineProps({
    datosTipoDistribucion: {
        type: Object,
        required: false,
    },
});

watchEffect(() => {
    if (props.datosTipoDistribucion) {
        fetchFilteredData();
    }
});

onMounted(() => {
    currentData.value = datosTipoDistribucion?.data || [];
    totalItems.value = datosTipoDistribucion?.totalItems || 0;
    currentPage.value = datosTipoDistribucion?.currentPage || 1;
    prevPageUrl.value = datosTipoDistribucion?.prevPageUrl || null;
    nextPageUrl.value = datosTipoDistribucion?.nextPageUrl || null;
    modalVisible.value = false;
});
</script>

<template>
    <AppLayout title="TipoDistribucion">
        <template #header>
        </template>

        <div class="app-container">
            <div class="content-wrapper">
                <h1 style="font-size: 24px;" class="font-semibold text-xl text-gray-800 leading-tight">
                    Catálogo de Tipos de Distribución
                </h1>

                <div class="table-wrapper">
                    <el-card class="box-card">
                        <template #header>
                            <div class="header-wrapper">
                                <div class="left">
                                    <FiltrarPor v-model:busca="tipoDistribucion" @update:busca="buscarPor()" />
                                </div>
                                <div class="right">
                                    <NuevoButton @crear="nuevoTipoDistribucion" />
                                </div>
                            </div>
                        </template>
                        <div class="table-responsive">
                            <el-table :data="currentData" :border="true" height="400" style="width: 100%"
                                :highlight-current-row="true" 
                                @sort-change="handleSortChange">
                                <el-table-column type="expand">
                                    <template #default="{ row }">
                                        <div class="bg-gray-50 p-4 rounded-md">
                                            <p><strong>Fecha de Captura:</strong> {{ row.FechaCaptura }}</p>
                                            <p><strong>Fecha de Modificación:</strong> {{ row.FechaModificacion }}</p>
                                        </div>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="Descripcion" label="Descripción" min-width="120"
                                    sortable="custom" align="center"></el-table-column>

                                <el-table-column label="Acciones" width="120">
                                    <template #default="{ row }">
                                        <div class="flex justify-around">
                                            <EditarButton @editar="editarTipoDistribucion(row)" />
                                            <EliminarButton
                                                @eliminar="eliminarTipoDistribucion(row.IdTipoDistribucion)" />
                                        </div>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </div>
                    </el-card>
                    <div v-if="totalItems > itemsPerPage" class="pagination-wrapper">
                        <el-pagination :current-page="currentPage" :page-size="itemsPerPage" :total="totalItems"
                            @current-change="handlePageChange" layout="total, prev, pager, next, jumper" background
                            class="my-2">
                            <template #prev>
                                <el-button :disabled="!prevPageUrl" @click="handlePrevNext('prev')"
                                    size="small">Anterior</el-button>
                            </template>
                            <template #next>
                                <el-button :disabled="!nextPageUrl" @click="handlePrevNext('next')"
                                    size="small">Siguiente</el-button>
                            </template>
                        </el-pagination>
                    </div>
                </div>

                <FormTipoDistribucion :visible="modalVisible" :tipoDistEdit="tipoDistribucionEditado"
                    :accion="tipoDistribucionEditado ? 'editar' : 'crear'" @resultadoAlta="handleResultado"
                    @resultadoEditar="handleResultado" @cerrar="cerrarModal" />
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.app-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: #f0f2f5;
}

.content-wrapper {
    width: 95%;
    max-width: 1200px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 20px auto;
}

.table-wrapper {
    width: 100%;
    margin-top: 15px;
}

.table-responsive {
    overflow-x: auto;
}

.header-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    margin-bottom: 15px;
}

.header-wrapper .left {
    flex: 1;
    text-align: left;
}

.header-wrapper .right {
    flex: 1;
    text-align: right;
}

.pagination-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-top: 30px;
    margin-bottom: 10px;
}

.el-table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    font-family: 'Roboto', sans-serif;
    color: #495057;
    background-color: #fff;
    width: 100%;
}

.el-table__header th {
    background-color: #343a40;
    font-weight: 500;
    color: #fff;
    text-align: left;
    padding: 12px 15px;
    border-bottom: 2px solid #495057;
    font-size: 14px;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.el-table__body td {
    text-align: left;
    padding: 12px 15px;
    font-size: 14px;
    border-bottom: 1px solid #495057;
}

.el-table__body tr:hover {
    background-color: #343a4010;
    transition: background-color 0.15s ease-in-out;
}

.el-table-column.type-expand .el-table__cell {
    padding: 15px;
    background-color: #343a4005;
    border-bottom: 1px solid #495057;
}

.el-table-column.type-expand .el-table__cell:hover {
    background-color: #343a4020;
}

.bg-gray-50 {
    background-color: #343a4005;
    color: #343a40;
}

.p-4 {
    padding: 1.5rem;
}

.rounded-md {
    border-radius: 0.5rem;
}

@media (max-width: 768px) {
    .content-wrapper {
        padding: 10px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .el-table {
        min-width: 600px;
    }

    .el-table-column[prop="Descripcion"] {
        min-width: auto;
        width: auto;
    }

    .el-table__header th {
        padding: 8px;
        font-size: 12px;
    }

    .el-table__body td {
        padding: 8px;
        font-size: 12px;
    }
}
</style>