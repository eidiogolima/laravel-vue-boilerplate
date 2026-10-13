<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { Trash2 } from '@lucide/vue';
import ErrorLogController from '@/actions/App/Http/Controllers/Admin/ErrorLogController';
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

defineProps<{
    errorLogId: number;
}>();
</script>

<template>
    <Dialog>
        <DialogTrigger as-child>
            <Button
                variant="ghost"
                size="icon-sm"
                class="text-muted-foreground hover:text-destructive"
                :aria-label="trans('Delete error log')"
                data-test="delete-error-log-button"
            >
                <Trash2 />
            </Button>
        </DialogTrigger>
        <DialogContent>
            <Form
                v-bind="ErrorLogController.destroy.form(errorLogId)"
                class="flex flex-col gap-6"
                v-slot="{ processing }"
            >
                <DialogHeader>
                    <DialogTitle>{{ trans('Delete error log') }}</DialogTitle>
                    <DialogDescription>
                        {{
                            trans(
                                'Only this record will be deleted. Other occurrences of the same error are kept. This action cannot be undone.',
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
                        data-test="confirm-delete-error-log-button"
                    >
                        <Spinner v-if="processing" />
                        {{ trans('Delete error log') }}
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
