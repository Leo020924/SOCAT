<template>
  <div class="form-nombre-container">
    <el-form :model="nombreTax" :rules="rules" ref="nombreTax" label-width="180px">
      <el-container>
        <el-header class="form-header">
          <el-row justify="space-between" align="middle">
            <el-col :span="auto">
              <el-tooltip class="item" effect="dark" content="Ingresar taxón descendente" placement="top">
                <el-button circle type="primary" @click.prevent="nuevoTax()" :disabled="habActTax">
                  <el-icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                      class="bi bi-box-arrow-in-down" viewBox="0 0 16 16">
                      <path fill-rule="evenodd"
                        d="M3.5 6a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 1 0-1h2A1.5 0 0 1 14 6.5v8a1.5 0 0 1-1.5 1.5h-9A1.5 0 0 1 2 14.5v-8A1.5 0 0 1 3.5 5h2a.5 0 0 0 1h-2z" />
                      <path fill-rule="evenodd"
                        d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5 0 1 0-.708.708l3 3z" />
                    </svg>
                  </el-icon>
                </el-button>
              </el-tooltip>
            </el-col>

            <el-col :span="auto">
              <el-tooltip class="item" effect="dark" content="Modificar" placement="bottom">
                <el-button circle type="success" @click.prevent="editarTax()" :disabled="habModTax">
                  <el-icon>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                      class="bi bi-pencil" viewBox="0 0 16 16">
                      <path
                        d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5 0 0 1-.168.11l-5 2c-.186.74-.526 1.205-1.032 1.41a.5 0 0 1-.118-.168l2-5a.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.625v.75a.5 0 0 0 .5.5h.75l6.425-6.425a.5 0 0 0-.707-.707z" />
                    </svg>
                  </el-icon>
                </el-button>
              </el-tooltip>
            </el-col>

            <el-col :span="auto">
              <el-popconfirm confirm-button-text='Si' cancel-button-text='No' icon="el-icon-info" icon-color="red"
                title="¿Realmente desea eliminar la información?" @confirm="borrarDatos()">
                <template #reference>
                  <el-tooltip class="item" effect="dark" content="Eliminar" placement="bottom">
                    <el-button circle type="danger" :disabled="borrarAct">
                      <el-icon>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                          class="bi bi-trash" viewBox="0 0 16 16">
                          <path
                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5 6v6a.5 0 0 1-1 0V6a.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 1v6a.5 0 0 0 1 0V6a.5 0 0 0 0-1Z" />
                          <path
                            d="M13 1a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1H1v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V1h-2Zm-2 0a.5 .5 0 0 1 .5.5v2a.5 .5 0 0 1-.5.5H4.5a.5 0 0 1-.5-.5v-2a.5 0 0 1 .5-.5H11Zm-8 4a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0v-8a.5.5 0 0 1 .5-.5Zm7 0a.5 .5 0 0 1 .5.5v8a.5 0 0 1-1 0v-8a.5 0 0 1 .5-.5Z" />
                        </svg>
                      </el-icon>
                    </el-button>
                  </el-tooltip>
                </template>
              </el-popconfirm>
            </el-col>

            <el-col :span="auto">
              <el-popconfirm confirm-button-text='Si' cancel-button-text='No' icon="el-icon-info" icon-color="#E6A23C"
                title="¿Realmente desea guardar los cambios?" @confirm="Guardar('nombreTax', accion)">
                <template #reference>
                  <el-tooltip class="item" effect="dark" content="Guardar" placement="bottom">
                    <el-button v-show="muestraGrd" circle type="warning">
                      <el-icon>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                          class="bi bi-usb-drive" viewBox="0 0 16 16">
                          <path
                            d="M6 .5a.5 0 0 1 .5-.5h4a.5 0 0 1 .5.5v4H6v-4ZM7 1v1h1V1H7Zm2 0v1h1V1H9ZM6 5a1 1 0 0 0-1 1v8.5A1.5 0 0 0 6.5 16h4a1.5 0 0 0 1.5-1.5V6a1 1 0 0 0-1-1H6Zm0 1h5v8.5a.5 0 0 1-.5.5h-4a.5 0 0 1-.5-.5V6Z" />
                        </svg>
                      </el-icon>
                    </el-button>
                  </el-tooltip>
                </template>
              </el-popconfirm>
            </el-col>

            <el-col :span="auto">
              <el-form-item label="Nivel taxonómico" prop="catTax" style="margin-bottom: 0;">
                <el-select v-model="nombreTax.catTax" placeholder="Nivel taxonómico" :disabled="nivelAct"
                  style="width: 200px;">
                  <el-option v-for="item in categorias" :key="item.id" :label="item.label" :value="item.id">
                  </el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
        </el-header>
        <el-main>
          <p>Taxón seleccionado: {{ taxonAct?.completo?.NombreCompleto }}</p>
          <el-form-item label="Estatus: " prop="estatusTax">
            <div>
              <el-radio-group v-model.lazy="nombreTax.estatusTax" @change="CambioEstatus()">
                <el-radio :disabled="estCor" :label="2">Correcto</el-radio>
                <el-radio :disabled="estSin" :label="1">Sinónimo</el-radio>
                <el-radio :disabled="estNa" :label="6">ND</el-radio>
                <el-radio :disabled="estNd" :label="-9">NA</el-radio>
              </el-radio-group>
            </div>
          </el-form-item>
          <p></p>
          <el-tabs type="card">
            <el-tab-pane label="Taxón">

              <!-- El resto del formulario -->
              <el-form-item label="Taxón" prop="nombreTaxon">
                <el-input type="text" maxlength="100" placeholder="Nombre taxón" show-word-limit :disabled="habAltEdic"
                  :value="props.taxonAct?.completo?.NombreCompleto" />
              </el-form-item>

              <el-form-item label="Nombre autoridad" prop="nombreAutoridad">
                <div class="d-flex">
                  <el-input type="textarea" :rows="2" maxlength="255" show-word-limit placeholder="Nombre autoridad"
                    @keydown.native="onPressSistC" :disabled="true"
                    :value="props.taxonAct?.completo?.NombreAutoridad" />
                  <el-button type="primary" circle style="background-color: #985ede; border-color: #985ede;"
                    @click="abrirAutorTaxon">
                    <el-icon>
                      <User />
                    </el-icon>
                  </el-button>
                </div>
              </el-form-item>

              <el-form-item label="Sist. Clas. / Catálogo de autoridad / Diccionario" prop="sistClassDicc">
                <el-input type="text" maxlength="255" show-word-limit placeholder="Sistema de clasificación"
                  @keydown.native="onPressSistC" :disabled="habAltEdic"
                  :value="props.taxonAct?.completo?.SistClasCatDicc" />
              </el-form-item>

              <el-form-item label="Cita nomenclatural" prop="citaNomenclatural">
                <el-input type="text" maxlength="255" show-word-limit placeholder="Cita nomenclatural"
                  @keydown.native="onPressSistC" :disabled="habAltEdic"
                  :value="props.taxonAct?.completo?.CitaNomenclatural" />
              </el-form-item>

              <el-form-item label="Anotación al taxón" prop="anotacionTaxon">
                <el-input type="textarea" :rows="3" maxlength="1650" show-word-limit placeholder="Anotación al taxón"
                  @keydown.native="onPressSistC" :disabled="habAltEdic" :value="props.taxonAct?.completo?.Anotacion" />
              </el-form-item>

              <el-form-item label="Número filogenético" prop="numeroFilogenetico">
                <el-input type="text" maxlength="50" show-word-limit placeholder="Número filogenético"
                  @keydown.native="onPressSistC" :disabled="habAltEdic"
                  :value="props.taxonAct?.completo?.NumeroFilogenetico" />
              </el-form-item>
            </el-tab-pane>

            <el-tab-pane label="SCAT">
              <el-row :gutter="20">
                <el-col :span="13">
                  <table style="border: 1px solid black; width: 100%;">
                    <tbody>
                      <tr style="border: 1px solid black;">
                        <th style="border: 1px solid black;">IdNombre</th>
                        <th style="border: 1px solid black;">IDCat</th>
                      </tr>
                      <tr>
                        <td style="border: 1px solid black;">{{ idNombre }}</td>
                        <td style="border: 1px solid black;">{{ idCat }}</td>
                      </tr>
                    </tbody>
                  </table>
                </el-col>
                <div>
                  <el-form-item label="Grupo" prop="grpSelec">
                    <el-select v-model="nombreTax.grpSelec" placeholder="Select" :disabled="actGrupo">
                      <el-option v-for="item in listGrp" :key="item.id" :label="item.label" :value="item.id">
                      </el-option>
                    </el-select>
                  </el-form-item>
                </div>
                <el-col :span="1">
                  <el-tooltip class="item" effect="dark" content="Catálogo de Grupos taxonómicos"
                    placement="left-start">
                    <el-button @click="carga_Grupos()" round size="small" icon="el-icon-connection" type="warning">
                    </el-button>
                  </el-tooltip>
                </el-col>
              </el-row>
              <p></p>
              <el-row :gutter='25'>
                <el-col :span="7">
                  <div>
                    Validación SNIB
                    <el-select v-model="valSnib" placeholder="" :disabled="autorAct">
                      <el-option v-for="item in opcSnib" :key="item.id" :label="item.label" :value="item.id">
                      </el-option>
                    </el-select>
                  </div>
                </el-col>
                <div>
                  <el-col :span="10">
                    <div>
                      <el-form-item label="Nivel de revisión" prop="nivelRev">
                        <div>
                          <el-select v-model="nombreTax.nivelRev" placeholder="Nivel de revisión" :disabled="autorAct">
                            <el-option v-for="item in opcNivRev" :key="item.id" :label="item.label" :value="item.value">
                            </el-option>
                          </el-select>
                        </div>
                      </el-form-item>
                    </div>
                  </el-col>
                </div>
                <el-col :span="7">
                  <div>
                    <el-row>
                      <el-form-item label="Publico" prop="estado">
                        <div>
                          <el-radio-group v-model="nombreTax.estado" :disabled="autorAct">
                            <el-radio :disabled="estPubS" :label="1">Si</el-radio>
                            <el-radio :disabled="estPubN" :label="0">No</el-radio>
                          </el-radio-group>
                        </div>
                      </el-form-item>
                    </el-row>
                  </div>
                </el-col>
              </el-row>
              <p></p>
              <el-row :gutter='25'>
                <el-col :span='5'>
                  <el-row>Id Invasora</el-row>
                  <el-row>
                    <el-input placeholder="Id Invasora" v-model="idInvasora" @keydown.native="onPressSistC"
                      :disabled="autorAct" :value="props.taxonAct?.completo?.scat?.ListaInvasoras" />
                  </el-row>
                </el-col>
                <el-col :span='6'>
                  <el-row>Comentarios SNIB</el-row>
                  <el-row>
                    <el-input placeholder="Comentarios SNIB" v-model="comentariosSnib" @keydown.native="onPressSistC"
                      :disabled="true" />
                  </el-row>
                </el-col>
                <el-col :span='2'>
                  <el-tooltip class="item" effect="dark" content="Total de comentarios al nombre en el SNIB"
                    placement="left-start">
                    <el-button @click="comentarios_Snib()" round size="small" icon="el-icon-chat-dot-square"
                      type="success" :disabled="comDet">
                    </el-button>
                  </el-tooltip>
                </el-col>
              </el-row>
              <p></p>
              <el-row :gutter='25'>
                <el-col :span="15">
                  <p></p>
                  <el-row>
                    Homonimia SNIB
                  </el-row>
                  <el-row>
                    <el-input type="input" placeholder="Homonimia SNIB" v-model="homonimiaSnib"
                      @keydown.native="onPressSistC" :disabled="autorAct" />
                  </el-row>
                </el-col>
              </el-row>
            </el-tab-pane>
          </el-tabs>
        </el-main>
      </el-container>
    </el-form>
    <template>
      <div>
        <el-dialog v-model="dialogFormVisibleAutor" :z-index="currentZIndex" :close-on-click-modal="false"
          @update:modelValue="dialogFormVisibleAutor = $event">
          <AutorTaxon @cerrar="dialogFormVisibleAutor = false" :visible="dialogFormVisibleAutor" :nombre="true"
            :autorTax="listAutorTax" :taxActual="taxonAct?.completo?.NombreCompleto" :botCerrar="true"
            :pressEsc="true" />
        </el-dialog>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { ElMessage, ElInput } from 'element-plus'
