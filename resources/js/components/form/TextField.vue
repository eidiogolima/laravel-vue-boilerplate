<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Props = {
    id: string;
    label: string;
    name?: string;
    type?: string;
    placeholder?: string;
    autocomplete?: string;
    description?: string;
    error?: string;
    required?: boolean;
    disabled?: boolean;
};

withDefaults(defineProps<Props>(), {
    type: 'text',
    required: false,
    disabled: false,
});

const model = defineModel<string>({ default: '' });
</script>

<template>
    <div class="grid gap-2">
        <Label :for="id">{{ label }}</Label>
        <Input
            :id="id"
            v-model="model"
            :name="name ?? id"
            :type="type"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            :required="required"
            :disabled="disabled"
            :aria-invalid="Boolean(error)"
        />
        <p v-if="description" class="text-xs text-muted-foreground">
            {{ description }}
        </p>
        <InputError :message="error" />
    </div>
</template>
