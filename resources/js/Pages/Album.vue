<template>
  <div class="min-h-screen bg-neutral-950 text-white">
    <header class="fixed top-0 left-0 right-0 z-50 bg-neutral-950/80 backdrop-blur-sm border-b border-white/5">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <Link href="/" class="text-neutral-400 hover:text-white flex items-center gap-2 text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
          </svg>
          Back
        </Link>
        <h1 class="text-lg font-medium text-white tracking-wider">{{ album.title }}</h1>
        <div class="w-16"></div>
      </div>
    </header>

    <main class="pt-24 pb-12 px-4 sm:px-6 lg:px-8">
      <div class="flex flex-wrap gap-2 mb-8">
        <Link
          v-for="tag in album.tags"
          :key="tag.slug"
          :href="`/?tag=${tag.slug}`"
          class="text-xs text-neutral-500 hover:text-white px-3 py-1.5 rounded-full bg-neutral-900 hover:bg-neutral-800 transition-colors"
        >
          {{ tag.name }}
        </Link>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-1">
        <button
          v-for="(image, index) in album.images"
          :key="image.id"
          @click="openLightbox(index)"
          class="aspect-square overflow-hidden bg-neutral-900 cursor-pointer hover:opacity-80 transition-opacity"
        >
          <img
            :src="image.thumbnail_url"
            :alt="`${album.title} photo ${index + 1}`"
            class="w-full h-full object-cover"
          />
        </button>
      </div>
    </main>

    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="lightboxOpen"
          class="fixed inset-0 z-[100] bg-neutral-950/95 backdrop-blur-sm flex items-center justify-center"
          @click.self="closeLightbox"
        >
          <button
            @click="closeLightbox"
            class="absolute top-4 right-4 text-neutral-500 hover:text-white p-2 transition-colors"
          >
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <button
            v-if="currentIndex > 0"
            @click="prevImage"
            class="absolute left-4 text-neutral-500 hover:text-white p-2 transition-colors"
          >
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
            </svg>
          </button>

          <img
            :src="album.images[currentIndex].full_url"
            :alt="`${album.title} photo ${currentIndex + 1}`"
            class="max-w-[90vw] max-h-[90vh] object-contain"
          />

          <button
            v-if="currentIndex < album.images.length - 1"
            @click="nextImage"
            class="absolute right-4 text-neutral-500 hover:text-white p-2 transition-colors"
          >
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
            </svg>
          </button>

          <div class="absolute bottom-8 text-neutral-500 text-sm font-light">
            {{ currentIndex + 1 }} <span class="text-neutral-700">/</span> {{ album.images.length }}
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import { Teleport } from "vue";

export default {
  components: { Link, Teleport },
  props: {
    album: Object,
  },
  data() {
    return {
      lightboxOpen: false,
      currentIndex: 0,
    };
  },
  methods: {
    openLightbox(index) {
      this.currentIndex = index;
      this.lightboxOpen = true;
      document.body.style.overflow = "hidden";
    },
    closeLightbox() {
      this.lightboxOpen = false;
      document.body.style.overflow = "";
    },
    prevImage() {
      if (this.currentIndex > 0) {
        this.currentIndex--;
      }
    },
    nextImage() {
      if (this.currentIndex < this.album.images.length - 1) {
        this.currentIndex++;
      }
    },
    handleKeydown(e) {
      if (!this.lightboxOpen) return;
      if (e.key === "Escape") this.closeLightbox();
      if (e.key === "ArrowLeft") this.prevImage();
      if (e.key === "ArrowRight") this.nextImage();
    },
  },
  mounted() {
    window.addEventListener("keydown", this.handleKeydown);
  },
  beforeUnmount() {
    window.removeEventListener("keydown", this.handleKeydown);
  },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
