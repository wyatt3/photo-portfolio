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
      <div>
        <div class="mb-8">
          <Link href="/admin/albums" class="text-neutral-400 hover:text-white text-sm">&larr; Back to Albums</Link>
        </div>

        <div class="bg-neutral-900 rounded-lg p-6 border border-neutral-800">
          <h2 class="text-xl font-bold text-white mb-6">Edit Album</h2>

          <form @submit.prevent="submit">
            <div class="mb-6">
              <label class="block text-sm font-medium text-neutral-400 mb-2">Title</label>
              <input
                v-model="form.title"
                type="text"
                class="w-full px-4 py-3 bg-neutral-950 border border-neutral-800 rounded-md text-white focus:border-white focus:ring-0 transition-colors"
                :class="{ 'border-red-500': errors.title }"
              />
              <p v-if="errors.title" class="text-red-400 text-sm mt-2">{{ errors.title }}</p>
            </div>

            <div class="mb-6">
              <label class="flex items-center">
                <input v-model="form.is_published" type="checkbox" class="mr-3 bg-neutral-950 border-neutral-700" />
                <span class="text-sm text-neutral-400">Publish album</span>
              </label>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-medium text-neutral-400 mb-2">Tags</label>
              <div class="flex flex-wrap gap-2">
                <label
                  v-for="tag in tags"
                  :key="tag.id"
                  class="flex items-center px-3 py-2 rounded cursor-pointer transition-colors"
                  :class="form.tag_ids.includes(tag.id) ? 'bg-white text-neutral-950' : 'bg-neutral-800 text-neutral-400 hover:bg-neutral-700'"
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

            <div class="flex justify-end gap-3">
              <Link
                :href="`/admin/albums/${album.id}/images`"
                class="bg-neutral-700 text-white px-6 py-2 rounded-md hover:bg-neutral-600 transition-colors"
              >
                Manage Images
              </Link>
              <button
                type="submit"
                class="bg-white text-neutral-950 px-6 py-2 rounded-md hover:bg-neutral-200 transition-colors font-medium disabled:opacity-50"
                :disabled="processing"
              >
                {{ processing ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </form>
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