import axios from 'axios';
import { User } from '@element-plus/icons-vue';
import AutorTaxon from '@/Pages/Socat/Autores/AutorTaxon.vue';

const page = usePage();


const hasPermisos = (permiso, accion) => {
  const permisosData = page.props.permisos || [];
  return permisosData.some(p => p.permiso === permiso && p.accion === accion);
};

const abrirAutorTaxon = () => {
  dialogFormVisibleAutor.value = true;
};

const cerrarAutorTaxon = () => {
  dialogFormVisibleAutor.value = false;
};

const props = defineProps({
  autTaxEdit: [],
  taxonAct: {
    type: Object,
    default: () => ({
      completo: {  // Eliminamos la capa "data" innecesaria
        NombreCompleto: '',
        NombreAutoridad: '',
        SistClasCatDicc: '',
        CitaNomenclatural: '',
        Anotacion: '',
        NumeroFilogenetico: '',
        IdNombre: '',
        scat: {
          IDCAT: '',
          ListaInvasoras: ''
        },
        categoria: {
          NombreCategoriaTaxonomica: '',
        },
        IdNivel2: 0,
        Estatus: 0,
        rel_nombre_autor: [],
        nombre_rel: [],
        hijos: [],
        rel_nombre_cat: [],
        autorTaxComp: [],
      }
    })
  },
  infTaxon: {
    type: Number,
    default: 0
  },
  muestraGuardar: {
    type: Boolean,
    default: false
  },
  paginaActual: Number,
  categoria: [],
  catalogos: []
});

