<template>
  <AppLayout title="Bibliografía">
    <template #header>
      <!-- Mantiene lo que tengas en tu encabezado -->
    </template>
    <div class="app-container">
      <div class="content-wrapper">
        <h1 style="font-size: 24px;" class="font-semibold text-xl text-gray-800 leading-tight">
          Catálogo de Bibliografía
        </h1>

        <div class="table-wrapper">
          <el-card class="box-card">
            <template #header>
              <div class="header-container">
                <div class="left">


                  <el-button type="primary" @click="crear">
                    <el-icon>
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-box-arrow-in-down" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                          d="M3.5 6a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 1 0-1h2A1.5 1.5 0 0 1 14 6.5v8a1.5.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-8A1.5 1.5 0 0 1 3.5 5h2a.5.5 5 0 0 1 0 1h-2z" />
                        <path fill-rule="evenodd"
                          d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                      </svg>
                    </el-icon>
                    Nuevo
                  </el-button>
                  <el-button type="primary" icon="el-icon-d-arrow-right">Exportar</el-button>
                  <el-button type="primary" @click="filtroCatalogos" icon="el-icon-setting">Filtros</el-button>
                </div>

                <div class="right">
                  <el-input placeholder="Filtrar por" v-model="filterText" class="filter-input"
                    @keyup.enter="querySearch" />
                </div>
              </div>

              <!-- <div class="table-filters">
                <el-checkbox v-model="checkAll" @change="handleCheckAllChange">Marcar todos</el-checkbox>
                <el-checkbox v-for="opc in opciones" :label="opc" :key="opc" :value="opc"
                  @change="handleCheckboxChange(opc, $event)">
                  {{ opc }}
                </el-checkbox>
              </div> -->
            </template>

            <div class="table-responsive">
              <el-table v-if="localTableData" :data="localTableData" border height="400" style="width: 100%"
                @row-click="handleRowClick" :default-sort="{ prop: 'Autor', order: 'ascending' }"
                :highlight-current-row="true" @sort-change="handleSortChange">
                <el-table-column type="expand">
                  <template #default="scope">
                    <div class="bg-gray-50 p-4 rounded-md">
                      <p><strong>IdOriginal:</strong> {{ scope.row.IdOriginal }}</p>
                    </div>
                  </template>
                </el-table-column>
                <el-table-column v-for="column in tableColumns" :key="column.label" :label="column.label"
                  :prop="column.prop" :column-key="column.prop" :min-width="column.minWidth" :sortable="column.sortable"
                  :align="column.align" :header-align="column.align" :fixed="column.fixed || null"
                  :formatter="column.formatter || null">
                </el-table-column>
                <el-table-column align="center" width="80">
                  <template #default="scope">
                    <el-tooltip class="item" effect="dark" content="Modificar" placement="left">
                      <el-button size="small" type="success" icon="el-icon-edit" @click="editar(scope.row)"
                        circle></el-button>
                    </el-tooltip>
                  </template>
                </el-table-column>

                <el-table-column align="center" width="80">
                  <template #default="scope">
                    <div>
                      <el-popconfirm title="¿Realmente desea eliminar la información?"
                      @confirm="borrarDatos(scope.row.IdBibliografia)">
                        <template #reference>
                          <el-button size="small" type="danger" icon="el-icon-delete" circle></el-button>
                        </template>
                      </el-popconfirm>
                    </div>
                  </template>
                </el-table-column>

              </el-table>
            </div>
          </el-card>

          <div class="referencia-completa">
            <span class="demo-input-label">Referencia completa</span>
            <el-input type="textarea" :rows="4" v-model="cita" show-word-limit></el-input>
          </div>

          <!-- Paginación -->
          <div v-if="lastPage > 1" class="pagination-wrapper">
            <span>Total {{ total }}</span>
            <el-pagination @current-change="handlePageChange" :current-page="currentPage"
              :page-sizes="[100, 150, 200, 255, 300]" :page-size="pageSize" layout="prev, pager, next, jumper, sizes"
              :total="total" @size-change="handleSizeChange" />
          </div>
        </div>

        <DialogForm v-model="dialogFormVisible" style="width:1250px" :botCerrar="true" :pressEsc="false">
          <FormBibliografia v-if="dialogFormVisible" :accion="accBiblio" :biblio-edit="rowEdit"
            @se-guardo="cerrarDialogo">
          </FormBibliografia>
        </DialogForm>


      </div>
    </div>
  </AppLayout>
