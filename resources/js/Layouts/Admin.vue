<template>
  <div class="min-h-screen bg-neutral-950">
    <nav class="border-b border-white/5 bg-neutral-900/50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-lg font-medium text-white tracking-wider">ADMIN</h1>
          </div>
          <div class="flex items-center gap-6">
            <Link href="/admin/albums" class="text-neutral-400 hover:text-white text-sm transition-colors">Albums</Link>
            <Link href="/admin/tags" class="text-neutral-400 hover:text-white text-sm transition-colors">Tags</Link>
            <form method="POST" action="/admin/logout" class="inline">
              <input type="hidden" name="_token" :value="$page.props.csrf_token" />
              <button type="submit" class="text-neutral-400 hover:text-white text-sm transition-colors">Logout</button>
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