console.log("Datos recibidos en FormNombre:", props.taxonAct); // Agrega esto

const muestraGrd = ref(false);
const currentZIndex = ref(1000);
const usuario = ref([]);
const estCor = ref(true);
const estSin = ref(true);
const estNa = ref(true);
const estNd = ref(true);
const estPubS = ref(true);
const estPubN = ref(true);
const actGrupo = ref(true);
const estDinamico = ref("");
const categorias = ref([]);
const listGrp = ref([]);
const listAutorTax = ref([]);
const opcSnib = ref([{
  id: 'total',
  label: 'Total'
}, {
  id: 'vínculo',
  label: 'Vínculo'
}, {
  id: 'total-vínculo',
  label: 'Total-Vínculo'
}
]);

const opcNivRev = ref([{
  value: 'A',
  label: 'A - Revisado y publicado por especialista'
}, {
  value: 'B',
  label: 'B - Revisado [no publicado] por especialista'
}, {
  value: 'C',
  label: 'C - Compilación de literatura especializada (recientemente publicado)'
}, {
  value: 'C2',
  label: 'C - Compilación de literatura especializada (no reciente)'
}, {
  value: 'D',
  label: 'D - Literatura diversa (incluye referencias ecológicas)'
}, {
  value: 'E',
  label: 'E - Lista tipo checklist'
}
]);
const Observaciones = ref('');
const comentariosSnib = ref('');
const idInvasora = ref('');
const homonimiaSnib = ref('');
const comDet = ref(true);
const accion = ref('');
const valSnib = ref('');
const categ = ref('');
const idNombre = ref('');
const idNom = ref(0);
const idCat = ref('');
const nivelAct = ref(true);
const autorAct = ref(true);
const borrarAct = ref(true);
const habAltEdic = ref(props.habAltEdic_prop)
const habActTax = ref(false);
const habModTax = ref(false);
const dialogFormVisibleGrupos = ref(false);
const dialogFormVisibleComentarios = ref(false);
const dialogFormVisibleAutor = ref(false);

