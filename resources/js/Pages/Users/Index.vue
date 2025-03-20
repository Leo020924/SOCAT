<template>
    <div>
      <h1>Usuarios</h1>
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Alias</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.rol.Nombre_Perfil }}</td>
            <td>{{ user.Alias }}</td>
          </tr>
        </tbody>
      </table>
      <Link href="/users/create" class="btn btn-primary">Agregar Usuario</Link>
    </div>
  </template>
  
  <script>  
  import AppLayout from '@/Layouts/AppLayout.vue';

  export default {
    props: {
      users: Array,
    },
    layout: AppLayout,
    created() {
      // Imprime el valor de la propiedad 'users' al crear el componente
      console.log('Usuarios:', this.users);
    },
  };
  </script>