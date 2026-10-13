<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import PageContainer from '@/components/PageContainer.vue';
import UserForm from '@/components/admin/UserForm.vue';
import Heading from '@/components/Heading.vue';
import { trans } from '@/lib/translation';
import { create, index } from '@/routes/admin/users';
import type { UserRole } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Users', href: index() },
            { title: 'New user', href: create() },
        ],
    },
});

defineProps<{
    roles: UserRole[];
}>();
</script>

<template>
    <Head :title="trans('New user')" />

    <PageContainer size="narrow">
        <Heading
            :title="trans('New user')"
            :description="
                trans('Create an account and choose its access level.')
            "
        />

        <UserForm
            :action="UserController.store.form()"
            :roles="roles"
            :submit-label="trans('Create user')"
        />
    </PageContainer>
</template>
