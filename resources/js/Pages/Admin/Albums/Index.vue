<template>
  <div class="min-h-screen bg-neutral-950">
    <nav class="border-b border-white/5 bg-neutral-900/50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-lg font-medium text-white tracking-wider">ADMIN</h1>
          </div>
          <div class="flex items-center gap-6">
            <Link href="/admin/albums" class="text-white text-sm transition-colors">Albums</Link>
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
      <div>
        <div class="flex justify-between items-center mb-8">
          <h2 class="text-2xl font-bold text-white">Albums</h2>
          <Link
            href="/admin/albums/create"
            class="bg-white text-neutral-950 px-4 py-2 rounded-md hover:bg-neutral-200 transition-colors text-sm font-medium"
          >
            Create Album
          </Link>
        </div>

        <div v-if="$page.props.flash?.success" class="bg-green-900/50 text-green-400 border border-green-800 p-3 rounded mb-6">
          {{ $page.props.flash.success }}
        </div>

        <div v-if="$page.props.errors?.message" class="bg-red-900/50 text-red-400 border border-red-800 p-3 rounded mb-6">
          {{ $page.props.errors.message }}
        </div>

        <div v-if="albums.length === 0" class="text-center text-neutral-500 py-12 bg-neutral-900 rounded-lg border border-neutral-800">
          No albums yet. Create your first album!
        </div>

        <div v-else class="bg-neutral-900 rounded-lg overflow-hidden border border-neutral-800">
          <table class="min-w-full divide-y divide-neutral-800">
            <thead class="bg-neutral-900">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Title</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Images</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Tags</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-right text-xs font-medium text-neutral-400 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-800">
              <tr v-for="album in albums" :key="album.id" class="hover:bg-neutral-800/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="font-medium text-white">{{ album.title }}</div>
                  <div class="text-sm text-neutral-500">{{ album.slug }}</div>
                </td>
                <td class="px-6 py-4 text-neutral-400">{{ album.images_count }}</td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="tag in album.tags"
                      :key="tag.id"
                      class="bg-neutral-800 text-neutral-400 px-2 py-1 rounded text-xs"
                    >
                      {{ tag.name }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="album.is_published ? 'bg-green-900/50 text-green-400 border border-green-800' : 'bg-neutral-800 text-neutral-500'"
                    class="px-2 py-1 rounded text-xs"
                  >
                    {{ album.is_published ? 'Published' : 'Draft' }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right space-x-4">
                  <Link
                    :href="`/admin/albums/${album.id}/images`"
                    class="text-neutral-400 hover:text-white text-sm transition-colors"
                  >
                    Images
                  </Link>
                  <Link
                    :href="`/admin/albums/${album.id}/edit`"
                    class="text-neutral-400 hover:text-white text-sm transition-colors"
                  >
                    Edit
                  </Link>
                  <button
                    @click="deleteAlbum(album.id)"
                    class="text-neutral-500 hover:text-red-400 text-sm transition-colors"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  components: { Link },
  props: {
    albums: Array,
  },
  methods: {
    deleteAlbum(id) {
      if (confirm('Are you sure you want to delete this album?')) {
        this.$inertia.delete(`/admin/albums/${id}`);
      }
    },
  },
};
</script>