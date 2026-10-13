<script setup lang="ts">
import type { FormComponentProps } from '@inertiajs/core';
import { Form, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { trans } from '@/lib/translation';
import { index as usersIndex } from '@/routes/admin/users';
import type { ManagedUser, UserRole } from '@/types';

const {
    user = null,
    isPasswordRequired = true,
    isRoleLocked = false,
} = defineProps<{
    action: Pick<FormComponentProps, 'action' | 'method'>;
    roles: UserRole[];
    submitLabel: string;
    user?: ManagedUser | null;
    isPasswordRequired?: boolean;
    isRoleLocked?: boolean;
}>();

const roleLabels: Record<UserRole, string> = {
    admin: trans('Administrator'),
    user: trans('User'),
};
</script>

<template>
    <Form
        v-bind="action"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <Card>
            <CardHeader>
                <CardTitle>{{ trans('Account details') }}</CardTitle>
                <CardDescription>
                    {{ trans('Name, email address and access level.') }}
                </CardDescription>
            </CardHeader>
            <CardContent class="grid gap-6 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="name">{{ trans('Name') }}</Label>
                    <Input
                        id="name"
                        name="name"
                        required
                        autocomplete="off"
                        :default-value="user?.name"
                        :placeholder="trans('Full name')"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">{{ trans('Email address') }}</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autocomplete="off"
                        :default-value="user?.email"
                        :placeholder="trans('email@example.com')"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2 sm:col-span-2">
                    <Label for="role">{{ trans('Role') }}</Label>
                    <Select
                        name="role"
                        :default-value="user?.role ?? 'user'"
                        :disabled="isRoleLocked"
                    >
                        <SelectTrigger id="role" class="w-full sm:w-64">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="role in roles"
                                :key="role"
                                :value="role"
                            >
                                {{ roleLabels[role] }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <input
                        v-if="isRoleLocked"
                        type="hidden"
                        name="role"
                        :value="user?.role"
                    />
                    <p
                        v-if="isRoleLocked"
                        class="text-sm text-muted-foreground"
                    >
                        {{ trans('You cannot change your own role.') }}
                    </p>
                    <InputError :message="errors.role" />
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>{{ trans('Password') }}</CardTitle>
                <CardDescription>
                    {{
                        isPasswordRequired
                            ? trans('Set the initial password for this user.')
                            : trans('Leave blank to keep the current password.')
                    }}
                </CardDescription>
            </CardHeader>
            <CardContent class="grid gap-6 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="password">{{ trans('Password') }}</Label>
                    <PasswordInput
                        id="password"
                        name="password"
                        :required="isPasswordRequired"
                        autocomplete="new-password"
                        :placeholder="trans('Password')"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">{{
                        trans('Confirm password')
                    }}</Label>
                    <PasswordInput
                        id="password_confirmation"
                        name="password_confirmation"
                        :required="isPasswordRequired"
                        autocomplete="new-password"
                        :placeholder="trans('Confirm password')"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>
            </CardContent>
            <CardFooter class="justify-end gap-2 border-t pt-6">
                <Button variant="outline" as-child>
                    <Link :href="usersIndex()">{{ trans('Cancel') }}</Link>
                </Button>
                <Button
                    type="submit"
                    :disabled="processing"
                    data-test="save-user-button"
                >
                    <Spinner v-if="processing" />
                    {{ submitLabel }}
                </Button>
            </CardFooter>
        </Card>
    </Form>
</template>