</template>






<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import NuevoButton from '@/Components/Biotica/NuevoButton.vue';
import EditarButton from '@/Components/Biotica/EditarButton.vue';
import EliminarButton from '@/Components/Biotica/EliminarButton.vue';
import FormBibliografia from '@/Pages/Socat/Bibliografia/FormBibliografia.vue';
import DialogForm from '@/Components/Biotica/DialogGeneral.vue';
import { ElMessageBox } from 'element-plus';

const props = defineProps({
  bibliografiaData: {
    type: Object,
    required: true
  }
});

const handleSortChange = (column) => {
  console.log("Ordenamiento Cambiado:", column);
  // Aquí puedes agregar la lógica para ordenar la tabla
  // Por ejemplo, puedes llamar a la API con los parámetros de ordenamiento
};

const cerrarDialogo = () => {
  dialogFormVisible.value = false; // Cerrar el diálogo
  list(); // Recargar la lista de bibliografías
};


const localTableData = ref(props.bibliografiaData.data);
const total = ref(props.bibliografiaData.totalItems);
const currentPage = ref(props.bibliografiaData.currentPage);
const lastPage = ref(props.bibliografiaData.lastPage);
const nextPageUrl = ref(props.bibliografiaData.nextPageUrl);
const prevPageUrl = ref(props.bibliografiaData.prevPageUrl);

const handlePageChange = (newPage) => {
  axios.get(`/bibliografias-api?page=${newPage}`)
    .then(response => {
      localTableData.value = response.data.bibliografiaData.data;
      total.value = response.data.bibliografiaData.totalItems;
      currentPage.value = response.data.bibliografiaData.currentPage;
      lastPage.value = response.data.bibliografiaData.lastPage;
      nextPageUrl.value = response.data.bibliografiaData.nextPageUrl;
      prevPageUrl.value = response.data.bibliografiaData.prevPageUrl;
    })
    .catch(error => {
      console.error('Error al cargar la página:', error);
    });
};

const handleSizeChange = (newSize) => {
  axios.get(`/bibliografias-api?page=${currentPage.value}&perPage=${newSize}`)
    .then(response => {
      localTableData.value = response.data.bibliografiaData.data;
      total.value = response.data.bibliografiaData.totalItems;
      currentPage.value = response.data.bibliografiaData.currentPage;
      lastPage.value = response.data.bibliografiaData.lastPage;
      nextPageUrl.value = response.data.bibliografiaData.nextPageUrl;
      prevPageUrl.value = response.data.bibliografiaData.prevPageUrl;
      pageSize.value = newSize;
    });
};

const opcionesBuscar = ['Autor(es)', 'Año(s)', 'Titulo de la publicación', 'Cita bibliográfica',
  'Titulo de la subpublicación', 'ISBN/ISSN'];


