<script setup lang="ts">
import { Head, setLayoutProps, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import UserForm from '@/components/admin/UserForm.vue';
import Heading from '@/components/Heading.vue';
import { trans } from '@/lib/translation';
import { edit, index } from '@/routes/admin/users';
import type { ManagedUser, UserRole } from '@/types';

const props = defineProps<{
    user: ManagedUser;
    roles: UserRole[];
}>();

setLayoutProps({
    breadcrumbs: [
        { title: 'Users', href: index() },
        { title: 'Edit user', href: edit(props.user.id) },
    ],
});

const isOwnAccount = computed(
    () => usePage().props.auth.user.id === props.user.id,
);
</script>

<template>
    <Head :title="trans('Edit user')" />

    <div class="mx-auto flex w-full max-w-3xl flex-col p-4 md:p-6">
        <Heading
            :title="trans('Edit user')"
            :description="
                trans('Update the account details of :name.', {
                    name: user.name,
                })
            "
        />

        <UserForm
            :action="UserController.update.form(user.id)"
            :roles="roles"
            :user="user"
            :is-password-required="false"
            :is-role-locked="isOwnAccount"
            :submit-label="trans('Save changes')"
        />
    </div>
</template>
