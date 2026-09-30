<script setup lang="ts">
import type { CalendarRootEmits, CalendarRootProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { ChevronLeft, ChevronRight } from "@lucide/vue"
import { reactiveOmit } from "@vueuse/core"
import {
  CalendarCell,
  CalendarCellTrigger,
  CalendarGrid,
  CalendarGridBody,
  CalendarGridHead,
  CalendarGridRow,
  CalendarHeadCell,
  CalendarHeader,
  CalendarHeading,
  CalendarNext,
  CalendarPrev,
  CalendarRoot,
  useForwardPropsEmits,
} from "reka-ui"
import { buttonVariants } from "@/components/ui/button"
import { cn } from "@/lib/utils"

const props = defineProps<CalendarRootProps & { class?: HTMLAttributes["class"] }>()
const emits = defineEmits<CalendarRootEmits>()

const delegatedProps = reactiveOmit(props, "class")

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <CalendarRoot
    v-slot="{ grid, weekDays }"
    data-slot="calendar"
    :class="cn('p-3', props.class)"
    v-bind="forwarded"
  >
    <CalendarHeader class="relative flex w-full items-center justify-between">
      <CalendarPrev
        :class="cn(buttonVariants({ variant: 'outline', size: 'icon-sm' }), 'bg-transparent')"
      >
        <ChevronLeft class="size-4" />
      </CalendarPrev>
      <CalendarHeading class="text-sm font-medium capitalize" />
      <CalendarNext
        :class="cn(buttonVariants({ variant: 'outline', size: 'icon-sm' }), 'bg-transparent')"
      >
        <ChevronRight class="size-4" />
      </CalendarNext>
    </CalendarHeader>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row">
      <CalendarGrid
        v-for="month in grid"
        :key="month.value.toString()"
        class="w-full border-collapse"
      >
        <CalendarGridHead>
          <CalendarGridRow class="flex">
            <CalendarHeadCell
              v-for="day in weekDays"
              :key="day"
              class="w-9 text-[0.8rem] font-normal text-muted-foreground capitalize"
            >
              {{ day }}
            </CalendarHeadCell>
          </CalendarGridRow>
        </CalendarGridHead>
        <CalendarGridBody>
          <CalendarGridRow
            v-for="(weekDates, index) in month.rows"
            :key="`weekDate-${index}`"
            class="mt-1 flex w-full"
          >
            <CalendarCell
              v-for="weekDate in weekDates"
              :key="weekDate.toString()"
              :date="weekDate"
              class="relative p-0 text-center text-sm"
            >
              <CalendarCellTrigger
                :day="weekDate"
                :month="month.value"
                :class="
                  cn(
                    'inline-flex size-9 items-center justify-center rounded-md text-sm font-normal transition-colors outline-none',
                    'hover:bg-accent hover:text-accent-foreground focus-visible:ring-[3px] focus-visible:ring-ring/50',
                    'data-[today]:bg-accent data-[today]:font-semibold data-[today]:text-accent-foreground',
                    'data-[selected]:bg-primary data-[selected]:text-primary-foreground data-[selected]:hover:bg-primary',
                    'data-[outside-view]:text-muted-foreground data-[outside-view]:opacity-50',
                    'data-[disabled]:pointer-events-none data-[disabled]:opacity-40',
                    'data-[unavailable]:text-destructive data-[unavailable]:line-through',
                  )
                "
              />
            </CalendarCell>
          </CalendarGridRow>
        </CalendarGridBody>
      </CalendarGrid>
    </div>
  </CalendarRoot>
</template>
