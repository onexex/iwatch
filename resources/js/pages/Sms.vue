<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { PlusIcon, ChevronLeftIcon, ChevronRightIcon, InboxIcon } from 'lucide-vue-next'

// shadcn/ui table components
import Button from '@/components/ui/button/Button.vue';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import Input from '@/components/ui/input/Input.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';

defineProps<{
    messages: {
        id: number;
        sender: string;
        message: string;
        received_at: string;
    }[];
    provinces: {
        province: string;
        region: string;
    }[];
    regions: {
        region: string;
    }[];
    barangays: {
        barangay: string;
        id: number;
        city_municipality: string;
    }[];
    cities: {
        city_municipality: string;
        province: string;
    }[];
    classifications: {
        id: number;
        name: string;
        other: number;
    }[];
}>();

const form = useForm({
    dialogueOpen: false,
    smsId: 0,
    smsinformation: '',
    receivedAt: '',
    file_number: '',
    reference: '',
    subject: '',
    dateOfReport: '',
    reporter: '',
    designation: '',
    evaluation: '',
    source: '',
    dateAcquired: '',
    mannerAcquired: '',
    informationProper: '',
    analysis: '',
    actions: '',
    attachment: [],
    classificationId: null as number | null,
    selectedRegion: '',
    selectedProvince: '',
    selectedCity: '',
    selectedBarangay: '',
});

const addToSMS = (item: {
    message: string;
    received_at: string;
    id: number;
}) => {
    form.dialogueOpen = true;
    form.smsinformation = item.message;
    form.receivedAt = item.received_at;
    form.smsId = item.id;
};

