<template>
    <Head title="SMS Inbox" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="mb-4 text-2xl font-bold">Verified Information</h1>
             <div class="flex flex-col gap-4">
                <div class="overflow-hidden rounded-xl border bg-card shadow-sm">
                    <div class="relative h-[650px] overflow-auto">
                        <Table>
                            <TableHeader class="sticky top-0 z-10 bg-muted/90 backdrop-blur-md">
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
                                    <TableHead class="w-[180px] px-6 py-4 font-semibold text-foreground">Actions</TableHead>
                                    <TableHead class="w-[250px] px-6 py-4 font-semibold text-foreground">Address</TableHead>
                                </TableRow>
                            </TableHeader>

                            <TableBody>
                                <TableRow
                                    v-for="sms in messages.data"
                                    :key="sms.id"
                                    class="group border-b transition-colors last:border-0 hover:bg-muted/30"
                                >
                                    <TableCell class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary ring-1 ring-primary/20 uppercase">
                                                {{ sms.classification.name.substring(0, 2) }}
                                            </div>
                                            <span class="font-semibold tracking-tight text-foreground truncate max-w-[120px]">
                                                {{ sms.classification.name }}
                                            </span>
                                        </div>
                                    </TableCell>

                                    <TableCell class="font-medium text-muted-foreground/80">
                                        {{ sms.file_number }}
                                    </TableCell>

                                    <TableCell class="text-xs italic text-muted-foreground">
                                        {{ sms.reference }}
                                    </TableCell>

                                    <TableCell class="font-medium">
                                        {{ sms.subject }}
                                    </TableCell>

                                    <TableCell class="text-sm whitespace-nowrap">
                                        {{ sms.date_of_report }}
                                    </TableCell>

                                    <TableCell class="font-semibold text-foreground/90">
                                        {{ sms.reporter }}
                                    </TableCell>

                                    <TableCell class="text-xs text-muted-foreground uppercase tracking-tight">
                                        {{ sms.designation }}
                                    </TableCell>

                                <TableCell class="text-center">
                                            <span v-if="sms.evaluation" :class="[
                                                'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border',
                                                (sms.evaluation.startsWith('A') || sms.evaluation.startsWith('1'))
                                                    ? 'bg-green-50 text-green-700 border-green-200 shadow-sm' 
                                                    : 'bg-amber-50 text-amber-700 border-amber-200 shadow-sm'
                                            ]">
                                                {{ sms.evaluation }}
                                            </span>
                                            <span v-else class="text-[10px] text-muted-foreground italic opacity-50">
                                                N/A
                                            </span>
                                        </TableCell>

                                    <TableCell class="text-sm">
                                        {{ sms.source }}
                                    </TableCell>

                                    <TableCell class="text-[11px] font-medium text-muted-foreground">
                                        {{ sms.date_acquired }}
                                    </TableCell>

                                    <TableCell class="text-xs italic text-muted-foreground/70">
                                        {{ sms.manner_acquired }}
                                    </TableCell>

                                    <TableCell class="max-w-md">
                                        <p class="line-clamp-2 text-sm text-muted-foreground transition-colors group-hover:text-foreground">
                                            {{ sms.information_proper }}
                                        </p>
                                    </TableCell>

                                    <TableCell class="max-w-xs">
                                        <p class="line-clamp-2 text-sm italic text-muted-foreground">
                                            {{ sms.analysis }}
                                        </p>
                                    </TableCell>

                                    <TableCell class="text-sm font-medium">
                                        {{ sms.actions }}
                                    </TableCell>

                                    <TableCell class="px-6 py-4">
                                        <div class="flex flex-col text-[11px] leading-relaxed">
                                            <span class="font-bold text-foreground/80 uppercase">{{ sms.barangay.barangay }}</span>
                                            <span class="text-muted-foreground">{{ sms.barangay.city_municipality }}, {{ sms.barangay.province }}</span>
                                        </div>
                                    </TableCell>
                                </TableRow>

                                <TableRow v-if="messages.data.length === 0">
                                    <TableCell colspan="15" class="h-[400px] text-center">
                                        <div class="flex flex-col items-center justify-center text-muted-foreground">
                                            <p class="text-lg font-light">No messages found</p>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div class="flex items-center justify-between border-t bg-card px-6 py-4">
                        <div class="flex flex-col">
                            <p class="text-xs text-muted-foreground italic">
                                Showing {{ messages.data.length }} of {{ messages.total }} records
                            </p>
                        </div>

                        <Pagination
                            :items-per-page="messages.per_page"
                            :total="messages.total"
                            :default-page="messages.current_page"
                            v-slot="{ page }"
                        >
                            <PaginationContent v-slot="{ items }">
                                <PaginationPrevious class="cursor-pointer" @click="changePage(page - 1)" />

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

                                <PaginationNext class="cursor-pointer" @click="changePage(page + 1)" />
                            </PaginationContent>
                        </Pagination>
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

    function changePage(page: number) {
        router.get(
            `/processed-messages?page=${page}`,
            {},
            { preserveScroll: true }
        )
    }
</script>
