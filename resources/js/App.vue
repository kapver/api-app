<template>
  <div>
    <nav>
      <router-link to="/">Home</router-link> | <router-link to="/users">Users</router-link>
    </nav>
    <router-view v-slot="{ Component, route }">
      <div :key="route.name">
        <Component :is="Component" />
      </div>
    </router-view>
  </div>
</template>

<style>
nav { padding: 10px; background: #ebebeb;}
nav a { margin-right: 10px; }
</style>