const submit = () => {
    form.errors = {};
    if (!form.classificationId) {
        form.errors.classificationId = 'Classification is required.';
        return;
    }

    if (!form.selectedBarangay) {
        form.errors.selectedBarangay = 'Barangay is required.';
        return;
    }

    form.post('/sms/fetch-message', {
        onSuccess: () => {
            form.reset();
        },
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Messages',
        href: '/sms',
    },
];
</script>

<template>
    <Head title="SMS Inbox" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="mb-4 text-2xl font-bold">Messages</h1>

         <div class="flex flex-col gap-4">
  <div class="rounded-xl border bg-card shadow-sm overflow-hidden">
    <div class="relative h-[750px] overflow-auto">
      <Table>
        <TableHeader class="sticky top-0 z-10 bg-muted/90 backdrop-blur-md shadow-sm">
          <TableRow>
            <TableHead class="w-[220px] py-4 px-6 font-semibold">Sender</TableHead>
            <TableHead class="font-semibold">Message</TableHead>
            <TableHead class="w-[180px] font-semibold">Received At</TableHead>
            <TableHead class="w-[180px] font-semibold">Status</TableHead>
            <TableHead class="w-[100px] text-right px-6 font-semibold">Action</TableHead>
          </TableRow>
        </TableHeader>
        
        <TableBody>
          <TableRow 
            v-for="sms in messages" 
            :key="sms.id"
            class="group transition-colors hover:bg-muted/30 border-b last:border-0"
          >
            <TableCell class="py-4 px-6">
              <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary ring-1 ring-primary/20">
                  {{ sms.sender.substring(0, 2).toUpperCase() }}
                </div>
                <span class="font-semibold text-foreground tracking-tight truncate">
                  {{ sms.sender }}
                </span>
              </div>
            </TableCell>

            <TableCell class="max-w-md lg:max-w-xl">
              <p class="text-sm text-muted-foreground group-hover:text-foreground transition-colors line-clamp-2">
                {{ sms.message }}
              </p>
            </TableCell>

            <TableCell class="text-sm font-medium whitespace-nowrap">
              {{ sms.received_at }}
            </TableCell>

             <TableCell class="text-sm font-medium whitespace-nowrap">
              
            </TableCell>

            <TableCell class="text-right px-6">
              <Button
                variant="secondary"
                size="sm"
                class="h-8 gap-2 px-3 transition-all hover:bg-primary hover:text-primary-foreground"
                @click="addToSMS(sms)"
              >
                <PlusIcon class="h-3.5 w-3.5" />
                <span>Evaluate</span>
              </Button>
            </TableCell>
          </TableRow>

          <TableRow v-if="messages.length === 0">
            <TableCell colspan="4" class="h-[400px] text-center">
              <div class="flex flex-col items-center justify-center gap-2 text-muted-foreground">
                <InboxIcon class="h-10 w-10 opacity-20" />
                <p class="text-sm font-medium">No messages found</p>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </div>

   
</div>
            <Dialog v-model:open="form.dialogueOpen">
                <DialogContent
                    class="flex max-h-[95vh] max-w-5xl flex-col overflow-hidden p-0"
                >
                    <DialogHeader class="border-b bg-muted/20 px-6 py-4">
                        <DialogTitle class="text-xl font-bold tracking-tight"
                            >Information Report</DialogTitle
                        >
                        <DialogDescription
                            >Review source data and categorize the intelligence
                            report.</DialogDescription
                        >
                    </DialogHeader>

                    <div
                        class="flex flex-1 flex-col overflow-hidden md:flex-row"
                    >
                        <aside
                            class="w-full space-y-6 overflow-y-auto border-r bg-muted/30 p-6 md:w-80"
                        >
                            <div>
                                <h3
                                    class="mb-3 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    SMS Metadata
                                </h3>
                                <div class="space-y-1">
                                    <p class="text-xs text-muted-foreground">
                                        Received At
                                    </p>
                                    <p class="text-sm font-medium">
                                        {{ form.receivedAt }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="rounded-xl border bg-background p-4 shadow-sm"
                            >
                                <h3
                                    class="mb-2 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Original Content
                                </h3>
                                <p
                                    class="text-sm leading-relaxed text-foreground/90 italic"
                                >
                                    "{{ form.smsinformation }}"
                                </p>
                            </div>
                        </aside>

                        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div
                                    class="flex items-center gap-2 md:col-span-2"
                                >
                                    <div class="h-px flex-1 bg-border"></div>
                                    <span
                                        class="text-[10px] font-bold tracking-widest text-muted-foreground uppercase"
                                        >General Details</span
                                    >
                                    <div class="h-px flex-1 bg-border"></div>
                                </div>

                                <Input
                                    v-model="form.file_number"
                                    label="File Number"
                                    placeholder="REQ-2024-001"
                                    :required="true"
                                />
                                <Input
                                    v-model="form.reference"
                                    label="Reference"
                                    placeholder="Ref code..."
                                    :required="true"
                                />
                                <Input
                                    v-model="form.subject"
                                    label="Subject"
                                    class="md:col-span-2"
                                    :required="true"
                                />

                                <Input
                                    v-model="form.dateOfReport"
                                    label="Date of Report"
                                    type="date"
                                    :required="true"
                                />
                                <Input
                                    v-model="form.reporter"
                                    label="Reporter"
                                    placeholder="Name of officer"
                                />
                                <Input
                                    v-model="form.designation"
                                    label="Designation"
                                    placeholder="Rank/Position"
                                />
                                <Input
                                    v-model="form.source"
                                    label="Source"
                                    placeholder="Intelligence Source"
                                />

                                <Input
                                    v-model="form.dateAcquired"
                                    label="Date Acquired"
                                    type="date"
                                />
                                <Input
                                    v-model="form.mannerAcquired"
                                    label="Manner Acquired"
                                    placeholder="Method of collection"
                                />

                                <div
                                    class="mt-4 flex items-center gap-2 md:col-span-2"
                                >
                                    <div class="h-px flex-1 bg-border"></div>
                                    <span
                                        class="text-[10px] font-bold tracking-widest text-muted-foreground uppercase"
                                        >Classification & Status</span
                                    >
                                    <div class="h-px flex-1 bg-border"></div>
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="text-sm leading-none font-medium"
                                        >Classification</label
                                    >
                                    <Select
                                        v-model="form.classificationId"
                                        :error="form.errors.classificationId"
                                    >
                                        <SelectTrigger>
                                            <SelectValue
                                                placeholder="Select classification"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="item in classifications"
                                                :key="item.id"
                                                :value="item.id"
                                            >
                                                {{ item.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <Input
                                    v-model="form.evaluation"
                                    label="Evaluation"
                                    placeholder="Initial assessment score"
                                />

                                <div
                                    class="mt-2 grid grid-cols-2 gap-4 rounded-lg border border-dashed bg-muted/20 p-4 md:col-span-2 lg:grid-cols-4"
                                >
                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-bold text-muted-foreground uppercase"
                                            >Region</label
                                        >
                                        <Select v-model="form.selectedRegion">
                                            <SelectTrigger class="bg-background"
                                                ><SelectValue
                                                    placeholder="Region"
                                            /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="r in regions"
                                                    :key="r.region"
                                                    :value="r.region"
                                                    >{{ r.region }}</SelectItem
                                                >
                                            </SelectContent>
                                        </Select>
                                    </div>

                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-bold text-muted-foreground uppercase"
                                            >Province</label
                                        >
                                        <Select v-model="form.selectedProvince">
                                            <SelectTrigger class="bg-background"
                                                ><SelectValue
                                                    placeholder="Province"
                                            /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="p in provinces.filter(
                                                        (prov) =>
                                                            prov.region ===
                                                            form.selectedRegion,
                                                    )"
                                                    :key="p.province"
                                                    :value="p.province"
                                                >
                                                    {{ p.province }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>

                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-bold text-muted-foreground uppercase"
                                            >City</label
                                        >
                                        <Select v-model="form.selectedCity">
                                            <SelectTrigger class="bg-background"
                                                ><SelectValue
                                                    placeholder="City"
                                            /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="c in cities.filter(
                                                        (city) =>
                                                            city.province ===
                                                            form.selectedProvince,
                                                    )"
                                                    :key="c.city_municipality"
                                                    :value="c.city_municipality"
                                                >
                                                    {{ c.city_municipality }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>

                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-bold text-muted-foreground uppercase"
                                            >Barangay</label
                                        >
                                        <Select
                                            v-model="form.selectedBarangay"
                                            :error="
                                                form.errors.selectedBarangay
                                            "
                                        >
                                            <SelectTrigger class="bg-background"
                                                ><SelectValue
                                                    placeholder="Barangay"
                                            /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="b in barangays.filter(
                                                        (bar) =>
                                                            bar.city_municipality ===
                                                            form.selectedCity,
                                                    )"
                                                    :key="b.id"
                                                    :value="b.id"
                                                >
                                                    {{ b.barangay }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>

                                <div class="mt-4 space-y-4 md:col-span-2">
                                    <Textarea
                                        v-model="form.informationProper"
                                        label="Information Proper"
                                        placeholder="Enter detailed narrative..."
                                        :required="true"
                                        class="min-h-[120px]"
                                    />
                                    <Input
                                        v-model="form.analysis"
                                        label="Analysis"
                                        placeholder="Analyst comments..."
                                    />
                                    <Input
                                        v-model="form.actions"
                                        label="Actions"
                                        placeholder="Recommended next steps..."
                                    />
                                </div>
                            </div>
                        </main>
                    </div>

                    <DialogFooter class="border-t bg-muted/20 px-6 py-4">
                        <DialogClose asChild>
                            <Button variant="ghost">Cancel</Button>
                        </DialogClose>
                        <Button
                            @click="submit()"
                            :disabled="form.processing"
                            class="px-8 shadow-lg shadow-primary/20 transition-all hover:scale-[1.02]"
                        >
                            <span v-if="!form.processing">Save Report</span>
                            <span v-else class="flex items-center gap-2">
                                <Loader2 class="h-4 w-4 animate-spin" />
                                Saving...
                            </span>
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
