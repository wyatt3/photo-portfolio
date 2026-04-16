<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Albums</h2>
      <Link
        href="/admin/albums/create"
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
      >
        Create Album
      </Link>
    </div>

    <div v-if="$page.props.flash?.success" class="bg-green-100 text-green-700 p-3 rounded mb-4">
      {{ $page.props.flash.success }}
    </div>

    <div v-if="albums.length === 0" class="text-center text-gray-500 py-12">
      No albums yet. Create your first album!
    </div>

    <div v-else class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Images</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tags</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="album in albums" :key="album.id">
            <td class="px-6 py-4">
              <div class="font-medium text-gray-900">{{ album.title }}</div>
              <div class="text-sm text-gray-500">{{ album.slug }}</div>
            </td>
            <td class="px-6 py-4 text-gray-600">{{ album.images_count }}</td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="tag in album.tags"
                  :key="tag.id"
                  class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs"
                >
                  {{ tag.name }}
                </span>
              </div>
            </td>
            <td class="px-6 py-4">
              <span
                :class="album.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                class="px-2 py-1 rounded text-xs"
              >
                {{ album.is_published ? 'Published' : 'Draft' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <Link
                :href="`/admin/albums/${album.id}/images`"
                class="text-blue-600 hover:text-blue-800 text-sm"
              >
                Images
              </Link>
              <Link
                :href="`/admin/albums/${album.id}/edit`"
                class="text-gray-600 hover:text-gray-800 text-sm"
              >
                Edit
              </Link>
              <button
                @click="deleteAlbum(album.id)"
                class="text-red-600 hover:text-red-800 text-sm"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
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
