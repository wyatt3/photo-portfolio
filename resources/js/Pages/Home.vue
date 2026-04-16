<template>
  <div class="min-h-screen bg-neutral-950">
    <header class="fixed top-0 left-0 right-0 z-50 bg-neutral-950/80 backdrop-blur-sm border-b border-white/5">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <h1 class="text-lg font-medium text-white tracking-wider">PORTFOLIO</h1>
        <div class="flex gap-2">
          <button
            @click="filterTag(null)"
            class="px-3 py-1.5 rounded-full text-xs font-medium transition-all"
            :class="!activeTag ? 'bg-white text-neutral-950' : 'text-neutral-400 hover:text-white'"
          >
            All
          </button>
          <button
            v-for="tag in tags"
            :key="tag.slug"
            @click="filterTag(tag.slug)"
            class="px-3 py-1.5 rounded-full text-xs font-medium transition-all"
            :class="activeTag === tag.slug ? 'bg-white text-neutral-950' : 'text-neutral-400 hover:text-white'"
          >
            {{ tag.name }}
          </button>
        </div>
      </div>
    </header>

    <main class="pt-24 pb-12 px-4 sm:px-6 lg:px-8">
      <div v-if="albums.length === 0" class="text-center text-neutral-600 py-24">
        <p v-if="activeTag">No albums found with this tag.</p>
        <p v-else>No albums published yet.</p>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-1">
        <Link
          v-for="album in albums"
          :key="album.id"
          :href="`/albums/${album.slug}`"
          class="group relative aspect-[4/3] overflow-hidden bg-neutral-900"
        >
          <img
            v-if="album.cover_url"
            :src="album.cover_url"
            :alt="album.title"
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
          />
          <div v-else class="w-full h-full flex items-center justify-center text-neutral-700">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>

          <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

          <div class="absolute top-3 right-3 flex flex-wrap gap-1.5 justify-end opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-75">
            <span
              v-for="tag in album.tags"
              :key="tag.slug"
              class="text-xs text-white/90 px-2.5 py-1 rounded-full bg-white/10 backdrop-blur-md"
            >
              {{ tag.name }}
            </span>
          </div>

          <div class="absolute bottom-3 right-3 flex items-center gap-2 text-white/90 opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">
            <span class="flex items-center gap-1.5 text-sm font-light bg-white/10 backdrop-blur-md px-2.5 py-1 rounded-full">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              {{ album.image_count }}
            </span>
          </div>
        </Link>
      </div>
    </main>
  </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";

export default {
  components: { Link },
  props: {
    albums: Array,
    tags: Array,
    activeTag: String,
  },
  methods: {
    filterTag(slug) {
      if (!slug) {
        this.$inertia.get("/");
      } else {
        this.$inertia.get(`/?tag=${slug}`);
      }
    },
  },
};
</script>