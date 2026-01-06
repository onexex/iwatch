<template>
    <Head title="SMS Inbox" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="mb-4 text-2xl font-bold">Verified Information</h1>
            
            <div class="flex flex-col gap-4">
                <div class="items-center w-full gap-4 rounded-lg border bg-card p-4 ">
                    <div class="grid grid-cols-6 gap-3">
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                for="reporter"
                            >
                                Reporters
                            </label>
                            <Select
                                v-model="form.reporter"
                                :error="copyforError"
                                class="w-full mb-2"
                                @change="changeFilter"
                            >
                                <SelectTrigger
                                    class="w-full mb-2"
                                >
                                    <SelectValue
                                        placeholder="Select Reporter"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="item in reporters"
                                        :key="item"
                                        :value="item"
                                        class="w-full"
                                    >
                                        {{ item }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div>
                            <label
                                class="mb-2 block text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                for="source"
                            >
                                Source
                            </label>
                            <Select
                                v-model="form.source"
                                :error="copyforError"
                                @change="changeFilter"
                                class="w-full "
                            >
                                <SelectTrigger
                                    class="w-full mb-2"
                                >
                                    <SelectValue
                                        placeholder="Select Source"
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="item in sources"
                                        :key="item"
                                        :value="item"
                                        class="w-full"
                                    >
                                        {{ item }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <Input 
                            label="Date"
                            type="date"
                            v-model="form.dateFrom"
                            @change="changeDate"
                        />
                        <Input 
                            label="Date"
                            type="date"
                            v-model="form.dateTo"
                            @change="changeDate"
                        />
                        
                        <div class="flex items-center gap-2 mt-auto pb-0.5">
                            <Button 
                                v-if="form.dateFrom || form.dateTo || form.reporter || form.source"
                                variant="outline" 
                                size="sm" 
                                class="h-9 px-3 text-xs font-medium transition-all hover:bg-destructive hover:text-destructive-foreground"
                                @click="clearFilter"
                            >
                                <XIcon class="mr-2 h-3.5 w-3.5 text-black" />
                                Clear Filters
                            </Button>
                        </div>
                    </div>
                </div>
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
                                    <TableCell class="font-medium text-center">
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
                                         <span v-html="sms.information_proper.replace(/(.{30})/g, '$1<br>')"></span>
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
                                    <div>
                                        <Select
                                            v-model="copyfor"
                                            :error="copyforError"
                                            class="w-full  mb-2"
                                        >
                                            <SelectTrigger
                                                class="w-full mb-2"
                                            >
                                                <SelectValue
                                                    placeholder="Select Copy For Watermark"
                                                />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="item in watermarks"
                                                    :key="item.id"
                                                    :value="item.id"
                                                    class="w-full"
                                                >
                                                    {{ item.name }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
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
    import { Head, router, useForm } from '@inertiajs/vue3'
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
    
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
        SelectValue,
    } from '@/components/ui/select';
    import { EyeIcon, FileDown, XIcon } from 'lucide-vue-next'
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

    const props = defineProps({
        messages: {
            type: Object,
            default: () => {}
        },
        watermarks: {
            type: Object,
            default: () => {}
        },
        reporters: {
            type: Object,
            default: () => {}
        },
        sources: {
            type: Object,
            default: () => {}
        },
        filter: {
            type: Object,
            default: () => {}
        },
    })
    

    const form = useForm({
        source: props.filter.source ??'',
        reporter:  props.filter.reporter ??'',
        dateTo:  props.filter.dateTo ??'',
        dateFrom:  props.filter.dateFrom ??'',
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

    function changeFilter() {
        router.get('/processed-messages', {
            reporter: form.reporter,
            source: form.source,
            dateFrom: form.dateFrom,
            dateTo: form.dateTo,
        })
    }

    function changeDate() {
        if (form.dateFrom != '' && form.dateTo != '') {
            router.get('/processed-messages', {
                reporter: form.reporter,
                source: form.source,
                dateFrom: form.dateFrom,
                dateTo: form.dateTo,
            })
        }
    }

    function clearFilter() {
        form.dateFrom = ''
        form.dateTo = ''
        form.source = ''
        form.reporter = ''
        router.get('/processed-messages')
    }

</script>
