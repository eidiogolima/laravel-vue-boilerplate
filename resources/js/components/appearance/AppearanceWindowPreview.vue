<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@/lib/utils';
import type { ResolvedAppearance } from '@/types';

const { theme } = defineProps<{
    theme: ResolvedAppearance;
}>();

const palette = computed(() =>
    theme === 'dark'
        ? {
              wallpaper:
                  'bg-linear-to-br from-indigo-950 via-slate-900 to-fuchsia-950',
              window: 'bg-neutral-800 ring-white/10',
              sidebar: 'bg-neutral-900/80',
              line: 'bg-neutral-600',
              mutedLine: 'bg-neutral-700',
          }
        : {
              wallpaper:
                  'bg-linear-to-br from-sky-200 via-indigo-200 to-pink-200',
              window: 'bg-white ring-black/5',
              sidebar: 'bg-neutral-100',
              line: 'bg-neutral-300',
              mutedLine: 'bg-neutral-200',
          },
);
</script>

<template>
    <div :class="cn('absolute inset-0', palette.wallpaper)">
        <div
            :class="
                cn(
                    'absolute inset-x-[14%] top-[18%] bottom-0 flex overflow-hidden rounded-t-md shadow-lg ring-1',
                    palette.window,
                )
            "
        >
            <div
                :class="
                    cn('flex w-[34%] flex-col gap-1 p-1.5', palette.sidebar)
                "
            >
                <div class="mb-0.5 flex gap-0.5">
                    <span class="size-1 rounded-full bg-[#ff5f57]" />
                    <span class="size-1 rounded-full bg-[#febc2e]" />
                    <span class="size-1 rounded-full bg-[#28c840]" />
                </div>
                <span class="h-1 w-4/5 rounded-full bg-blue-500" />
                <span
                    :class="cn('h-1 w-3/5 rounded-full', palette.mutedLine)"
                />
                <span
                    :class="cn('h-1 w-2/3 rounded-full', palette.mutedLine)"
                />
            </div>
            <div class="flex flex-1 flex-col gap-1 p-1.5 pt-3">
                <span :class="cn('h-1.5 w-1/2 rounded-full', palette.line)" />
                <span
                    :class="cn('h-1 w-full rounded-full', palette.mutedLine)"
                />
                <span
                    :class="cn('h-1 w-5/6 rounded-full', palette.mutedLine)"
                />
                <span
                    :class="cn('h-1 w-2/3 rounded-full', palette.mutedLine)"
                />
            </div>
        </div>
    </div>
</template>
