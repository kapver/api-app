<template>
  <div>
    <h1>Users</h1>
    <hr/>
    <div v-for="user in users" :key="user.id" style="display: flex; align-items: center; margin: 2px">
      <img :src="user.photo" alt="User photo" width="70" height="70" style="margin-right: 10px;">
      <span>{{ user.name }} - {{ user.email }} - {{ user.phone }} - {{ user.position }}</span>
    </div>
    <hr/>
    <button @click="loadMore">Show more</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      users: [],
      page: 1,
    };
  },
  mounted() {
    this.loadUsers();
  },
  methods: {
    async loadUsers() {
      const response = await this.$axios.get(`/api/v1/users?page=${this.page}&count=6`);
      this.users = response.data.users;
    },
    loadMore() {
      this.page++;
      this.loadUsers();
    },
  },
};
</script>