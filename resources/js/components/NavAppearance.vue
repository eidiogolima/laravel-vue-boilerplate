<script setup lang="ts">
import { Check } from '@lucide/vue';
import { computed } from 'vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
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

const { appearance, updateAppearance } = useAppearance();
const { isMobile, state } = useSidebar();

const currentOption = computed(
    () =>
        appearanceOptions.find((option) => option.value === appearance.value) ??
        appearanceOptions[appearanceOptions.length - 1],
);
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton
                        :tooltip="trans('Theme')"
                        data-test="sidebar-appearance-button"
                    >
                        <component :is="currentOption.icon" />
                        <span>{{ trans('Theme') }}</span>
                        <span class="ml-auto text-xs text-muted-foreground">
                            {{ trans(currentOption.label) }}
                        </span>
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="min-w-44 rounded-lg"
                    :side="
                        isMobile
                            ? 'top'
                            : state === 'collapsed'
                              ? 'right'
                              : 'top'
                    "
                    align="start"
                    :side-offset="4"
                >
                    <DropdownMenuLabel class="text-xs text-muted-foreground">
                        {{ trans('Theme') }}
                    </DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem
                        v-for="option in appearanceOptions"
                        :key="option.value"
                        class="cursor-pointer"
                        @select="updateAppearance(option.value)"
                    >
                        <component :is="option.icon" />
                        {{ trans(option.label) }}
                        <Check
                            v-if="appearance === option.value"
                            class="ml-auto"
                        />
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