const nombreTax = ref({
  nombreTaxon: '',
  nombreAutoridad: '',
  sistClassDicc: '',
  citaNomenclatural: '',
  anotacionTaxon: '',
  numeroFilogenetico: '',
  catTax: '',
  estatusTax: '',
  estado: '',
  grpSelec: [],
  nivelRev: '',
});

const rules = ref({
  nombreTaxon: [{
    required: true,
    message: 'Ingrese un nombre de taxón',
    trigger: 'blur'
  },
  {
    min: 1,
    max: 100,
    message: 'El tamaño debe ser entre 1 y 100',
    trigger: 'blur'
  }],
  nombreAutoridad: [{
    required: true,
    message: 'Ingrese un nombre de autoridad',
    trigger: 'blur'
  },
  {
    max: 255,
    message: 'El tamaño debe ser menor o igual a 255 caracteres',
    trigger: 'blur'
  }],
  sistClassDicc: [{
    required: true,
    message: 'Ingrese un sistema de claseficación',
    trigger: 'blur'
  },
  {
    max: 255,
    message: 'El tamaño debe ser menor o igual a 255 caracteres',
    trigger: 'blur'
  }],
  citaNomenclatural: [{
    max: 255,
    message: 'El tamaño debe ser menor o igual a 255 caracteres',
    trigger: 'blur'
  }],
  anotacionTaxon: [{
    max: 255,
    message: 'El tamaño debe ser menor o igual a 1650 caracteres',
    trigger: 'blur'
  }],
  numeroFilogenetico: [{
    max: 255,
    message: 'El tamaño debe ser menor o igual a 50 caracteres',
    trigger: 'blur'
  }],
  catTax: [{
    required: true,
    message: 'Se debe seleccionar una categoria taxonomica',
    trigger: 'change'
  }],
  estatusTax: [{
    required: true,
    message: 'Debe seleccionar el estatus',
    trigger: 'change'
  }],
  estado: [{
    required: true,
    message: 'Debe seleccionar el estado',
    trigger: 'change'
  }],
  grpSelec: [{
    required: true,
    message: 'Se debe seleccionar un grupo',
    trigger: 'change'
  }],
  nivelRev: [{
    required: true,
    message: 'Se debe seleccionar nivel de revisión',
    trigger: 'change'
  }]
});

