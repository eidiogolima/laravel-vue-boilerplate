<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { Trash2 } from '@lucide/vue';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';
import { trans } from '@/lib/translation';
import type { ManagedUser } from '@/types';

defineProps<{
    user: ManagedUser;
}>();
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button
                variant="ghost"
                size="icon-sm"
                class="text-muted-foreground hover:text-destructive"
                :aria-label="trans('Delete user')"
                data-test="delete-user-button"
            >
                <Trash2 />
            </Button>
        </DialogTrigger>
        <DialogContent>
            <Form
                v-bind="UserController.destroy.form(user.id)"
                :options="{ preserveScroll: true }"
                class="flex flex-col gap-6"
                v-slot="{ processing }"
            >
                <DialogHeader>
                    <DialogTitle>{{ trans('Delete user') }}</DialogTitle>
                    <DialogDescription>
                        {{
                            trans(
                                'Are you sure you want to delete :name? This action cannot be undone.',
                                { name: user.name },
                            )
                        }}
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary">{{
                            trans('Cancel')
                        }}</Button>
                    </DialogClose>
                    <Button
                        type="submit"
                        variant="destructive"
                        :disabled="processing"
                        data-test="confirm-delete-user-button"
                    >
                        <Spinner v-if="processing" />
                        {{ trans('Delete user') }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
