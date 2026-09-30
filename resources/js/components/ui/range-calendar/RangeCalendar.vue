<script setup lang="ts">
import type { RangeCalendarRootEmits, RangeCalendarRootProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { ChevronLeft, ChevronRight } from "@lucide/vue"
import { reactiveOmit } from "@vueuse/core"
import {
  RangeCalendarCell,
  RangeCalendarCellTrigger,
  RangeCalendarGrid,
  RangeCalendarGridBody,
  RangeCalendarGridHead,
  RangeCalendarGridRow,
  RangeCalendarHeadCell,
  RangeCalendarHeader,
  RangeCalendarHeading,
  RangeCalendarNext,
  RangeCalendarPrev,
  RangeCalendarRoot,
  useForwardPropsEmits,
} from "reka-ui"
import { buttonVariants } from "@/components/ui/button"
import { cn } from "@/lib/utils"

const props = defineProps<RangeCalendarRootProps & { class?: HTMLAttributes["class"] }>()
const emits = defineEmits<RangeCalendarRootEmits>()

const delegatedProps = reactiveOmit(props, "class")

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <RangeCalendarRoot
    v-slot="{ grid, weekDays }"
    data-slot="range-calendar"
    :class="cn('p-3', props.class)"
    v-bind="forwarded"
  >
    <RangeCalendarHeader class="relative flex w-full items-center justify-between">
      <RangeCalendarPrev
        :class="cn(buttonVariants({ variant: 'outline', size: 'icon-sm' }), 'bg-transparent')"
      >
        <ChevronLeft class="size-4" />
      </RangeCalendarPrev>
      <RangeCalendarHeading class="text-sm font-medium capitalize" />
      <RangeCalendarNext
        :class="cn(buttonVariants({ variant: 'outline', size: 'icon-sm' }), 'bg-transparent')"
      >
        <ChevronRight class="size-4" />
      </RangeCalendarNext>
    </RangeCalendarHeader>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:gap-6">
      <RangeCalendarGrid
        v-for="month in grid"
        :key="month.value.toString()"
        class="w-full border-collapse"
      >
        <RangeCalendarGridHead>
          <RangeCalendarGridRow class="flex">
            <RangeCalendarHeadCell
              v-for="day in weekDays"
              :key="day"
              class="w-9 text-[0.8rem] font-normal text-muted-foreground capitalize"
            >
              {{ day }}
            </RangeCalendarHeadCell>
          </RangeCalendarGridRow>
        </RangeCalendarGridHead>
        <RangeCalendarGridBody>
          <RangeCalendarGridRow
            v-for="(weekDates, index) in month.rows"
            :key="`weekDate-${index}`"
            class="mt-1 flex w-full"
          >
            <RangeCalendarCell
              v-for="weekDate in weekDates"
              :key="weekDate.toString()"
              :date="weekDate"
              :class="
                cn(
                  'relative p-0 text-center text-sm',
                  '[&:has([data-selected])]:bg-accent',
                  'first:[&:has([data-selected])]:rounded-l-md last:[&:has([data-selected])]:rounded-r-md',
                  '[&:has([data-selection-start])]:rounded-l-md [&:has([data-selection-end])]:rounded-r-md',
                  '[&:has([data-outside-view][data-selected])]:bg-accent/50',
                )
              "
            >
              <RangeCalendarCellTrigger
                :day="weekDate"
                :month="month.value"
                :class="
                  cn(
                    'inline-flex size-9 items-center justify-center rounded-md text-sm font-normal transition-colors outline-none',
                    'hover:bg-accent hover:text-accent-foreground focus-visible:ring-[3px] focus-visible:ring-ring/50',
                    'data-[today]:font-semibold data-[today]:underline data-[today]:underline-offset-4',
                    'data-[selected]:text-accent-foreground',
                    'data-[highlighted]:bg-accent',
                    'data-[selection-start]:bg-primary data-[selection-start]:text-primary-foreground data-[selection-start]:hover:bg-primary',
                    'data-[selection-end]:bg-primary data-[selection-end]:text-primary-foreground data-[selection-end]:hover:bg-primary',
                    'data-[outside-view]:text-muted-foreground data-[outside-view]:opacity-50',
                    'data-[disabled]:pointer-events-none data-[disabled]:opacity-40',
                    'data-[unavailable]:text-destructive data-[unavailable]:line-through',
                  )
                "
              />
            </RangeCalendarCell>
          </RangeCalendarGridRow>
        </RangeCalendarGridBody>
      </RangeCalendarGrid>
    </div>
  </RangeCalendarRoot>
</template>