onMounted(() => {
  try {
    usuario.value = page.props.value?.auth.user;
  } catch (error) {
    console.error('Error al obtener el usuario: ', error);
  }
  // Asigna valores iniciales desde taxonAct
  nombreTax.value.catTax = props.taxonAct?.completo?.NombreCategoriaTaxonomica
  nombreTax.value.estatusTax = props.taxonAct?.completo?.Estatus; // Inicializa estatusTax
  idCat.value = props.taxonAct?.completo?.scat?.IDCAT
  idNombre.value = props.taxonAct?.completo.IdNombre;
  nombreTax.value.estado = props.taxonAct.completo.Publico;

});

const autTaxComp = ref('')

watch(
  () => page.props.value?.autorTaxComp,
  (newVal, oldVal) => {
    if (newVal !== oldVal) {
      autTaxComp.value = newVal;
      muestraFiltros();
    }
  },
  { immediate: true } // Ejecuta el watch inmediatamente al montar el componente
);

watch(() => props.taxonAct, (newTaxonAct) => {
  // Actualizar los valores del formulario
  nombreTax.value.nombreTaxon = newTaxonAct?.completo?.NombreCompleto || '';
  nombreTax.value.nombreAutoridad = newTaxonAct?.completo?.NombreAutoridad || '';
  nombreTax.value.sistClassDicc = newTaxonAct?.completo?.SistClasCatDicc || '';
  nombreTax.value.citaNomenclatural = newTaxonAct?.completo?.CitaNomenclatural || '';
  nombreTax.value.anotacionTaxon = newTaxonAct?.completo?.Anotacion || '';
  nombreTax.value.numeroFilogenetico = newTaxonAct?.completo?.NumeroFilogenetico || '';
});




const carga_inicio = () => {
  muestraGrd.value = props.muestraGuardar;
  estCor.value = true;
  estSin.value = true;
  estNa.value = true;
  estNd.value = true;
  nombreTax.value.nombreTaxon = props.taxonAct?.data?.completo?.NombreCompleto || '';
  nombreTax.value.nombreAutoridad = props.taxonAct?.data?.completo?.NombreAutoridad || '';
  nombreTax.value.sistClassDicc = props.taxonAct?.data?.completo?.SistClasCatDicc || '';
  nombreTax.value.citaNomenclatural = props.taxonAct?.data?.completo?.CitaNomenclatural || '';
  nombreTax.value.anotacionTaxon = props.taxonAct?.data?.completo?.Anotacion || '';
  nombreTax.value.numeroFilogenetico = props.taxonAct?.data?.completo?.NumeroFilogenetico || '';
  idNombre.value = props.taxonAct?.data?.completo?.IdNombre || '';
  idCat.value = props.taxonAct?.data?.completo?.scat?.IDCAT || '';
  idInvasora.value = props.taxonAct?.data?.completo?.scat.ListaInvasoras || '';
  nivelAct.value = true;
  autorAct.value = true;
  borrarAct.value = false;
  habAltEdic.value = true;
  comDet.value = true;
  cargaListGrp();
  cargaValSnib();
  cargaNivRev();
  cargaComSnib();
  validaHijos();
  comDet.value = false;
  borrarAct.value = false;
  habAltEdic.value = true;
  nivelAct.value = true;
  autorAct.value = true;
  habActTax.value = false;
  habModTax.value = false;
  cargaCategorias();
  nombreTax.value.estatusTax = props.taxonAct?.data?.completo?.Estatus || '';
  nombreTax.value.estado = props.taxonAct?.data?.completo?.scat?.Publico || '';

  if (props.taxonAct?.data?.completo?.IdNivel2 === 1) {
    estDinamico.value = "Valido";
  } else {
    estDinamico.value = "Correcto";
  }

  if (props.taxonAct?.data?.completo?.rel_nombre_autor?.length === 0 && props.infTaxon != 0) {
    ElMessage({
      showClose: true,
      message: 'Recuerde que se debe actualizar los autores para generar la relación entre nombre y autor',
      type: 'warning',
      duration: '3000'
    })
  }
};