const paginas = ref(1);
const checkAll = ref(false);
const opciones = ref(opcionesBuscar);
const isIndeterminate = ref(true);
const opcionesElejidas = ref([]);
const pageSize = ref(100);
const cita = ref('');
const idBiblio = ref('');
const accBiblio = ref('');
const filterText = ref('');
const dialogFormVisible = ref(false);
const dialogFormVisibleCat = ref(false);
const filgrupoScat = ref([]);
const rowEdit = ref({}); // Usar ref para rowEdit
const sorting = ref({ column: null, order: null });
const tableColumns = ref([
  { prop: "Autor", label: "Autor(es)", minWidth: 150, sortable: true, hidden: false, align: 'left', fixed: true },
  { prop: "Anio", label: "Año(s)", minWidth: 80, sortable: true, hidden: false, align: 'left' },
  { prop: "TituloPublicacion", label: "Titulo de la publicación", minWidth: 250, sortable: true, hidden: false, align: 'left' },
  { prop: "TituloSubPublicacion", label: "Titulo de la subpublicación", minWidth: 200, sortable: true, hidden: false, align: 'left' },
  { prop: "EditorialPaisPagina", label: "Editorial, país, lugar, páginas", minWidth: 200, sortable: true, hidden: false, align: 'left' },
  { prop: "NumeroVolumenAnio", label: "Número, volumen, año, mes(es)", minWidth: 200, sortable: true, hidden: false, align: 'left' },
  { prop: "EditoresCompiladores", label: "Editor(es)/compílador(es)", minWidth: 200, sortable: true, hidden: false, align: 'left' },
  { prop: "ISBNISSN", label: "ISBN / ISSN", minWidth: 150, sortable: true, hidden: false, align: 'left' }
]);

const handleCheckAllChange = (val) => {
  opcionesElejidas.value = val ? [...opciones] : [];
  updateIndeterminateState();
};

const handleCheckboxChange = (opc, event) => {
  if (event.target.checked) {
    opcionesElejidas.value.push(opc);
  } else {
    opcionesElejidas.value = opcionesElejidas.value.filter(item => item !== opc);
  }
  updateIndeterminateState();
};

const updateIndeterminateState = () => {
  const checkedCount = opcionesElejidas.value.length;
  checkAll.value = checkedCount === opciones.value.length;
  isIndeterminate.value = checkedCount > 0 && checkedCount < opciones.value.length;
};

const crear = () => {
  dialogFormVisible.value = true;
  accBiblio.value = 'crear';
  rowEdit.value = {};
};

const editar = (row) => {
  accBiblio.value = 'editar';
  console.log("Valor de accBiblio:", accBiblio.value); // Agrega esta línea
  rowEdit.value = row;
  dialogFormVisible.value = true;
};
const handleRowClick = (row) => {
  citaCompleta(row);
  idBiblio.value = row.IdBibliografia;
  cargaGrupos();
};

const citaCompleta = (row) => {
  let orden = '';
  let citaComp = '';

  const campos = ['', 'Autor', 'Anio', 'TituloSubPublicacion', 'TituloPublicacion', 'EditoresCompiladores', 'NumeroVolumenAnio', 'ISBNISSN'];

  if (row.OrdenCitaCompleta != '') {
    orden = row.OrdenCitaCompleta;
  } else {
    orden = '1243765';
  }

  const myArray = orden.split("");

  for (let i = 0; i < myArray.length; i++) {
    if (citaComp != '') {
      if (row[campos[myArray[i]]] != '') {
        citaComp += ' ' + row[campos[myArray[i]]];
      }
    } else {
      if (row[campos[myArray[i]]] != '') {
        citaComp = row[campos[myArray[i]]];
      }
    }
    cita.value = citaComp;
  };
}

const cargaGrupos = async () => {
  try {
    // const response = await axios.get(`/carga-Biblio?idBiblio=${idBiblio.value}`);
    // tableGrupo.value = response.data;
  } catch (error) {
    console.error('Error al cargar grupos:', error);
  }
};

const borrarGrp = (index, row) => {
  // store.dispatch('borraBiblioGrp', { idBiblio: row.IdBibliografia, idGrupo: row.IdGrupoSCAT, observ: row.Observaciones });
  cargaGrupos();
};

