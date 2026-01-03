<template>
    <Head title="SMS Inbox" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
              <h1 class="mb-4 text-2xl font-bold">Verified Information</h1>
             <div class="flex flex-col gap-4">
                <div class="overflow-hidden rounded-xl border bg-card shadow-sm">
                    <div class="relative h-[650px] overflow-auto">
                        <Table>
                            <TableCaption>A list of verified information.</TableCaption>
                            <TableHeader>
                                <TableRow>
                               <TableHead class="w-[220px] px-6 py-4 font-semibold text-foreground">Classification</TableHead>
                                    <TableHead class="w-[180px] px-6 py-4 font-semibold text-foreground">File Number</TableHead>
                                    <TableHead class="w-[180px] px-6 py-4 font-semibold text-foreground">Reference</TableHead>
                                    <TableHead class="w-[200px] px-6 py-4 font-semibold text-foreground">Subject</TableHead>
                                    <TableHead class="w-[180px] px-6 py-4 font-semibold text-foreground">Date of Report</TableHead>
                                    <TableHead class="w-[180px] px-6 py-4 font-semibold text-foreground">Reporter</TableHead>
                                    <TableHead class="w-[180px] px-6 py-4 font-semibold text-foreground">Designation</TableHead>
                                    <TableHead class="w-[150px] px-6 py-4 font-semibold text-foreground text-center">Evaluation</TableHead>
                                    <TableHead class="w-[180px] px-6 py-4 font-semibold text-foreground">Source</TableHead>
                                    <TableHead class="w-[180px] px-6 py-4 font-semibold text-foreground">Date Acquired</TableHead>
                                    <TableHead class="w-[180px] px-6 py-4 font-semibold text-foreground">Manner Acquired</TableHead>
                                    <TableHead class="w-[300px] px-6 py-4 font-semibold text-foreground">Information Proper</TableHead>
                                    <TableHead class="w-[250px] px-6 py-4 font-semibold text-foreground">Analysis</TableHead>
                                     <TableHead  class="w-[180px] px-6 py-4 font-semibold text-foreground">Actions</TableHead>
                                <TableHead  class="w-[180px] px-6 py-4 font-semibold text-foreground">Address</TableHead>
                                <TableHead  class="w-[180px] px-6 py-4 font-semibold text-foreground">Action</TableHead>
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
                                    <TableCell class="gap-2 flex">
                                        <Button
                                            variant="secondary"
                                            size="sm"
                                            @click="funcOpenDialog(sms.attachments)"
                                        >
                                            <EyeIcon className="w-4 h-4 mr-1" />
                                            View Attachment
                                        </Button>
                                        <Button
                                            variant="secondary"
                                            size="sm"
                                            @click="funcDOwnloadIncident(sms.id)"
                                        >
                                            <FileDown className="w-4 h-4 mr-1" />
                                            Download
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
                                        class="cursor-pointer"
                                        @click="changePage(item.value)"
                                    >
                                        {{ item.value }}
                                    </PaginationItem>
                                    <PaginationEllipsis v-else :index="index" />
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
                        <Dialog v-model:open="downloadDialog">
                            <DialogContent class="max-w-3xl">
                                <DialogHeader>
                                    <DialogTitle>Download Incident</DialogTitle>
                                </DialogHeader>
                                <div>
                                    <Input 
                                        v-model="copyfor"
                                        label="Copy For"
                                        placeholder="Copy for"
                                        class="mb-2"
                                        :error="copyforError"
                                    />
                                    <Button
                                        variant="secondary"
                                        class="cursor-pointer"
                                        size="sm"
                                        @click="downloadFile()"
                                    >
                                        <FileDown className="w-4 h-4 mr-1" />
                                        Download
                                    </Button>
                                </div>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>  
            </div>          
        </div>
    </AppLayout>
</template>
<script setup lang="ts">
    import AppLayout from '@/layouts/AppLayout.vue'
    import { Head, router } from '@inertiajs/vue3'
    import { type BreadcrumbItem } from '@/types'
    import { Button } from "@/components/ui/button"
    import Input from '@/components/ui/input/Input.vue';

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
    import { EyeIcon, FileDown } from 'lucide-vue-next'
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
    const incidentId = ref(0)
    const copyfor = ref("")
    const copyforError = ref("")
    const incidentattachments = ref<IncidentAttachment[]>([])

    const downloadDialog = ref(false)

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

    function funcDOwnloadIncident(id: number) {
        incidentId.value = id
        downloadDialog.value = true
    }

    function downloadFile() {
        if (copyfor.value == "") {
            copyforError.value = "Please input copy for"
            return
        }
         const params = new URLSearchParams({
            copyFor: copyfor.value
        })

        const url = `/download-incident/${incidentId.value}?${params.toString()}`

        window.open(url, '_blank', 'noopener,noreferrer')
    }
</script>