const carga_Grupos = () => {
  dialogFormVisibleGrupos.value = true;
};

const comentarios_Snib = () => {
  dialogFormVisibleComentarios.value = true;
};

const cargaListGrp = () => {
  let api = window.location.origin
  axios.get(`${api}/carga-list-grp`).then((response) => {
    listGrp.value = response.data;

    let idGrp = props.taxonAct?.data?.completo?.scat?.IdGrupoSCAT;

    const resp = listGrp.value.find(grupo => grupo.id === idGrp);

    if (resp) {
      nombreTax.value.grpSelec = resp.label
    }

  });
};

const cargaCategorias = () => {
  let params = {
    idNombre: props.taxonAct?.data?.completo?.IdCategoriaTaxonomica,
    IdNivel1: props.taxonAct.data.completo.categoria.IdNivel1,
    IdNivel2: props.taxonAct.data.completo.categoria.IdNivel2
  };
  let api = window.location.origin
  axios.get(`${api}/carga-categ`, {
    params
  }).then((response) => {

    categorias.value = response.data;
    categorias.value.push({
      id: props.taxonAct.data.completo.categoria.IdCategoriaTaxonomica,
      label: props.taxonAct.data.completo.categoria.NombreCategoriaTaxonomica
    });
  });

  nombreTax.value.catTax = props.taxonAct?.data?.completo?.categoria?.NombreCategoriaTaxonomica
};

const cargaValSnib = () => {

  if (props.taxonAct.data.completo.scat) {
    let valSnibValue = props.taxonAct.data.completo.scat.ValidacionSNIB;
    let resp = opcSnib.value.find(niv => niv.id === valSnibValue);

    if (typeof resp === 'undefined') {
      valSnib.value = '';
    } else {
      valSnib.value = resp.label;
    }
  }


};

const cargaNivRev = () => {

  if (props.taxonAct.data.completo.scat) {
    let nivRev = props.taxonAct.data.completo.scat.Nivel_de_revision;

    let resp = opcNivRev.value.find(niv => niv.value === nivRev);

    if (typeof resp === 'undefined') {
      nivRev.value = '';
    } else {

      nombreTax.value.nivelRev = resp.label;
    }
  }
};

const cargaComSnib = () => {

  if (props.taxonAct.data.completo.scat) {
    homonimiaSnib.value = props.taxonAct.data.completo.scat.HomonimiaSNIB;
    idInvasora.value = props.taxonAct.data.completo.scat.ListaInvasoras;

  }

  let params = {
    idNombre: props.taxonAct.data.id
  };
  let api = window.location.origin
  axios.get(`${api}/cargar-comSnib`, {
    params
  }).then((response) => {

    comentariosSnib.value = response.data;

    if (comentariosSnib.value > 0) {
      comDet.value = false;
    } else {
      comDet.value = true;
    }
  });
};

