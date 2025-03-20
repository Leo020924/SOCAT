<script setup>
import { ref, onMounted, triggerRef } from 'vue';
import { InfoFilled, Setting } from '@element-plus/icons-vue';
import DialogForm from '@/Components/Biotica/DialogGeneral.vue';
import FormNombre from '@/Components/Biotica/FormNombre.vue'; // Asegúrate de que la ruta sea correcta
import FiltroGrupos from '@/Pages/Socat/NombreTaxonomico/FiltroGrupoTax.vue';
import CuerpoGen from '@/Components/Biotica/LayoutCuerpo.vue';
import { ElMessageBox } from 'element-plus';
import { ElLoading } from 'element-plus';
import AutorTaxon from '../Autores/AutorTaxon.vue';
import Logo from '@/Components/Biotica/LogoCategoria.vue';

//Definición de variables
const props = defineProps({
  gruposTax: {
    type: Object,
    required: true,
  },
  categoriasTax: {
    type: Object,
    required: true,
  }
});

const dialogFormVisibleAlta = ref(false); // Para controlar la visibilidad del modal
const taxonAct = ref(null); // Agrega esta línea
const data = ref([]);
const categ = ref(null);
const catalogos = ref('');
const grupos = ref('');
const idsGrupos = ref('');
const mostrar = ref(false);
const dialogFormVisibleCat = ref(false);
const totalReg = ref(0);
const paginas = ref('');
const defaultProps = {
  children: 'children',
  label: 'label'
};
const filterText = ref('');
const catego = ref('');
const tablaNomenclatura = ref([]);
const columnasNom = ref([
  {
    prop: "TipoRelacion",
    label: "Tipo",
    minWidth: 80,
    sortable: true,
    hidden: false,
    align: 'left',
    fixed: true
  },
  {
    prop: "NombreCompleto",
    label: "Nombre",
    minWidth: 80,
    sortable: true,
    hidden: false,
    align: 'left',
    fixed: true
  },
  {
    prop: "estatus",
    label: "Estatus",
    minWidth: 80,
    sortable: true,
    hidden: false,
    align: 'left',
    fixed: true
  }
]);
const tablaReferencias = ref([]);
const columnasRef = ref([
  {
    prop: "Autor",
    label: "Autor",
    minWidth: 80,
    sortable: true,
    hidden: false,
    align: 'left',
    fixed: true
  },
  {
    prop: "Anio",
    label: "Año",
    minWidth: 80,
    sortable: true,
    hidden: false,
    align: 'left',
    fixed: true
  },
  {
    prop: "Titulo",
    label: "Tipo de publicación",
    minWidth: 80,
    sortable: true,
    hidden: false,
    align: 'left',
    fixed: true
  },
  {
    prop: "Cita",
    label: "Cita completa",
    minWidth: 80,
    sortable: true,
    hidden: false,
    align: 'left',
    fixed: true
  }
]);

//Declaración de Funciones
const filtro_Catalogos = () => {
  //Funcion para abrir el modal que muestra los catalogos taxonómicos
  dialogFormVisibleCat.value = true;
};

// Funcion para abrir el modal de FormNombre
const openDialog = (nodeData) => {
    taxonAct.value = nodeData;
    console.log("Datos del nodo al abrir el modal:", taxonAct.value); // Agrega esta línea
    emit('reset-form'); // Emite el evento "reset-form"
    dialogFormVisibleAlta.value = true;
};


const emit = defineEmits(['reset-form']); //Definimos el Emite
//Reseteamos FormNombre
const resetFormNombre = () =>{
 emit('reset-form'); // Emitimos el evento de vuelta al componente
}



//Funcion para cerrar el modal de FormNombre
const closeDialog = () => {
  dialogFormVisibleAlta.value = false;
};

const cerrarDialog = (valor) => {
  dialogFormVisibleCat.value = valor; // Cambia la visibilidad del diálogo
};

//Funcion para recibir los datos de los grupos y catalogos selccionados
const recibeGrupos = (data) => {
  catalogos.value = data['catalogos'];
  grupos.value = data['grupos'];
  idsGrupos.value = data['ids'];

  if (categ.value === '') {
    open("Se debe seleccionar una categoría taxonómica.");
  }
}

