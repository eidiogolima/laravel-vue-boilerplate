<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    BadgeCheck,
    ChevronLeft,
    ChevronRight,
    Pencil,
    Plus,
    Search,
    UserX,
} from '@lucide/vue';
import { useDebounceFn } from '@vueuse/core';
import { ref, watch } from 'vue';
import DeleteUserDialog from '@/components/admin/DeleteUserDialog.vue';
import UserRoleBadge from '@/components/admin/UserRoleBadge.vue';
import Heading from '@/components/Heading.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { getInitials } from '@/composables/useInitials';
import { formatLongDate, formatNumber } from '@/lib/formatters';
import { trans } from '@/lib/translation';
import { create, edit, index } from '@/routes/admin/users';
import type {
    ManagedUser,
    PaginatedResource,
    UserFilters,
    UserRole,
} from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Users', href: index() }],
    },
});

const props = defineProps<{
    users: PaginatedResource<ManagedUser>;
    filters: UserFilters;
    roles: UserRole[];
}>();

const allRolesFilter = 'all';
const roleLabels: Record<UserRole, string> = {
    admin: trans('Administrators'),
    user: trans('Users'),
};

const currentUserId = usePage().props.auth.user.id;
const searchTerm = ref(props.filters.search);
const selectedRole = ref<string>(props.filters.role || allRolesFilter);

