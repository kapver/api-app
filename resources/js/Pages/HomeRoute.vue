<template>
  <div>
    <h1>Add New User</h1>
    <hr/>
    <form @submit.prevent="register">
      <input v-model="form.name" placeholder="Name" type="text" /><br>
      <span v-if="errors.name">{{ errors.name[0] }}</span><br>
      <input v-model="form.email" placeholder="Email" type="email" /><br>
      <span v-if="errors.email">{{ errors.email[0] }}</span><br>
      <input v-model="form.phone" placeholder="Phone" type="text" /><br>
      <span v-if="errors.phone">{{ errors.phone[0] }}</span><br>
      <select v-model="form.position_id">
        <option value="" disabled>Select position</option>
        <option v-for="position in positions" :key="position.id" :value="position.id">
          {{ position.name }}
        </option>
      </select><br>
      <span v-if="errors.position_id">{{ errors.position_id[0] }}</span><br>
      <input type="file" @change="onFileChange" /><br>
      <span v-if="errors.photo">{{ errors.photo[0] }}</span><br>
      <hr/>
      <button type="submit" style="border: solid 1px #ccc; padding: 5px; margin: 5px;">Add User</button>
    </form>
    <hr/>
    <br/>
    <p v-if="message">{{ message }}</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        name: '',
        email: '',
        phone: '',
        position_id: '',
        photo: null,
      },
      positions: [],
      message: '',
      token: '',
      errors: {}, // Объект для ошибок по полям
    };
  },
  async mounted() {
    await Promise.all([this.fetchToken(), this.fetchPositions()]);
  },
  methods: {
    async fetchToken() {
      const response = await this.$axios.get('/api/v1/token');
      this.token = response.data.token;
    },
    async fetchPositions() {
      try {
        const response = await this.$axios.get('/api/v1/positions');
        this.positions = response.data.positions;
      } catch (error) {
        this.message = 'Failed to load positions';
      }
    },
    onFileChange(event) {
      this.form.photo = event.target.files[0];
    },
    async register() {
      const formData = new FormData();
      formData.append('name', this.form.name);
      formData.append('email', this.form.email);
      formData.append('phone', this.form.phone);
      formData.append('position_id', this.form.position_id);
      if (this.form.photo) formData.append('photo', this.form.photo);

      try {
        const response = await this.$axios.post('/api/v1/users', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Token': this.token,
          },
        });
        this.message = 'User registered successfully!';
        this.form = { name: '', email: '', phone: '', position_id: '', photo: null };
        this.errors = {}; // Очищаем ошибки при успехе
      } catch (error) {
        this.message = error.response?.data?.message || 'Registration failed';
        this.errors = error.response?.data?.fails || {}; // Сохраняем ошибки по полям
      }
    },
  },
};
</script>

<style scoped>
input:not([type="file"]), select {
  border: 1px solid #ccc;
  margin: 5px 0;
  padding: 5px;
}
span {
  color: red;
  font-size: 12px;
}
</style>