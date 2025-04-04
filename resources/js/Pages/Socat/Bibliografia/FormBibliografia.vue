<template>
  <div>
    <el-container>
      <el-header>
        <table>
          <tbody>
            <tr>
              <td style="width:50px">
                <div v-show="deshabilita">
                  <el-tooltip class="item" effect="dark" content=" Orden de la referencia" placement="right-start">
                    <el-button size="small" circle type="primary" @click="ordenar" disabled>
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-layout-wtf" viewBox="0 0 16 16">
                        <path
                          d="M5 1v8H1V1h4zM1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1H1zm13 2v5H9V2h5zM9 1a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9zM5 13v2H3v-2h2zm-2-1a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H3zm12-1v2H9v-2h6zm-6-1a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H9z" />
                      </svg>
                    </el-button>
                  </el-tooltip>
                </div>
                <div v-show="habilita">
                  <el-tooltip class="item" effect="dark" content=" Orden de la referencia" placement="right-start">
                    <el-button size="small" circle type="primary" @click="ordenar">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-layout-wtf" viewBox="0 0 16 16">
                        <path
                          d="M5 1v8H1V1h4zM1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1H1zm13 2v5H9V2h5zM9 1a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9zM5 13v2H3v-2h2zm-2-1a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H3zm12-1v2H9v-2h6zm-6-1a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H9z" />
                      </svg>
                    </el-button>
                  </el-tooltip>
                </div>
              </td>
              <td style="width:50px">
                <div>

                  <el-tooltip class="item" effect="dark" content="Guardar" placement="right-start">
                    <el-button size="small" circle type="warning" @click="guardarBibliografia">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-usb-drive" viewBox="0 0 16 16">
                        <path
                          d="M6 .5a.5 0 0 1 .5-.5h4a.5 0 0 1 .5.5v4H6v-4ZM7 1v1h1V1H7Zm2 0v1h1V1H9ZM6 5a1 1 0 0 0-1 1v8.5A1.5 1.5 0 0 0 6.5 16h4a1.5 1.5 0 0 0 1.5-1.5V6a1 1 0 0 0-1-1H6Zm0 1h5v8.5a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5V6Z" />
                      </svg>
                    </el-button>
                  </el-tooltip>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </el-header>
      <el-main>
        <el-card class="box-card">
          <div>
            <el-form :model="bibliografia" :rules="rules" ref="bibliografiaForm">
              <el-row :gutter="10">

                <el-form-item label="Autor(es)" prop="Autor">
                  <el-input type="text" maxlength="255" v-model="bibliografia.Autor" @blur="citaCompleta()"
                    aria-placeholder="Nombre del autor" show-word-limit></el-input>
                </el-form-item>

                <el-form-item label="Año(s)" prop="Anio">
                  <el-input type="text" maxlength="5" show-word-limit v-model="bibliografia.Anio" @blur="citaCompleta"
                    aria-placeholder="Años">
                  </el-input>
                </el-form-item>

                <el-form-item label="Título de la publicación" prop="TituloPublicacion">
                  <el-input type="text" maxlength="255" show-word-limit v-model="bibliografia.TituloPublicacion"
                    @blur="ordenarCita" aria-placeholder="Título de la publicación">
                  </el-input>
                </el-form-item>

                <el-form-item label="Título de la subpublicación" prop="TituloSubPublicacion">
                  <el-input type="text" maxlength="255" show-word-limit v-model="bibliografia.TituloSubPublicacion"
                    @blur="ordenarCita" aria-placeholder="Título de la subpublicación">
                  </el-input>
                </el-form-item>

                <el-form-item label="Editorial, país, lugar, páginas" prop="EditoresCompiladores">
                  <el-input type="text" maxlength="255" show-word-limit v-model="bibliografia.EditoresCompiladores"
                    @blur="ordenarCita" aria-placeholder="Editorial, país, lugar, páginas">
                  </el-input>
                </el-form-item>

                <el-form-item label="Número, volúmen, año, mes(es)" prop="NumeroVolumenAnio">
                  <el-input type="text" maxlength="255" show-word-limit v-model="bibliografia.NumeroVolumenAnio"
                    @blur="ordenarCita" aria-placeholder="Número, volúmen, año, mes(es)">
                  </el-input>
                </el-form-item>

                <el-form-item label="Editor(es)/compilador(es)" prop="EditorialPaisPagina">
                  <el-input type="text" maxlength="255" show-word-limit v-model="bibliografia.EditorialPaisPagina"
                    @blur="ordenarCita" aria-placeholder="Editor(es)/compilador(es)">
                  </el-input>
                </el-form-item>

                <el-form-item label="Referencia completa" prop="citaCompleta">
                  <el-input type="textarea" :rows="4" maxlength="255" v-model="bibliografia.citaCompleta"
                    aria-placeholder="Referencia completa">
                  </el-input>
                </el-form-item>

              </el-row>
            </el-form>
          </div>
        </el-card>
      </el-main>
    </el-container>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { ElMessageBox } from 'element-plus';
import axios from 'axios';