//Funcion que se encarga de ejecutar acciones una vez que se crea el elemento
onMounted(() => {
  dialogFormVisibleCat.value = true;
});

//Funcion para el envio de mensajes en pantalla
const open = (mensaje) => {
  ElMessageBox.alert(mensaje, 'Nombres taxonómicos', {
    confirmButtonText: 'OK',
  })
}

//Esta funcion se dispara una vez que se selecciona una categia taxonomica
const handleChange = async (value) => {
  filterText.value = "";
  mostrar.value = false;
  catego.value = value[0];

  if (idsGrupos.value != '') {
    const params = {
      categ: value[0],
      catalog: idsGrupos.value
    };

    const loading = ElLoading.service({
      lock: true,
      text: "Loading",
      spinner: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><path fill="none" d="M0 0h200v200H0z"></path><path fill="none" stroke-linecap="round" stroke="#53B0FF" stroke-width="15" transform-origin="center" d="M70 95.5V112m0-84v16.5m0 0a25.5 25.5 0 1 0 0 51 25.5 25.5 0 0 0 0-51Zm36.4 4.5L92 57.3M33.6 91 48 82.7m0-25.5L33.6 49m58.5 33.8 14.3 8.2"><animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="1.1" values="0;-120" keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform></path><path fill="none" stroke-linecap="round" stroke="#53B0FF" stroke-width="15" transform-origin="center" d="M130 155.5V172m0-84v16.5m0 0a25.5 25.5 0 1 0 0 51 25.5 25.5 0 0 0 0-51Zm36.4 4.5-14.3 8.3M93.6 151l14.3-8.3m0-25.4L93.6 109m58.5 33.8 14.3 8.2"><animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="1.1" values="0;120" keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform></path></svg>`,
      backgroud: 'rgba(255,255,255,0.85)',
    });

    //De forma asincrona se ejecutan las funciones de carga de datos por medio de axios
    const response = await axios.get('/cargar-nomArb', { params });

    if (response.status === 200) {
      data.value = response.data[0];
      totalReg.value = response.data[1].total;
      paginas.value = response.data[1].last_page;
    }
    else {
      open("Se presento un error en la recuperacion de los datos");
    }
    loading.close();
  }
}

//Función para hacer la busqueda de los valores colocados en el input de busqueda
const filterNode = async (value, data) => {
  mostrar.value = false;

  const loading = ElLoading.service({
    lock: true,
    text: "Loading",
    spinner: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><path fill="none" d="M0 0h200v200H0z"></path><path fill="none" stroke-linecap="round" stroke="#53B0FF" stroke-width="15" transform-origin="center" d="M70 95.5V112m0-84v16.5m0 0a25.5 25.5 0 1 0 0 51 25.5 25.5 0 0 0 0-51Zm36.4 4.5L92 57.3M33.6 91 48 82.7m0-25.5L33.6 49m58.5 33.8 14.3 8.2"><animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="1.1" values="0;-120" keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform></path><path fill="none" stroke-linecap="round" stroke="#53B0FF" stroke-width="15" transform-origin="center" d="M130 155.5V172m0-84v16.5m0 0a25.5 25.5 0 1 0 0 51 25.5 25.5 0 0 0 0-51Zm36.4 4.5-14.3 8.3M93.6 151l14.3-8.3m0-25.4L93.6 109m58.5 33.8 14.3 8.2"><animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="1.1" values="0;120" keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform></path></svg>`,
    backgroud: 'rgba(255,255,255,0.85)',
  });

  if (value != '' && idsGrupos.value != '' && categ.value[0] != '') {
    const params = {
      categ: categ.value[0],
      catalog: idsGrupos.value,
      taxon: value
    };

    const response = await axios.get('/cargar-nomArb',
      { params });
    if (response.status === 200) {
      data.value = response.data[0];
      totalReg = response.data[1].total;
      paginas = response.data[1].last_page;
      loading.close();
    }
  } else if (idsGrupos.value != '') {
    const params = {
      categ: categ.value[0],
      catalog: idsGrupos.value
    };

    const response = await axios.get('/cargar-nomArb',
      { params });

    if (response.status === 200) {
      data.value = response.data[0];
      totalReg = response.data[1].total;
      paginas = response.data[1].last_page;
      loading.close();
    }
  }
  loading.close();
}

const expande = async (draggingNode) => {
  mostrar.value = true;
  if (draggingNode.children.length === 0) {
    const loading = ElLoading.service({
      lock: true,
      text: "Loading",
      spinner: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><path fill="none" d="M0 0h200v200H0z"></path><path fill="none" stroke-linecap="round" stroke="#53B0FF" stroke-width="15" transform-origin="center" d="M70 95.5V112m0-84v16.5m0 0a25.5 25.5 0 1 0 0 51 25.5 25.5 0 0 0 0-51Zm36.4 4.5L92 57.3M33.6 91 48 82.7m0-25.5L33.6 49m58.5 33.8 14.3 8.2"><animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="1.1" values="0;-120" keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform></path><path fill="none" stroke-linecap="round" stroke="#53B0FF" stroke-width="15" transform-origin="center" d="M130 155.5V172m0-84v16.5m0 0a25.5 25.5 0 1 0 0 51 25.5 25.5 0 0 0 0-51Zm36.4 4.5-14.3 8.3M93.6 151l14.3-8.3m0-25.4L93.6 109m58.5 33.8 14.3 8.2"><animateTransform type="rotate" attributeName="transform" calcMode="spline" dur="1.1" values="0;120" keyTimes="0;1" keySplines="0 0 1 1" repeatCount="indefinite"></animateTransform></path></svg>`,
      backgroud: 'rgba(255,255,255,0.85)',
    });

    const response = await axios.get(`/cargar-hijos-nomArb/${draggingNode.id}`);

    if (response.status === 200) {
      tablaNomenclatura.value = response.data[1];
      tablaReferencias.value = response.data[2];
      if (draggingNode.children.length === 0) {
        for (let i = 0; i < response.data[0].length; i++) {
          draggingNode.children.push(response.data[0][i]);
        }
      }
    }
    loading.close();
  }
}

const handleNodeRightClick = () => {

}

</script>

<template>
  <CuerpoGen :tituloPag="'Nombre_Taxón'" :tituloArea="'Catálogo de nombres taxonómicos'">
    <div class="common-layout">
      <el-container style="min-height: 100vh; display: flex; flex-direction: column; border: 1px solid #eee">
        <el-header>
          <div>
            <el-row :gutter="10">
              <el-col :xs="8" :sm="6" :md="4" :lg="3" :xl="1">
                <div>
                  <el-tooltip class="item" effect="dark" content="Selección Catálogo de Grupos taxonómicos"
                    placement="left-start">
                    <el-button @click="filtro_Catalogos()" type="primary" round
                      class="w-full sm:w-auto px-4 py-2 text-xs sm:text-sm md:text-base lg:text-lg">
                      <el-icon class="icono">
                        <Setting />
                      </el-icon>
                      <span class="hidden sm:inline">Catálogo de grupos taxonómicos</span>
                    </el-button>
                  </el-tooltip>
                </div>
              </el-col>
            </el-row>
            <br>
            <el-row>
              <div class="demo-collapse">
                <el-collapse v-model="activeNames" @change="handleChange">
                  <el-collapse-item title="Grupos taxonómicos seleccionados" name="1">
                    <el-card style="width: 100%; max-width: 1500px;" shadow="always">
                      <el-row>
                        <span class="demo-input-label">Catálogo(s)</span>
                        <el-input type="textarea" autosize placeholder="Catálogos" v-model="catalogos" :disabled="true">
                        </el-input>
                      </el-row>
                      <el-row>
                        <span class="demo-input-label">Grupo SCAT</span>
                        <el-input type="textarea" :rows="2" placeholder="Grupo SCAT" v-model="grupos" :disabled="true">
                        </el-input>
                      </el-row>
                    </el-card>
                  </el-collapse-item>
                </el-collapse>
              </div>
            </el-row>
            <br>
            <el-row :gutter="20" style="display: flex; flex-wrap: wrap;">
              <!-- Primera columna -->
              <el-col :xs="24" :sm="12" :md="6" style="display: flex; flex-direction: column;">
                <span>Ir a:</span>
                <el-input clearable placeholder="" v-model="filterText" @change="filterNode">
                </el-input>
              </el-col>

              <!-- Segunda columna -->
              <el-col :xs="24" :sm="12" :md="6" style="display: flex; flex-direction: column;">
                <span class="block">Nivel taxonómico</span>
                <el-cascader :options="categoriasTax" clearable filterable v-model="categ"
                  placeholder="Nivel taxonómico" @change="handleChange">
                </el-cascader>
              </el-col>
            </el-row>
          </div>
          <br>
          <div style="flex-grow: 1;">
            <el-container style="height: 100%; border: 1px solid #eee">
              <el-aside width="750px" style="background-color: rgb(238, 241, 246); min-height: 100%; overflow:auto;">
                <div class="d-table-cell">
                  <el-tree class="filter-tree" style="height: 100%; overflow: auto;" :data="data" node-key="id"
                    @node-click="expande" :expand-on-click-node="true" :filter-node-method="filterNode" allowDrag=true
                    :draggable="true" empty-text='' ref="tree" :highlight-current="false" :props="defaultProps"
                    @node-contextmenu="handleNodeRightClick">
                    <template #default="{ node }">
                      <div class="tree-node-wrapper">
                        <Logo class="tree-node-logo" :rutaCategoria="node.data.completo.categoria.RutaIcono" />
                        <el-tooltip content="Información">
                          <el-icon @click.stop="openDialog(node.data)">
                            <!-- Agregamos el @click.stop y el openDialog -->
                            <InfoFilled />
                          </el-icon>
                        </el-tooltip>
                        <el-tooltip class="item" effect="dark" content="Mover" placement="right">
                          <span :style="{ color: node.color }" :id="`node-${node.id}`">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                              class="bi bi-diagram-3-fill" viewBox="0 0 16 16" @click="mover(node)">
                              <path fill-rule="evenodd"
                                d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H14a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 2 7h5.5V6A1.5 1.5 0 0 1 6 4.5zm-6 8A1.5 1.5 0 0 1 1.5 10h1A1.5 1.5 0 0 1 4 11.5v1A1.5 1.5 0 0 1 2.5 14h-1A1.5 1.5 0 0 1 0 12.5zm6 0A1.5 1.5 0 0 1 7.5 10h1a1.5 1.5 0 0 1 1.5 1.5v1A1.5 1.5 0 0 1 8.5 14h-1A1.5 1.5 0 0 1 6 12.5zm6 0a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1A1.5 1.5 0 0 1-1.5-1.5z" />
                            </svg>
                          </span>
                        </el-tooltip>
                        <span class="tree-node-label">{{ node.label }}</span>
                      </div>
                    </template>
                  </el-tree>
                </div>
              </el-aside>
            </el-container>
          </div>
        </el-header>
      </el-container>
    </div>
  </CuerpoGen>
  <DialogForm v-model="dialogFormVisibleCat" :botCerrar="false" :pressEsc="false">
    <FiltroGrupos :grupos="gruposTax" @cerrar="cerrarDialog" @regresaGrupos="recibeGrupos" />
  </DialogForm>

  <DialogForm v-model="dialogFormVisibleAlta" @close="closeDialog" @reset-form="resetFormNombre" :botCerrar= "true" :pressEsc= "true">
    <FormNombre :taxonAct="taxonAct" :infTaxon="infTaxon" :muestraGuardar="muestraGuardar"
        :paginaActual="1" :categoria="catego" :catalogos="idsGrupos"  />
</DialogForm>
</template>

<style scoped>
.icono {
  margin-right: 8px;
  /* Ajusta el espacio */
}

.custom-tree-node {
  align-items: center;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  width: 50%;
  overflow: auto;
}

/*Aqui se aplican los estilos para que el arbol cambie el color de la letra y sombreado del arbol*/
.filter-tree .el-tree-node.is-current>.el-tree-node__content {
  background-color: rgb(203, 233, 200) !important;
  /* Color de fondo del nodo seleccionado */
  color: rgb(0 17 255) !important;
  /* Color del texto del nodo seleccionado */
}

.filter-tree .el-tree-node.is-current .el-tree-node__children {
  background-color: transparent !important;
  /* Sin fondo en los nodos hijos */
  color: inherit !important;
  /* Mantener el color original de los hijos */
}

.highlight-node {
  color: #a52f2f !important;
  /* Fondo */
}

.greenClass {
  background: rgb(90, 177, 90);
}

.redClass {
  background: rgb(226, 119, 119);
}

.context-menu {
  display: block !important;
  visibility: visible !important;
  position: absolute;
  z-index: 9999;
  background-color: hsl(223, 41%, 93%);
  border: 1px solid #dcdfe6;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
  padding: 20px;
  /* Añade un relleno para que no se vea tan estrecho */
  min-width: 190px;
  /* Ajusta el ancho mínimo */
  height: auto;
  /* Asegúrate de que la altura se ajuste al contenido */
  box-sizing: border-box;
  /* Asegura que el padding no afecte el ancho del menú */
}

.el-menu-item {
  padding: 4px 12px;
  /* Reduce el padding vertical a la mitad */
  font-size: 14px;
  /* Ajusta el tamaño del texto si es necesario */
  line-height: 16px;
  /* Asegúrate de que la línea del texto sea más compacta */
  height: auto;
  /* Asegura que la altura se ajuste automáticamente */
}

.menu-item-submenu {
  padding: 4px 12px;
  /* Reduce el padding */
  font-size: 14px;
  /* Ajusta el tamaño del texto */
  line-height: 16px;
  /* Línea del texto compacta */
  height: auto;
  /* Ajusta la altura automáticamente */
}

.el-submenu .el-menu-item {
  padding: 4px 12px;
  /* Aplica el mismo padding a los items del submenu */
  font-size: 14px;
  /* Asegura que el texto tenga el mismo tamaño */
  line-height: 16px;
  /* Línea del texto compacta */
  height: auto;
  /* Ajusta la altura automáticamente */
}

.el-submenu__title {
  padding: 4px 12px;
  /* Aplica el mismo padding a la cabecera del submenu */
  font-size: 14px;
  /* Asegura que el texto tenga el mismo tamaño */
  line-height: 16px;
  /* Línea del texto compacta */
  height: auto;
  /* Ajusta la altura automáticamente */
}

.icon-style {
  width: 16px;
  height: 16px;
  fill: currentColor;
  /* Asegura que el color sea el mismo */
  margin-right: 2px;
  /* Espacio a la derecha */
  vertical-align: middle;
  /* Alineación vertical */
}

.el-icon {
  font-size: 16px;
  /* Ajusta el tamaño de la fuente */
  margin-right: 2px;
  vertical-align: middle;
}

/* --------------------------------------------------------- */

.flex-container {
  display: flex;
  align-items: center;
  /* Centra verticalmente */
  gap: 8px;
  /* Espacio entre el texto y el input */
  flex-wrap: wrap;
  /* Permite que los elementos bajen si no hay espacio */
}

.flex-container span {
  white-space: nowrap;
  /* Evita que el texto se divida en varias líneas */
}

.el-input,
.el-cascader {
  flex: 1;
  /* Hace que los inputs ocupen el espacio disponible */
  min-width: 150px;
  /* Evita que se vuelvan demasiado pequeños */
}

.tree-node-wrapper {
  display: flex;
  align-items: center;
  /* Alinea verticalmente los elementos */
  gap: 8px;
  /* Espacio entre el ícono y el texto */
  white-space: nowrap;
  /* Evita que el texto se divida en varias líneas */
}

.tree-node-logo {
  width: 20px;
  /* Ajusta según el tamaño de tus íconos */
  height: 20px;
  flex-shrink: 0;
  /* Evita que el ícono se reduzca de tamaño */
}

.tree-node-label {
  font-size: 14px;
  line-height: 20px;
  /* Ajusta el alto del texto para que coincida con el ícono */
}

.el-tree-node:hover {
  background-color: transparent !important;
}
</style>