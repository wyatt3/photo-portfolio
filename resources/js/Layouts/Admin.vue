<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold">Photography Admin</h1>
          </div>
          <div class="flex items-center space-x-4">
            <Link href="/admin/albums" class="text-gray-600 hover:text-gray-900">Albums</Link>
            <Link href="/admin/tags" class="text-gray-600 hover:text-gray-900">Tags</Link>
            <form method="POST" action="/admin/logout" class="inline">
              <input type="hidden" name="_token" :value="$page.props.csrf_token" />
              <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
            </form>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <slot />
    </main>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  components: { Link },
  props: {
    title: String,
  },
};
</script>
