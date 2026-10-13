<script setup lang="ts">
import { ShieldCheck, User as UserIcon } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { trans } from '@/lib/translation';
import type { UserRole } from '@/types';

defineProps<{
    role: UserRole;
}>();
</script>

<template>
    <Badge v-if="role === 'admin'" class="gap-1">
        <ShieldCheck />
        {{ trans('Administrator') }}
    </Badge>
    <Badge v-else variant="secondary" class="gap-1">
        <UserIcon />
        {{ trans('User') }}
    </Badge>
</template>
