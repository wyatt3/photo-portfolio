<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
      <h1 class="text-2xl font-bold text-center mb-6">Admin Login</h1>
      
      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full px-3 py-2 border rounded-md"
            :class="{ 'border-red-500': errors.email }"
          />
          <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full px-3 py-2 border rounded-md"
            :class="{ 'border-red-500': errors.password }"
          />
          <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
        </div>

        <div class="mb-6">
          <label class="flex items-center">
            <input v-model="form.remember" type="checkbox" class="mr-2" />
            <span class="text-sm text-gray-600">Remember me</span>
          </label>
        </div>

        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:opacity-50"
          :disabled="processing"
        >
          {{ processing ? 'Logging in...' : 'Login' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    errors: Object,
  },
  data() {
    return {
      form: {
        email: '',
        password: '',
        remember: false,
      },
      processing: false,
    };
  },
  methods: {
    submit() {
      this.processing = true;
      this.$inertia.post('/admin/login', this.form, {
        onFinish: () => {
          this.processing = false;
        },
      });
    },
  },
};
</script>