const borrarDatos = (id) => {  // Recibimos el ID
  axios.delete(`/bibliografias/${id}`)
    .then(response => {
      if (response.data.status === 200) {
        list(); // Recarga la lista
      } else {
        ElMessageBox.alert('Error al eliminar bibliografía', 'Error', { type: 'error' });
      }
    })
    .catch(error => {
      console.error('Error al eliminar bibliografía:', error);
      ElMessageBox.alert('Error al eliminar bibliografía', 'Error', { type: 'error' });
    });
};
const querySearch = async () => {
  try {
    let params = {};
    if (opcionesElejidas.value.length > 0) {
      params = {
        opciones: opcionesElejidas.value,
        buscado: filterText.value,
        page: currentPage.value,
        perPage: pageSize.value,
        sortBy: sorting.value.column,
        sortOrder: sorting.value.order,
      };
    } else {
      params = {
        buscado: filterText.value,
        page: currentPage.value,
        perPage: pageSize.value,
        sortBy: sorting.value.column,
        sortOrder: sorting.value.order,
      };
    }

    const response = await axios.get(route('bibliografias.index'), { params });
    if (response.status === 200) {
      localTableData.value = response.data.bibliografiaData.data;
      total.value = response.data.bibliografiaData.totalItems;
      lastPage.value = response.data.bibliografiaData.lastPage;

      nextPageUrl.value = response.data.bibliografiaData.nextPageUrl;
      prevPageUrl.value = response.data.bibliografiaData.prevPageUrl;
    }
  } catch (error) {
    console.error('Error al buscar bibliografías:', error);
  }
};

const list = async (page = 1) => {
  console.log("Función list() ejecutada");
  try {
    let params = {};
    if (opcionesElejidas.value.length > 0) {
      params = {
        opciones: opcionesElejidas.value,
        buscado: filterText.value,
        page: page,
        perPage: pageSize.value,
        sortBy: sorting.value.column,
        sortOrder: sorting.value.order,
      };
    } else {
      params = {
        page: page,
        perPage: pageSize.value,
        buscado: filterText.value,
        sortBy: sorting.value.column,
        sortOrder: sorting.value.order,
      };
    }

    const response = await axios.get('/bibliografias-api', { params }); // Utiliza la ruta de la API
    console.log("Datos recibidos:", response.data);
    localTableData.value = response.data?.bibliografiaData?.data || [];
    total.value = response.data.bibliografiaData.totalItems;
    paginas.value = response.data.bibliografiaData.lastPage;
    currentPage.value = page;
    nextPageUrl.value = response.data.bibliografiaData.nextPageUrl;
    prevPageUrl.value = response.data.bibliografiaData.prevPageUrl;

  } catch (error) {
    console.error('Error al cargar bibliografías:', error);
  }
};

const filtroCatalogos = () => {
  if (idBiblio.value != '') {
    dialogFormVisibleCat.value = true;
  } else {
    ElMessageBox.alert('Primero se debe seleccionar una bibliografía', 'SOCAT', { type: 'warning' });
  }
};

onMounted(() => {
  list();
});
</script>
<style scoped>
.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: #f0f2f5;
  align-items: center;
  justify-content: flex-start;
  padding: 10px;
  box-sizing: border-box;
}

.content-wrapper {
  width: 100%;
  max-width: 1200px;
  background-color: white;
  border-radius: 10px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
  padding: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 10px auto;

}

.table-wrapper {
  width: 100%;
  margin-top: 15px;
}

.table-responsive {
  overflow-x: auto;
}

.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  margin-bottom: 15px;
}

.header-container .left {
  flex: 1;
  text-align: left;
}

.header-container .right {
  flex: 1;
  text-align: right;
}

.pagination-wrapper {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-top: 20px;
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
  color: #495057;
}

.p-4 {
  padding: 1.5rem;
}

.rounded-md {
  border-radius: 0.5rem;
}

.el-pagination {
  display: flex;
  justify-content: center;
  margin-top: 10px;
  margin-bottom: 10px;
}

.el-pagination button,
.el-pagination span {
  font-size: 12px;
  padding: 6px 10px;
}

.total-text {
  display: none;
}

.table-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 10px;
}

.referencia-completa {
  margin-top: 20px;
}

@media (max-width: 768px) {
  .app-container {
    padding: 5px;
  }

  .content-wrapper {
    padding: 5px;
  }

  .el-table__header th {
    font-size: 12px;
    padding: 10px 6px;
  }

  .el-table__body td {
    font-size: 12px;
    padding: 8px 6px;
  }

  .el-pagination button,
  .el-pagination span {
    font-size: 12px;
    padding: 5px 10px;
  }

}
</style>