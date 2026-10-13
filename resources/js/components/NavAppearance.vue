<script setup lang="ts">
import { computed } from 'vue';
import AppearanceSegmentedControl from '@/components/appearance/AppearanceSegmentedControl.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { appearanceOptions, useAppearance } from '@/composables/useAppearance';
import { trans } from '@/lib/translation';

const { appearance } = useAppearance();
const { isMobile, state } = useSidebar();

const isCollapsed = computed(
    () => !isMobile.value && state.value === 'collapsed',
);

const currentOption = computed(
    () =>
        appearanceOptions.find((option) => option.value === appearance.value) ??
        appearanceOptions[appearanceOptions.length - 1],
);
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu v-if="isCollapsed">
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton
                        :tooltip="trans('Theme')"
                        data-test="sidebar-appearance-button"
                    >
                        <component :is="currentOption.icon" />
                        <span>{{ trans('Theme') }}</span>
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="w-48 rounded-xl p-2"
                    side="right"
                    align="end"
                    :side-offset="8"
                >
                    <DropdownMenuLabel
                        class="px-0.5 pt-0 pb-2 text-xs text-muted-foreground"
                    >
                        {{ trans('Theme') }}
                    </DropdownMenuLabel>
                    <AppearanceSegmentedControl />
                </DropdownMenuContent>
            </DropdownMenu>

            <div v-else class="flex items-center gap-2 px-2 py-1">
                <span class="shrink-0 text-xs text-sidebar-foreground/70">
                    {{ trans('Theme') }}
                </span>
                <AppearanceSegmentedControl class="ml-auto max-w-36" />
            </div>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