const nuevoTax = () => {
  muestraGrd.value = true;
  if (estatus.value === 4) {
    ElMessage({
      showClose: true,
      message: 'No es posible asignarle taxones descendentes',
      type: 'error',
      duration: '1500'
    });
    nuevoTax.break();
  }

  if (props.taxonAct.data.completo.categoria.IdNivel1 < 5) {
    actGrupo.value = false;
  }

  nombreTax.value.estatusTax = '';
  nivelAct.value = false;
  autorAct.value = false;
  nombreTax.value.catTax = '';
  valSnib.value = '';

  AltaEstatus();

  let params = {
    idNombre: props.taxonAct.data.completo.IdCategoriaTaxonomica,
    IdNivel1: props.taxonAct.data.completo.categoria.IdNivel1,
    IdNivel2: props.taxonAct.data.completo.categoria.IdNivel2
  };
  let api = window.location.origin
  axios.get(`${api}/carga-categ`, {
    params
  }).then((response) => {

    categorias.value = response.data;

  });

  nombreTax.value.nombreTaxon = '';
  nombreTax.value.nombreAutoridad = '';
  nombreTax.value.sistClassDicc = '';
  nombreTax.value.citaNomenclatural = '';
  nombreTax.value.anotacionTaxon = '';
  nombreTax.value.numeroFilogenetico = '';
  idInvasora.value = '';
  homonimiaSnib.value = '';
  idNombre.value = '';
  idCat.value = '';
  grpSelec.value = '';
  comentariosSnib.value = '';
  comDet.value = true;
  borrarAct.value = true;
  habAltEdic.value = false;
  habActTax.value = true;
  habModTax.value = true;
  accion.value = 'crear';
  idNom.value = taxonAct.value.data.completo.IdNombre;
  estPubS.value = false;
  estPubN.value = false;
  estCor.value = false;
  estSin.value = false;
  estNa.value = false;
  estNd.value = false;
};

const editarTax = () => {
  muestraGrd.value = true;
  comDet.value = true;
  borrarAct.value = true;
  habAltEdic.value = false;
  habActTax.value = true;
  habModTax.value = true;
  autorAct.value = false;
  accion.value = 'editar';
  idNom.value = taxonAct.value.data.completo.IdNombre;
  nombreTax.value.nivelRev = taxonAct.value.data.completo.scat.Nivel_de_revision;
  estPubS.value = false;
  estPubN.value = false;
  estCor.value = false;
  estSin.value = false;
  estNa.value = false;
  estNd.value = false;
};


const CambioEstatus = () => {
  let params;

  params = {
    estatusInicio: props.taxonAct.completo.Estatus,
    nomAct: props.taxonAct.id
  };

  axios.get("/valCamEstatus", { params }).then((response) => {
    // Actualiza correctamente las variables ref
    switch (props.taxonAct.completo.Estatus) {
      case 1:
        estCor.value = response.data != 1 ? true : false;
        estSin.value = false;
        estNa.value = false;
        estNd.value = false;
        break;
      case 2:
        estCor.value = false;
        estSin.value = response.data != 1 ? false : true;
        estNa.value = false;
        estNd.value = false;
        break;
      case 6:
        estCor.value = response.data != 1 ? true : false;
        estSin.value = response.data != 1 ? true : false;
        estNa.value = false;
        estNd.value = false;
        break;
      case -9:
        estCor.value = response.data != 1 ? true : false;
        estSin.value = response.data != 1 ? true : false;
        estNa.value = false;
        estNd.value = false;
        break;
    }
  });
};

const borrarDatos = async () => {
  let rel = taxonAct.value.data.completo.nombre_rel.length;
  let hijos = taxonAct.value.data.completo.hijos.length;
  let catRel = taxonAct.value.data.completo.rel_nombre_cat.length;
  let numEjemp = parseInt(taxonAct.value.data.numEjemp);

  let mensaje = '<strong>El nombre no puede ser eliminado ya que tiene: </p> <br></strong>';

  if (numEjemp > 0) {
    if (numEjemp === 1) {
      mensaje += '<strong>' + numEjemp + ' ejemplar relacionado en el SNIB. </p> <br></strong>';
    } else {
      mensaje += '<strong>' + numEjemp + ' ejemplares relacionados en el SNIB. </p> <br></strong>';
    }
  }

  if (rel > 0) {
    if (rel === 1) {
      mensaje += '<strong>' + rel + ' taxón relacionado. </p> <br></strong>';
    } else {
      mensaje += '<strong>' + rel + ' taxones relacionados. </p> <br></strong>';
    }
  }


  if (hijos > 0) {
    if (hijos === 1) {
      mensaje += '<strong>' + hijos + ' taxón hijo </p> <br></strong>';
    } else {
      mensaje += '<strong>' + hijos + ' taxones hijos </p> <br></strong>';
    }
  }

  if (catRel > 0) {
    if (catRel === 1) {
      mensaje += '<strong>' + catRel + ' caracteristica relacionada </p> <br></strong>';
    } else {
      mensaje += '<strong>' + catRel + ' caracteristicas relacionadas </p> <br></strong>';
    }
  }

  if (comentariosSnib.value > 0) {

    if (comentariosSnib.value === 1) {
      mensaje += '<strong>' + comentariosSnib.value + ' comentario en el SNIB </p> <br></strong>';
    } else {
      mensaje += '<strong>' + comentariosSnib.value + ' comentarios en el SNIB </p> <br></strong>';
    }
  }

  if (rel > 0 || hijos > 0 || catRel > 0 || comentariosSnib.value > 0) {
    ElMessage({
      showClose: true,
      dangerouslyUseHTMLString: true,
      message: mensaje,
      type: 'error',
      duration: '3000'
    })
    return;
  }


  ElMessage({
    showClose: true,
    message: 'Falta implementar la lógica de borrado del taxón',
    type: 'info',
    duration: '3000'
  });
};

