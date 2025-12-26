<script setup lang="ts">
  import AppLayout from '@/layouts/AppLayout.vue'
  import { Head, useForm } from '@inertiajs/vue3'
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
  import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
  } from "@/components/ui/dialog"
  import Button from '@/components/ui/button/Button.vue'
  import Input from '@/components/ui/input/Input.vue'
  import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
  } from '@/components/ui/select'
  import { Textarea } from '@/components/ui/textarea'


  defineProps<{
    messages: {
      id: number
      sender: string
      message: string
      received_at: string
    }[],
    provinces: {
      'province': string,
      'region': string,
    }[],
    regions: {
      'region': string,
    }[],
    barangays: {
      'barangay': string,
      'id': number,
      'city_municipality': string,
    }[],
    cities: {
      'city_municipality': string,
      'province': string,
    }[],
    classifications: {
      id: number,
      name: string,
      other: number,
    }[],
  }>() 

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
  })

  const addToSMS = (item: { message: string, received_at: string, id: number}) => {
    form.dialogueOpen = true
    form.smsinformation = item.message
    form.receivedAt = item.received_at
    form.smsId = item.id
  }

  const submit = () => {
    form.errors = {}
    if (!form.classificationId) {
      form.errors.classificationId = 'Classification is required.'
      return
    }

    if (!form.selectedBarangay) {
      form.errors.selectedBarangay = 'Barangay is required.'
      return
    }

    form.post('/sms/fetch-message', {
      onSuccess: () => {
        form.reset()
      },
    })
  }

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
              <TableCell colspan="3" class="text-center py-6 text-muted-foreground">
                No messages found
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <Dialog
        v-model:open="form.dialogueOpen"
      >
          <DialogContent 
              class="w-3/4 
              max-h-[90vh]
              overflow-scroll"
          >
            <DialogHeader>
              <DialogTitle>Information Report</DialogTitle>
            </DialogHeader>
            
            <div
              class="grid gap-2 grid-cols-3"
            >
              <div class="col-span-1 md:col-span-1">
                <p>SMS Information</p>
                <p>Received At : {{ form.receivedAt }}</p>
                <p>{{ form.smsinformation }}</p>
              </div>
              <div class="col-span-2 md:col-span-2">
                <Input 
                  v-model="form.file_number"
                  label="File Number"
                  class="mb-4"
                  :required="true"
                />
                <Input 
                  v-model="form.reference"
                  label="Reference"
                  class="mb-4"
                  :required="true"
                />
                <Input 
                  v-model="form.subject"
                  label="Subject"
                  class="mb-4"
                  :required="true"
                />
                <Input 
                  v-model="form.dateOfReport"
                  label="Date of Report"
                  :required="true"
                  class="mb-4"
                  type="date"
                />
                <Input 
                  v-model="form.reporter"
                  label="Reporter"
                  class="mb-4"
                />
                <Input 
                  v-model="form.designation"
                  label="Designation"
                  class="mb-4"
                />
                <Input 
                  v-model="form.evaluation"
                  label="Evaluation"
                  class="mb-4"
                />
                <Input 
                  v-model="form.source"
                  label="Source"
                  class="mb-4"
                />
                <Input 
                  v-model="form.dateAcquired"
                  label="Date Acquired"
                  class="mb-4"
                  type="date"
                />
                <Input 
                  v-model="form.mannerAcquired"
                  label="Manner Acquired"
                  class="mb-4"
                />
                <Textarea 
                  v-model="form.informationProper"
                  label="Information Proper"
                  class="mb-4"
                  :required="true"
                />
                <Input 
                  v-model="form.analysis"
                  label="Analysis"
                  class="mb-4"
                />
                <Input 
                  v-model="form.actions"
                  label="Actions"
                  class="mb-4"
                />
                
                <Select
                  v-model="form.classificationId"
                  :error="form.errors.classificationId"
                >
                  <label 
                    class="mb-2 block text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  >
                    Classification
                  </label>
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select a classification" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectLabel>Classification</SelectLabel>
                      <template 
                        v-for="classification in classifications"
                        :key="classification.id"
                      >
                        <SelectItem :value="classification.id">
                          {{ classification.name }}
                        </SelectItem>
                      </template>
                    </SelectGroup>
                  </SelectContent>
                </Select>

                <Select
                  v-model="form.selectedRegion"
                >
                  <label 
                    class="mb-2 mt-4 block text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  >
                    Region
                  </label>
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select a region" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectLabel>Region</SelectLabel>
                      <template 
                        v-for="region in regions"
                        :key="region.region"
                      >
                        <SelectItem :value="region.region">
                          {{ region.region }}
                        </SelectItem>
                      </template>
                    </SelectGroup>
                  </SelectContent>
                </Select>
                
                <Select
                  v-model="form.selectedProvince"
                >
                  <label 
                    class="mb-2 mt-4 block text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  >
                    Province
                  </label>
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select a province" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectLabel>Province</SelectLabel>
                      <template 
                        v-for="province in provinces.filter(prov => prov.region === form.selectedRegion)"
                        :key="province.province"
                      >
                        <SelectItem :value="province.province">
                          {{ province.province }}
                        </SelectItem>
                      </template>
                    </SelectGroup>
                  </SelectContent>
                </Select>
                
                <Select
                  v-model="form.selectedCity"
                >
                  <label 
                    class="mb-2 mt-4 block text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  >
                    City/Municipality
                  </label>
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select a city/municipality" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectLabel>City/Municipality</SelectLabel>
                      <template 
                        v-for="city in cities.filter(city => city.province === form.selectedProvince)"
                        :key="city.city_municipality"
                      >
                        <SelectItem :value="city.city_municipality">
                          {{ city.city_municipality }}
                        </SelectItem>
                      </template>
                    </SelectGroup>
                  </SelectContent>
                </Select>

                <Select
                  v-model="form.selectedBarangay"
                  :error="form.errors.selectedBarangay"
                >
                  <label 
                    class="mb-2 mt-4 block text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  >
                    Barangay
                  </label>
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Select a barangay" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectLabel>Barangay</SelectLabel>
                      <template 
                        v-for="barangay in barangays.filter(bar => bar.city_municipality === form.selectedCity)"
                        :key="barangay.barangay"
                      >
                        <SelectItem :value="barangay.id">
                          {{ barangay.barangay }}
                        </SelectItem>
                      </template>
                    </SelectGroup>
                  </SelectContent>
                </Select>
              </div>
            </div>

            <DialogFooter>
              <DialogClose asChild>
                <Button variant="outline">Cancel</Button>
              </DialogClose>
              <Button
                @click="submit()"
                type="submit"
                class="cursor-pointer"
                :class="form.processing ? 'disabled cursor-not-allowed' : ''"
              >
                Save
              </Button>
            </DialogFooter>
          </DialogContent>
      </Dialog>
    </div>
  </AppLayout>
</template>
