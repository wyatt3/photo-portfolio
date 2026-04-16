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
            <Link href="/admin/tags" class="text-white text-sm transition-colors">Tags</Link>
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
          <h2 class="text-2xl font-bold text-white">Tags</h2>
        </div>

        <div v-if="$page.props.flash?.success" class="bg-green-900/50 text-green-400 border border-green-800 p-3 rounded mb-6">
          {{ $page.props.flash.success }}
        </div>

        <div class="bg-neutral-900 rounded-lg p-6 mb-6 border border-neutral-800">
          <h3 class="text-lg font-semibold text-white mb-4">Create New Tag</h3>
          <form @submit.prevent="createTag" class="flex gap-3">
            <input
              v-model="newTagName"
              type="text"
              placeholder="Tag name"
              class="flex-1 px-4 py-3 bg-neutral-950 border border-neutral-800 rounded-md text-white focus:border-white focus:ring-0 transition-colors"
            />
            <button
              type="submit"
              class="bg-white text-neutral-950 px-6 py-2 rounded-md hover:bg-neutral-200 transition-colors disabled:opacity-50 font-medium"
              :disabled="!newTagName || creating"
            >
              {{ creating ? 'Creating...' : 'Create' }}
            </button>
          </form>
        </div>

        <div v-if="tags.length === 0" class="text-center text-neutral-500 py-12 bg-neutral-900 rounded-lg border border-neutral-800">
          No tags yet. Create your first tag!
        </div>

        <div v-else class="bg-neutral-900 rounded-lg overflow-hidden border border-neutral-800">
          <table class="min-w-full divide-y divide-neutral-800">
            <thead class="bg-neutral-900">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Name</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Slug</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-neutral-400 uppercase tracking-wider">Albums</th>
                <th class="px-6 py-4 text-right text-xs font-medium text-neutral-400 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-800">
              <tr v-for="tag in tags" :key="tag.id" class="hover:bg-neutral-800/50 transition-colors">
                <td class="px-6 py-4 font-medium text-white">{{ tag.name }}</td>
                <td class="px-6 py-4 text-neutral-400">{{ tag.slug }}</td>
                <td class="px-6 py-4 text-neutral-400">{{ tag.albums_count }}</td>
                <td class="px-6 py-4 text-right">
                  <button
                    @click="deleteTag(tag.id)"
                    class="text-neutral-500 hover:text-red-400 text-sm transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    :disabled="tag.albums_count > 0"
                    :title="tag.albums_count > 0 ? 'Cannot delete: tag is in use' : ''"
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
    tags: Array,
  },
  data() {
    return {
      newTagName: '',
      creating: false,
    };
  },
  methods: {
    createTag() {
      if (!this.newTagName) return;
      this.creating = true;
      this.$inertia.post('/admin/tags', { name: this.newTagName }, {
        onFinish: () => {
          this.creating = false;
          this.newTagName = '';
        },
      });
    },
    deleteTag(id) {
      if (!confirm('Are you sure you want to delete this tag?')) return;
      this.$inertia.delete(`/admin/tags/${id}`);
    },
  },
};
</script>