const carga_autor = () => {
  dialogFormVisibleAutor.value = true;
};

const muestraFiltros = () => {
  nombreTax.value.nombreAutoridad = autTaxComp.value;
};

const onPressSistC = async (e) => {
  let allowedKeys = [0, 1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
    21, 22, 23, 24, 25, 26, 27, 28, 29, 30
  ];

  if (allowedKeys.indexOf(e.keyCode) > -1) {
    event.preventDefault();
    return false;
  }
};



const resetForm = () => {
  nombreTax.value = {
    nombreTaxon: '',
    nombreAutoridad: '',
    sistClassDicc: '',
    citaNomenclatural: '',
    anotacionTaxon: '',
    numeroFilogenetico: '',
    catTax: '',
    estatusTax: '',
    estado: '',
    grpSelec: [],
    nivelRev: '',
  };
};
defineExpose({ resetForm });
</script>


<style scoped>
.form-nombre-container {
  padding: 20px;
  /* Otros estilos si los tienes */
}

.form-header {
  padding: 20px;
  min-width: 190px;
  height: 180px;
  box-sizing: border-box;
}

.icono {
  margin-right: 8px;
}

.custom-tree-node {
  align-items: center;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  width: 50%;
  overflow: auto;
}

.filter-tree .el-tree-node.is-current>.el-tree-node__content {
  background-color: rgb(203, 233, 200) !important;
  color: rgb(0 17 255) !important;
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
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
  font-size: 14px;
  line-height: 16px;
  height: auto;
}

.menu-item-submenu {
  padding: 4px 12px;
  font-size: 14px;
  line-height: 16px;
  height: auto;
}

.el-submenu .el-menu-item {
  padding: 4px 12px;
  font-size: 14px;
  line-height: 16px;
  height: auto;
}

.el-submenu__title {
  padding: 4px 12px;
  font-size: 14px;
  line-height: 16px;
  height: auto;
}

.icon-style {
  width: 16px;
  height: 16px;
  fill: currentColor;
  margin-right: 2px;
  vertical-align: middle;
}

.el-icon {
  font-size: 16px;
  margin-right: 2px;
  vertical-align: middle;
}

/* --------------------------------------------------------- */

.flex-container {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.flex-container span {
  white-space: nowrap;
}

.el-input,
.el-cascader {
  flex: 1;
  min-width: 150px;
}

.tree-node-wrapper {
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}

.tree-node-logo {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.tree-node-label {
  font-size: 14px;
  line-height: 20px;
}

.el-tree-node:hover {
  background-color: transparent !important;
}

@media (max-width: 768px) {
  .filter-tree {
    min-width: 98%;
  }

  .el-aside {
    width: auto !important;
    height: auto !important;
    min-height: auto !important;
    max-width: 100%;
    margin-bottom: 30px;
  }

  .d-table-cell {
    max-width: 100%;
  }

  .el-header {
    width: 100%;
  }

  .el-row {
    justify-content: flex-start;
  }
}
</style>

<style>
.el-dialog {
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  width: 90%;
}

@media (min-width: 768px) {
  .el-dialog {
    max-width: 900px;
  }
}

@media (max-width: 767px) {
  .el-dialog {
    width: 95%;
    margin: 0 auto;
    top: 15%;
    max-width: none;
  }
}
</style>