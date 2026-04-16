<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Tags</h2>
    </div>

    <div v-if="$page.props.flash?.success" class="bg-green-100 text-green-700 p-3 rounded mb-4">
      {{ $page.props.flash.success }}
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
      <h3 class="text-lg font-semibold mb-4">Create New Tag</h3>
      <form @submit.prevent="createTag" class="flex gap-3">
        <input
          v-model="newTagName"
          type="text"
          placeholder="Tag name"
          class="flex-1 px-3 py-2 border rounded-md"
        />
        <button
          type="submit"
          class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
          :disabled="!newTagName || creating"
        >
          {{ creating ? 'Creating...' : 'Create' }}
        </button>
      </form>
    </div>

    <div v-if="tags.length === 0" class="text-center text-gray-500 py-12 bg-white rounded-lg shadow">
      No tags yet. Create your first tag!
    </div>

    <div v-else class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Albums</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="tag in tags" :key="tag.id">
            <td class="px-6 py-4 font-medium text-gray-900">{{ tag.name }}</td>
            <td class="px-6 py-4 text-gray-600">{{ tag.slug }}</td>
            <td class="px-6 py-4 text-gray-600">{{ tag.albums_count }}</td>
            <td class="px-6 py-4 text-right">
              <button
                @click="deleteTag(tag.id)"
                class="text-red-600 hover:text-red-800 text-sm"
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
</template>

<script>
export default {
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