const applyFilters = (): void => {
    router.get(
        index.url(),
        {
            search: searchTerm.value.trim() || undefined,
            role:
                selectedRole.value === allRolesFilter
                    ? undefined
                    : selectedRole.value,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
};

const applyFiltersDebounced = useDebounceFn(applyFilters, 300);

watch(searchTerm, applyFiltersDebounced);
watch(selectedRole, applyFilters);

const formatJoinedAt = (isoDateTime: string | null): string =>
    isoDateTime ? formatLongDate(isoDateTime.slice(0, 10)) : '—';
</script>

<template>
    <Head :title="trans('Users')" />

    <div class="flex flex-col gap-6 p-4 md:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
        >
            <Heading
                :title="trans('Users')"
                :description="
                    trans(
                        'Manage who has access to the system and their roles.',
                    )
                "
            />
            <Button as-child>
                <Link :href="create()" data-test="create-user-link">
                    <Plus />
                    {{ trans('New user') }}
                </Link>
            </Button>
        </div>

        <Card>
            <CardContent class="flex flex-col gap-4">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <Search
                            class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            v-model="searchTerm"
                            type="search"
                            class="pl-9"
                            :placeholder="trans('Search by name or email')"
                            :aria-label="trans('Search by name or email')"
                        />
                    </div>
                    <Select v-model="selectedRole">
                        <SelectTrigger
                            class="w-full sm:w-48"
                            :aria-label="trans('Role')"
                        >
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem :value="allRolesFilter">
                                {{ trans('All roles') }}
                            </SelectItem>
                            <SelectItem
                                v-for="role in roles"
                                :key="role"
                                :value="role"
                            >
                                {{ roleLabels[role] }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div class="overflow-x-auto rounded-lg border">
                    <table class="w-full text-sm">
                        <thead
                            class="bg-muted/50 text-left text-muted-foreground"
                        >
                            <tr>
                                <th class="px-4 py-3 font-medium">
                                    {{ trans('User') }}
                                </th>
                                <th class="px-4 py-3 font-medium">
                                    {{ trans('Role') }}
                                </th>
                                <th
                                    class="hidden px-4 py-3 font-medium md:table-cell"
                                >
                                    {{ trans('Status') }}
                                </th>
                                <th
                                    class="hidden px-4 py-3 font-medium lg:table-cell"
                                >
                                    {{ trans('Joined at') }}
                                </th>
                                <th class="px-4 py-3 text-right font-medium">
                                    <span class="sr-only">{{
                                        trans('Actions')
                                    }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                class="transition-colors hover:bg-muted/40"
                            >
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <Avatar class="size-9 rounded-lg">
                                            <AvatarImage
                                                v-if="user.avatar"
                                                :src="user.avatar"
                                                :alt="user.name"
                                            />
                                            <AvatarFallback class="rounded-lg">
                                                {{ getInitials(user.name) }}
                                            </AvatarFallback>
                                        </Avatar>
                                        <div class="flex min-w-0 flex-col">
                                            <span
                                                class="flex items-center gap-2 truncate font-medium"
                                            >
                                                {{ user.name }}
                                                <Badge
                                                    v-if="
                                                        user.id ===
                                                        currentUserId
                                                    "
                                                    variant="outline"
                                                >
                                                    {{ trans('You') }}
                                                </Badge>
                                            </span>
                                            <span
                                                class="truncate text-xs text-muted-foreground"
                                                >{{ user.email }}</span
                                            >
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <UserRoleBadge :role="user.role" />
                                </td>
                                <td class="hidden px-4 py-3 md:table-cell">
                                    <span
                                        v-if="user.email_verified_at"
                                        class="inline-flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400"
                                    >
                                        <BadgeCheck class="size-4" />
                                        {{ trans('Verified') }}
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1.5 text-amber-700 dark:text-amber-400"
                                    >
                                        <UserX class="size-4" />
                                        {{ trans('Unverified') }}
                                    </span>
                                </td>
                                <td
                                    class="hidden px-4 py-3 text-muted-foreground lg:table-cell"
                                >
                                    {{ formatJoinedAt(user.created_at) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div
                                        class="flex items-center justify-end gap-1"
                                    >
                                        <Button
                                            variant="ghost"
                                            size="icon-sm"
                                            as-child
                                        >
                                            <Link
                                                :href="edit(user.id)"
                                                :aria-label="trans('Edit user')"
                                                data-test="edit-user-link"
                                            >
                                                <Pencil />
                                            </Link>
                                        </Button>
                                        <DeleteUserDialog
                                            v-if="user.can_be_deleted"
                                            :user="user"
                                        />
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!users.data.length">
                                <td
                                    colspan="5"
                                    class="px-4 py-10 text-center text-muted-foreground"
                                >
                                    {{ trans('No users found.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="flex flex-col items-center justify-between gap-3 text-sm text-muted-foreground sm:flex-row"
                >
                    <span class="tabular-nums">
                        {{
                            trans('Showing :from–:to of :total', {
                                from: formatNumber(users.meta.from ?? 0),
                                to: formatNumber(users.meta.to ?? 0),
                                total: formatNumber(users.meta.total),
                            })
                        }}
                    </span>
                    <div class="flex items-center gap-2">
                        <Button
                            v-if="users.links.prev"
                            variant="outline"
                            size="icon-sm"
                            as-child
                        >
                            <Link
                                :href="users.links.prev"
                                :aria-label="trans('Previous page')"
                                preserve-scroll
                            >
                                <ChevronLeft />
                            </Link>
                        </Button>
                        <Button
                            v-else
                            variant="outline"
                            size="icon-sm"
                            disabled
                            :aria-label="trans('Previous page')"
                        >
                            <ChevronLeft />
                        </Button>
                        <span class="tabular-nums">
                            {{
                                trans('Page :current of :last', {
                                    current: users.meta.current_page,
                                    last: users.meta.last_page,
                                })
                            }}
                        </span>
                        <Button
                            v-if="users.links.next"
                            variant="outline"
                            size="icon-sm"
                            as-child
                        >
                            <Link
                                :href="users.links.next"
                                :aria-label="trans('Next page')"
                                preserve-scroll
                            >
                                <ChevronRight />
                            </Link>
                        </Button>
                        <Button
                            v-else
                            variant="outline"
                            size="icon-sm"
                            disabled
                            :aria-label="trans('Next page')"
                        >
                            <ChevronRight />
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
