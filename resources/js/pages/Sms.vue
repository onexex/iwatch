<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

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
    SelectGroup,
    SelectItem,
    SelectLabel,
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

            <div class="overflow-hidden rounded-lg border">
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
                        <TableRow v-for="sms in messages" :key="sms.id">
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
                                <Button
                                    class="cursor-pointer"
                                    size="sm"
                                    @click="addToSMS(sms)"
                                >
                                    Add
                                </Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="messages.length === 0">
                            <TableCell
                                colspan="3"
                                class="py-6 text-center text-muted-foreground"
                            >
                                No messages found
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
             <Dialog v-model:open="form.dialogueOpen">
  <DialogContent class="max-w-5xl p-0 overflow-hidden max-h-[95vh] flex flex-col">
    <DialogHeader class="px-6 py-4 border-b bg-muted/20">
      <DialogTitle class="text-xl font-bold tracking-tight">Information Report</DialogTitle>
      <DialogDescription>Review source data and categorize the intelligence report.</DialogDescription>
    </DialogHeader>

    <div class="flex-1 overflow-hidden flex flex-col md:flex-row">
      <aside class="w-full md:w-80 bg-muted/30 p-6 border-r overflow-y-auto space-y-6">
        <div>
          <h3 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-3">SMS Metadata</h3>
          <div class="space-y-1">
            <p class="text-xs text-muted-foreground">Received At</p>
            <p class="text-sm font-medium">{{ form.receivedAt }}</p>
          </div>
        </div>

        <div class="p-4 rounded-xl bg-background border shadow-sm">
          <h3 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-2">Original Content</h3>
          <p class="text-sm leading-relaxed text-foreground/90 italic">
            "{{ form.smsinformation }}"
          </p>
        </div>
      </aside>

      <main class="flex-1 overflow-y-auto p-6 lg:p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          
          <div class="md:col-span-2 flex items-center gap-2">
            <div class="h-px flex-1 bg-border"></div>
            <span class="text-[10px] font-bold uppercase text-muted-foreground tracking-widest">General Details</span>
            <div class="h-px flex-1 bg-border"></div>
          </div>

          <Input v-model="form.file_number" label="File Number" placeholder="REQ-2024-001" :required="true" />
          <Input v-model="form.reference" label="Reference" placeholder="Ref code..." :required="true" />
          <Input v-model="form.subject" label="Subject" class="md:col-span-2" :required="true" />

          <Input v-model="form.dateOfReport" label="Date of Report" type="date" :required="true" />
          <Input v-model="form.reporter" label="Reporter" placeholder="Name of officer" />
          <Input v-model="form.designation" label="Designation" placeholder="Rank/Position" />
          <Input v-model="form.source" label="Source" placeholder="Intelligence Source" />
          
          <Input v-model="form.dateAcquired" label="Date Acquired" type="date" />
          <Input v-model="form.mannerAcquired" label="Manner Acquired" placeholder="Method of collection" />

          <div class="md:col-span-2 mt-4 flex items-center gap-2">
            <div class="h-px flex-1 bg-border"></div>
            <span class="text-[10px] font-bold uppercase text-muted-foreground tracking-widest">Classification & Status</span>
            <div class="h-px flex-1 bg-border"></div>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium leading-none">Classification</label>
            <Select v-model="form.classificationId" :error="form.errors.classificationId">
              <SelectTrigger>
                <SelectValue placeholder="Select classification" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="item in classifications" :key="item.id" :value="item.id">
                  {{ item.name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <Input v-model="form.evaluation" label="Evaluation" placeholder="Initial assessment score" />

          <div class="md:col-span-2 grid grid-cols-2 lg:grid-cols-4 gap-4 mt-2 p-4 bg-muted/20 rounded-lg border border-dashed">
            <div class="space-y-2">
              <label class="text-[10px] font-bold uppercase text-muted-foreground">Region</label>
              <Select v-model="form.selectedRegion">
                <SelectTrigger class="bg-background"><SelectValue placeholder="Region" /></SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="r in regions" :key="r.region" :value="r.region">{{ r.region }}</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div class="space-y-2">
              <label class="text-[10px] font-bold uppercase text-muted-foreground">Province</label>
              <Select v-model="form.selectedProvince">
                <SelectTrigger class="bg-background"><SelectValue placeholder="Province" /></SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="p in provinces.filter(prov => prov.region === form.selectedRegion)" :key="p.province" :value="p.province">
                    {{ p.province }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div class="space-y-2">
              <label class="text-[10px] font-bold uppercase text-muted-foreground">City</label>
              <Select v-model="form.selectedCity">
                <SelectTrigger class="bg-background"><SelectValue placeholder="City" /></SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="c in cities.filter(city => city.province === form.selectedProvince)" :key="c.city_municipality" :value="c.city_municipality">
                    {{ c.city_municipality }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div class="space-y-2">
              <label class="text-[10px] font-bold uppercase text-muted-foreground">Barangay</label>
              <Select v-model="form.selectedBarangay" :error="form.errors.selectedBarangay">
                <SelectTrigger class="bg-background"><SelectValue placeholder="Barangay" /></SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="b in barangays.filter(bar => bar.city_municipality === form.selectedCity)" :key="b.id" :value="b.id">
                    {{ b.barangay }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>

          <div class="md:col-span-2 space-y-4 mt-4">
            <Textarea v-model="form.informationProper" label="Information Proper" placeholder="Enter detailed narrative..." :required="true" class="min-h-[120px]" />
            <Input v-model="form.analysis" label="Analysis" placeholder="Analyst comments..." />
            <Input v-model="form.actions" label="Actions" placeholder="Recommended next steps..." />
          </div>
        </div>
      </main>
    </div>

    <DialogFooter class="px-6 py-4 border-t bg-muted/20">
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
          <Loader2 class="h-4 w-4 animate-spin" /> Saving...
        </span>
      </Button>
    </DialogFooter>
  </DialogContent>
</Dialog>
        </div>
    </AppLayout>
</template>
