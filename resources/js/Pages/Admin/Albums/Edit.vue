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
      <div class="mb-8">
        <Link href="/admin/albums" class="text-neutral-400 hover:text-white text-sm">&larr; Back to Albums</Link>
      </div>

      <!-- Album Info -->
      <div class="bg-neutral-900 rounded-lg p-6 mb-6 border border-neutral-800">
        <h2 class="text-xl font-bold text-white mb-6">Album Details</h2>

        <form @submit.prevent="saveAlbum">
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

          <div class="flex justify-end">
            <button
              type="submit"
              class="bg-white text-neutral-950 px-6 py-2 rounded-md hover:bg-neutral-200 transition-colors font-medium disabled:opacity-50"
              :disabled="savingAlbum"
            >
              {{ savingAlbum ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Images -->
      <div class="bg-neutral-900 rounded-lg p-6 mb-6 border border-neutral-800">
        <h3 class="text-lg font-semibold text-white mb-4">Upload Images</h3>
        <form @submit.prevent="uploadImages">
          <div class="mb-4">
            <label class="block text-sm font-medium text-neutral-400 mb-2">
              Select images (JPEG, PNG, max 10MB each)
            </label>
            <input
              ref="fileInput"
              type="file"
              multiple
              accept="image/jpeg,image/png"
              @change="handleFileSelect"
              class="w-full text-neutral-400"
            />
          </div>
          <div v-if="selectedFiles.length > 0" class="mb-4 text-sm text-neutral-400">
            {{ selectedFiles.length }} file(s) selected
          </div>
          <button
            type="submit"
            class="bg-white text-neutral-950 px-4 py-2 rounded-md hover:bg-neutral-200 transition-colors disabled:opacity-50 font-medium text-sm"
            :disabled="selectedFiles.length === 0 || uploading"
          >
            {{ uploading ? "Uploading..." : "Upload Images" }}
          </button>
        </form>
      </div>

      <div
        v-if="$page.props.flash?.success"
        class="bg-green-900/50 text-green-400 border border-green-800 p-3 rounded mb-6"
      >
        {{ $page.props.flash.success }}
      </div>

      <div
        v-if="album.images.length === 0"
        class="text-center text-neutral-500 py-12 bg-neutral-900 rounded-lg border border-neutral-800"
      >
        No images in this album yet.
      </div>

      <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <div v-for="image in album.images" :key="image.id" class="relative group">
          <img :src="image.thumbnail_url" class="w-full aspect-square object-cover rounded-lg" />
          <div
            v-if="album.cover_image_id === image.id"
            class="absolute top-2 left-2 bg-white text-neutral-950 text-xs px-2 py-1 rounded font-medium"
          >
            Cover
          </div>
          <div
            class="absolute inset-0 bg-neutral-950/80 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2"
          >
            <button
              v-if="album.cover_image_id !== image.id"
              @click="setCover(image.id)"
              class="bg-white text-neutral-950 px-3 py-1 rounded text-sm hover:bg-neutral-200 transition-colors"
            >
              Set Cover
            </button>
            <button
              @click="deleteImage(image.id)"
              class="bg-neutral-800 text-white px-3 py-1 rounded text-sm hover:bg-red-600 transition-colors"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";

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
        tag_ids: this.album.tags || [],
      },
      selectedFiles: [],
      uploading: false,
      savingAlbum: false,
    };
  },
  methods: {
    saveAlbum() {
      this.savingAlbum = true;
      this.$inertia.put(`/admin/albums/${this.album.id}`, this.form, {
        onFinish: () => {
          this.savingAlbum = false;
        },
      });
    },
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
          "Content-Type": "multipart/form-data",
        },
        onFinish: () => {
          this.uploading = false;
          this.selectedFiles = [];
          this.$refs.fileInput.value = "";
        },
      });
    },
    setCover(imageId) {
      this.$inertia.post(`/admin/albums/${this.album.id}/images/${imageId}/cover`);
    },
    deleteImage(imageId) {
      if (confirm("Are you sure you want to delete this image?")) {
        this.$inertia.delete(`/admin/images/${imageId}`);
      }
    },
  },
};
</script>