const props = defineProps({
  biblioEdit: {
    type: Object,
    required: false,
    default: null
  },
  accion: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['se-guardo']);



const bibliografia = reactive({
  Autor: '',
  Anio: '',
  TituloPublicacion: '',
  TituloSubPublicacion: '',
  EditoresCompiladores: '',
  NumeroVolumenAnio: '',
  EditorialPaisPagina: '',
  citaCompleta: '',
  OrdenCitaCompleta: '1243765'
});

const rules = {
  Autor: [
    { required: true, message: 'Ingrese un autor', trigger: 'blur' },
    { min: 1, max: 255, message: 'El tamaño debe ser entre 1 y 255', trigger: 'blur' }
  ],
  Anio: [
    { required: true, message: 'Ingrese un año de publiación', trigger: 'blur' },
    { min: 1, max: 50, message: 'El tamaño debe ser menor o igual a 50 caracteres', trigger: 'blur' }
  ],
  TituloPublicacion: [
    { required: true, message: 'Ingrese un titulo', trigger: 'blur' },
    { max: 255, message: 'El tamaño debe ser menor o igual a 255 caracteres', trigger: 'blur' }
  ],
  TituloSubPublicacion: [
    { max: 255, message: 'El tamaño debe ser menor o igual a 255 caracteres', trigger: 'blur' }
  ],
  EditoresCompiladores: [
    { max: 255, message: 'El tamaño debe ser menor o igual a 255 caracteres', trigger: 'blur' }
  ],
  NumeroVolumenAnio: [
    { max: 255, message: 'El tamaño debe ser menor o igual a 255 caracteres', trigger: 'blur' }
  ],
  EditorialPaisPagina: [
    { max: 255, message: 'El tamaño debe ser menor o igual a 255 caracteres', trigger: 'blur' }
  ]
};

const habilita = ref(false);
const deshabilita = ref(false);
const bibliografiaForm = ref(null); // Referencia al formulario

const ordenar = () => {
  emit('open-orden-dialog');
};

const guardarBibliografia = () => {
  bibliografiaForm.value.validate(async (valid) => {
    if (valid) {
      const data = { ...bibliografia };
      let url = '/bibliografias';

      try {
        console.log("Valor de props.accion en FormBibliografia:", props.accion); // Agrega esta línea
        if (props.accion === 'editar') {
          url = `/bibliografias/${props.biblioEdit.IdBibliografia}`;
          console.log("URL de la petición:", url);
          console.log("Datos a enviar:", data);
          const response = await axios.put(url, data);
          mostrarMensaje(response.data.message, 'success');
          emit('se-guardo'); // Emitir el evento
        } else {
          const response = await axios.post(url, data);
          mostrarMensaje(response.data.message, 'success');
          resetForm();
        }
        emit('se-guardo');
      } catch (error) {
        mostrarMensaje('Error al guardar la bibliografía', 'error');
        console.error('Error al guardar bibliografía:', error);
      }
    } else {
      mostrarMensaje('Por favor, complete correctamente el formulario', 'warning');
    }
  });
};





const citaCompleta = () => {
  bibliografia.citaCompleta = bibliografia.Autor + ' ' + bibliografia.Anio + ' ' +
    bibliografia.TituloPublicacion + ' ' + bibliografia.TituloSubPublicacion + ' ' +
    bibliografia.EditorialPaisPagina + ' ' + bibliografia.NumeroVolumenAnio + ' ' +
    bibliografia.EditoresCompiladores;
};

const ordenarCita = () => {
  let orden = '';
  let citaComp = '';

  const campos = ['', 'Autor', 'Anio', 'TituloSubPublicacion', 'TituloPublicacion',
    'EditoresCompiladores', 'NumeroVolumenAnio', 'EditorialPaisPagina'];

  orden = props.biblioEdit?.OrdenCitaCompleta || '1243765';

  const myArray = orden.split("");

  for (let i = 0; i < myArray.length; i++) {
    if (citaComp !== '') {
      if (bibliografia[campos[myArray[i]]] !== '') {
        citaComp += ' ' + bibliografia[campos[myArray[i]]];
      }
    } else {
      if (bibliografia[campos[myArray[i]]] !== '') {
        citaComp = bibliografia[campos[myArray[i]]];
      }
    }
  }
  bibliografia.citaCompleta = citaComp;
};

const resetForm = () => {
  Object.assign(bibliografia, {
    Autor: '',
    Anio: '',
    TituloPublicacion: '',
    TituloSubPublicacion: '',
    EditoresCompiladores: '',
    NumeroVolumenAnio: '',
    EditorialPaisPagina: '',
    citaCompleta: '',
    OrdenCitaCompleta: '1243765'
  });
};

const mostrarMensaje = (message, type) => {
  ElMessageBox.alert(message, 'Bibliografía', { type });
};

watch(
  () => props.biblioEdit,
  (newVal) => {
    if (props.accion === 'crear') {
      habilita.value = false;
      deshabilita.value = true;
      resetForm();
    } else if (newVal) {
      habilita.value = true;
      deshabilita.value = false;
      // Iterar sobre las propiedades de newVal y asignarlas individualmente
      for (const key in newVal) {
        if (bibliografia.hasOwnProperty(key)) {
          bibliografia[key] = newVal[key];
        }
      }
    }
  },
  { deep: true, immediate: true }
);

</script>