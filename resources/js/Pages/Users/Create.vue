<template>
  <div>
    <h1>Crear Usuario</h1>
    <form @submit.prevent="submit">
      <div>
        <label for="name">Nombre</label>
        <input v-model="form.name" id="name" type="text" />
      </div>
      <div>
        <label for="email">Email</label>
        <input v-model="form.email" id="email" type="email" />
      </div>
      <div>
        <label for="password">Contraseña</label>
        <input v-model="form.password" id="password" type="password" />
      </div>
      <div>
        <label for="IdRol">Rol</label>
        <select v-model="form.IdRol" id="IdRol">
          <option v-for="rol in roles" :key="rol.IdRol" :value="rol.IdRol">
            {{ rol.Nombre_Perfil }}
          </option>
        </select>
      </div>
      <div>
        <label for="Alias">Alias</label>
        <input v-model="form.Alias" id="Alias" type="text" />
      </div>
      <button type="submit">Guardar</button>
    </form>
  </div>
</template>

<script>

import { useForm } from '@inertiajs/inertia-vue3';

export default {
  props: {
    roles: Array,
  },
  setup() {
    const form = useForm({
      name: '',
      email: '',
      password: '',
      IdRol: '',
      Alias: '',
    });

    const submit = () => {
      form.post('/users');
    };

    return { form, submit };
  },

  layout: AppLayout,

};
</script>