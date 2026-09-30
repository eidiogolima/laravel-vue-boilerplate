<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';

type Props = {
    id: string;
    label: string;
    name?: string;
    description?: string;
    error?: string;
    disabled?: boolean;
};

withDefaults(defineProps<Props>(), {
    disabled: false,
});

const model = defineModel<boolean>({ default: false });
</script>

<template>
    <div class="grid gap-2">
        <div class="flex items-start gap-3">
            <Checkbox
                :id="id"
                v-model="model"
                :name="name ?? id"
                :disabled="disabled"
                :aria-invalid="Boolean(error)"
                class="mt-0.5"
            />
            <div class="grid gap-1">
                <Label :for="id" class="leading-snug">{{ label }}</Label>
                <p v-if="description" class="text-sm text-muted-foreground">
                    {{ description }}
                </p>
            </div>
        </div>
        <InputError :message="error" />
    </div>
</template>
