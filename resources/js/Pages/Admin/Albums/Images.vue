<template>
  <div>
    <div class="mb-6 flex justify-between items-center">
      <div>
        <Link href="/admin/albums" class="text-blue-600 hover:text-blue-800">&larr; Back to Albums</Link>
        <h2 class="text-2xl font-bold mt-2">{{ album.title }}</h2>
      </div>
      <div class="flex gap-3">
        <Link
          :href="`/admin/albums/${album.id}/edit`"
          class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
        >
          Edit Album
        </Link>
      </div>
    </div>

    <div v-if="$page.props.flash?.success" class="bg-green-100 text-green-700 p-3 rounded mb-4">
      {{ $page.props.flash.success }}
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
      <h3 class="text-lg font-semibold mb-4">Upload Images</h3>
      <form @submit.prevent="uploadImages">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Select images (JPEG, PNG, max 10MB each)
          </label>
          <input
            ref="fileInput"
            type="file"
            multiple
            accept="image/jpeg,image/png"
            @change="handleFileSelect"
            class="w-full"
          />
        </div>
        <div v-if="selectedFiles.length > 0" class="mb-4">
          <p class="text-sm text-gray-600">{{ selectedFiles.length }} file(s) selected</p>
        </div>
        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
          :disabled="selectedFiles.length === 0 || uploading"
        >
          {{ uploading ? 'Uploading...' : 'Upload Images' }}
        </button>
      </form>
    </div>

    <div v-if="album.images.length === 0" class="text-center text-gray-500 py-12 bg-white rounded-lg shadow">
      No images in this album yet.
    </div>

    <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
      <div
        v-for="image in album.images"
        :key="image.id"
        class="relative group"
      >
        <img
          :src="image.thumbnail_url"
          class="w-full aspect-square object-cover rounded-lg"
        />
        <div
          v-if="album.cover_image_id === image.id"
          class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded"
        >
          Cover
        </div>
        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2">
          <button
            v-if="album.cover_image_id !== image.id"
            @click="setCover(image.id)"
            class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700"
          >
            Set Cover
          </button>
          <button
            @click="deleteImage(image.id)"
            class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  components: { Link },
  props: {
    album: Object,
  },
  data() {
    return {
      selectedFiles: [],
      uploading: false,
    };
  },
  methods: {
    handleFileSelect(event) {
      this.selectedFiles = Array.from(event.target.files);
    },
    uploadImages() {
      if (this.selectedFiles.length === 0) return;

      this.uploading = true;
      const formData = new FormData();
      this.selectedFiles.forEach((file, index) => {
        formData.append(`images[${index}]`, file);
      });

      this.$inertia.post(`/admin/albums/${this.album.id}/images`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
        onFinish: () => {
          this.uploading = false;
          this.selectedFiles = [];
          this.$refs.fileInput.value = '';
        },
      });
    },
    setCover(imageId) {
      this.$inertia.post(`/admin/albums/${this.album.id}/images/${imageId}/cover`);
    },
    deleteImage(imageId) {
      if (confirm('Are you sure you want to delete this image?')) {
        this.$inertia.delete(`/admin/images/${imageId}`);
      }
    },
  },
};
</script>
