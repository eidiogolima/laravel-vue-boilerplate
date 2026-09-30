<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { trans } from '@/lib/translation';

export type SelectOption = {
    value: string;
    label: string;
    disabled?: boolean;
};

type Props = {
    id: string;
    label: string;
    options: SelectOption[];
    name?: string;
    placeholder?: string;
    description?: string;
    error?: string;
    required?: boolean;
    disabled?: boolean;
};

withDefaults(defineProps<Props>(), {
    required: false,
    disabled: false,
});

const model = defineModel<string>();
</script>

<template>
    <div class="grid gap-2">
        <Label :for="id">{{ label }}</Label>
        <Select
            v-model="model"
            :name="name ?? id"
            :required="required"
            :disabled="disabled"
        >
            <SelectTrigger
                :id="id"
                class="w-full"
                :aria-invalid="Boolean(error)"
            >
                <SelectValue
                    :placeholder="placeholder ?? trans('Select an option')"
                />
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="option in options"
                    :key="option.value"
                    :value="option.value"
                    :disabled="option.disabled"
                >
                    {{ option.label }}
                </SelectItem>
            </SelectContent>
        </Select>
        <p v-if="description" class="text-xs text-muted-foreground">
            {{ description }}
        </p>
        <InputError :message="error" />
    </div>
</template>
