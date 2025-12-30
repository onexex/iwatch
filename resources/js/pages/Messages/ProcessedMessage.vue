<template>
    <Head title="SMS Inbox" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <Table>
                <TableCaption>A list of processed messages.</TableCaption>
                <TableHeader>
                    <TableRow>
                    <TableHead class="w-[180px]">Classification</TableHead>
                    <TableHead class="w-[180px]">File Number</TableHead>
                    <TableHead class="w-[180px]">Reference</TableHead>
                    <TableHead class="w-[180px]">Subject</TableHead>
                    <TableHead class="w-[180px]">Date of Report</TableHead>
                    <TableHead class="w-[180px]">Reporter</TableHead>
                    <TableHead class="w-[180px]">Designation</TableHead>
                    <TableHead class="w-[180px]">Evaluation</TableHead>
                    <TableHead class="w-[180px]">Source</TableHead>
                    <TableHead class="w-[180px]">Date Acquired</TableHead>
                    <TableHead class="w-[180px]">Manner Acquired</TableHead>
                    <TableHead class="w-[180px]">Information Proper</TableHead>
                    <TableHead class="w-[180px]">Analysis</TableHead>
                    <TableHead class="w-[180px]">Actions</TableHead>
                    <TableHead class="w-[180px]">Address</TableHead>
                    <TableHead class="w-[180px]">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow
                        v-for="sms in messages.data"
                        :key="sms.id"
                    >
                        <TableCell class="font-medium">
                            {{ sms.classification.name }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.file_number }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.reference }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.subject }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.date_of_report }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.reporter }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.designation }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.evaluation }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.source }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.date_acquired }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.manner_acquired }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.information_proper }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.analysis }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.actions }}
                        </TableCell>
                        <TableCell class="font-medium">
                            {{ sms.barangay.barangay }} , {{ sms.barangay.city_municipality }} , {{ sms.barangay.province }}
                        </TableCell>
                        <TableCell>
                            <Button
                                variant="secondary"
                                size="sm"
                                @click="funcOpenDialog(sms.attachments)"
                            >
                                <EyeIcon className="w-4 h-4 mr-1" />
                                View Attachment
                            </Button>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="messages.data.length === 0">
                        <TableCell colspan="3" class="text-center py-6 text-muted-foreground">
                            No messages found
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <Pagination
                :items-per-page="messages.per_page"
                :total="messages.total"
                :default-page="messages.current_page"
                v-slot="{ page }"
            >
                <PaginationContent v-slot="{ items }">
                    <PaginationPrevious @click="changePage(page - 1)" />

                    <template v-for="(item, index) in items" :key="index">
                        <PaginationItem
                        v-if="item.type === 'page'"
                        :value="item.value"
                        :is-active="item.value === page"
                        @click="changePage(item.value)"
                        >
                        {{ item.value }}
                        </PaginationItem>

                        <PaginationEllipsis
                        v-else
                        :index="index"
                        />
                    </template>

                    <PaginationNext @click="changePage(page + 1)" />
                </PaginationContent>
            </Pagination>
            <Dialog v-model:open="openDialog">
                <DialogContent class="max-w-3xl">
                    <DialogHeader>
                        <DialogTitle>Attachments</DialogTitle>
                    </DialogHeader>

                    <div v-if="incidentattachments.length === 0" class="text-muted-foreground">
                        No attachments
                    </div>

                    <div
                        v-else
                        class="grid grid-cols-2 md:grid-cols-3 gap-4"
                    >
                        <div
                            v-for="file in incidentattachments"
                            :key="file.id"
                            class="border rounded p-2"
                        >
                            <img
                                :src="`/storage/${file.url}`"
                                class="w-full h-32 object-cover rounded"
                            />
                            <a
                                :href="`/storage/${file.url}`"
                                target="_blank"
                                class="block mt-2 text-sm text-primary hover:underline text-center"
                            >
                                Open
                            </a>
                        </div>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
    import AppLayout from '@/layouts/AppLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { type BreadcrumbItem } from '@/types'
    import { Button } from "@/components/ui/button"

    import {
        Table,
        TableBody,
        TableCell,
        TableHead,
        TableHeader,
        TableRow,
        TableCaption
    } from '@/components/ui/table'

    import {
        Pagination,
        PaginationContent,
        PaginationItem,
        PaginationPrevious,
        PaginationNext,
        PaginationEllipsis,
    } from '@/components/ui/pagination'
    import {
        Dialog,
        DialogContent,
        DialogHeader,
        DialogTitle,
    } from "@/components/ui/dialog"
    import { EyeIcon } from 'lucide-vue-next'
    import { ref } from 'vue'

    interface IncidentAttachment {
        id: number
        url: string
    }    

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Processed Messages',
            href: '/processed-messages',
        },
    ]

    defineProps({
        messages: {
            type: Object,
            default: () => {}
        },
    })

    const openDialog = ref(false)
    const incidentattachments = ref<IncidentAttachment[]>([])

    function changePage(page: number) {
        router.get(
            `/processed-messages?page=${page}`,
            {},
            { preserveScroll: true }
        )
    }

    function funcOpenDialog(attachments: Array<[]>) {
        incidentattachments.value = attachments.flat()
        openDialog.value = true
    }
</script>
