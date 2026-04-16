<template>
  <div class="min-h-screen bg-neutral-950 flex items-center justify-center">
    <div class="w-full max-w-md p-8">
      <h1 class="text-2xl font-bold text-white text-center mb-8 tracking-wider">ADMIN</h1>

      <form @submit.prevent="submit">
        <div class="mb-6">
          <label class="block text-sm font-medium text-neutral-400 mb-2">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full px-4 py-3 bg-neutral-900 border border-neutral-800 rounded-md text-white focus:border-white focus:ring-0 transition-colors"
            :class="{ 'border-red-500': errors.email }"
          />
          <p v-if="errors.email" class="text-red-400 text-sm mt-2">{{ errors.email }}</p>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-neutral-400 mb-2">Password</label>
          <input
            v-model="form.password"
            type="password"
            class="w-full px-4 py-3 bg-neutral-900 border border-neutral-800 rounded-md text-white focus:border-white focus:ring-0 transition-colors"
            :class="{ 'border-red-500': errors.password }"
          />
          <p v-if="errors.password" class="text-red-400 text-sm mt-2">{{ errors.password }}</p>
        </div>

        <div class="mb-6">
          <label class="flex items-center">
            <input v-model="form.remember" type="checkbox" class="mr-2 bg-neutral-900 border-neutral-700" />
            <span class="text-sm text-neutral-400">Remember me</span>
          </label>
        </div>

        <button
          type="submit"
          class="w-full bg-white text-neutral-950 py-3 px-4 rounded-md hover:bg-neutral-200 disabled:opacity-50 transition-colors font-medium"
          :disabled="processing"
        >
          {{ processing ? "Logging in..." : "Login" }}
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
        email: "",
        password: "",
        remember: false,
      },
      processing: false,
    };
  },
  methods: {
    submit() {
      this.processing = true;
      this.$inertia.post("/login", this.form, {
        onFinish: () => {
          this.processing = false;
        },
      });
    },
  },
};
</script>
