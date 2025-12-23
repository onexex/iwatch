<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { type BreadcrumbItem } from '@/types'

// shadcn/ui table components
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

defineProps<{
  messages: {
    id: number
    sender: string
    message: string
    received_at: string
  }[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Messages',
    href: '/sms',
  },
]
</script>

<template>
  <Head title="SMS Inbox" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Messages</h1>

      <div class="border rounded-lg overflow-hidden">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="w-[180px]">Sender</TableHead>
              <TableHead>Message</TableHead>
              <TableHead class="w-[180px]">Received At</TableHead>
              <TableHead class="w-[180px]">Action</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody>
            <TableRow
              v-for="sms in messages"
              :key="sms.id"
            >
              <TableCell class="font-medium">
                {{ sms.sender }}
              </TableCell>

              <TableCell class="max-w-xl truncate">
                {{ sms.message }}
              </TableCell>

              <TableCell class="text-sm text-muted-foreground">
                {{ sms.received_at }}
              </TableCell>

            <TableCell class="text-sm text-muted-foreground">
              
              </TableCell>
            </TableRow>

            <!-- Empty state -->
            <TableRow v-if="messages.length === 0">
              <TableCell colspan="3" class="text-center py-6 text-muted-foreground">
                No messages found
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>
