<template>
  <div>
    <div class="mb-6">
      <Link href="/admin/albums" class="text-blue-600 hover:text-blue-800">&larr; Back to Albums</Link>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-xl font-bold mb-6">Edit Album</h2>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
          <input
            v-model="form.title"
            type="text"
            class="w-full px-3 py-2 border rounded-md"
            :class="{ 'border-red-500': errors.title }"
          />
          <p v-if="errors.title" class="text-red-500 text-sm mt-1">{{ errors.title }}</p>
        </div>

        <div class="mb-4">
          <label class="flex items-center">
            <input v-model="form.is_published" type="checkbox" class="mr-2" />
            <span class="text-sm text-gray-700">Publish album</span>
          </label>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
          <div class="flex flex-wrap gap-2">
            <label
              v-for="tag in tags"
              :key="tag.id"
              class="flex items-center px-3 py-1 rounded cursor-pointer"
              :class="form.tag_ids.includes(tag.id) ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'"
            >
              <input
                type="checkbox"
                :value="tag.id"
                v-model="form.tag_ids"
                class="mr-2"
              />
              {{ tag.name }}
            </label>
          </div>
        </div>

        <div class="flex justify-end space-x-3">
          <Link
            :href="`/admin/albums/${album.id}/images`"
            class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600"
          >
            Manage Images
          </Link>
          <button
            type="submit"
            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700"
            :disabled="processing"
          >
            {{ processing ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  components: { Link },
  props: {
    album: Object,
    tags: Array,
    errors: Object,
  },
  data() {
    return {
      form: {
        title: this.album.title,
        is_published: this.album.is_published,
        tag_ids: this.album.tags.map(t => t.id),
      },
      processing: false,
    };
  },
  methods: {
    submit() {
      this.processing = true;
      this.$inertia.put(`/admin/albums/${this.album.id}`, this.form, {
        onFinish: () => {
          this.processing = false;
        },
      });
    },
  },
};
</script>
