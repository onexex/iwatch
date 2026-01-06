<script setup lang="ts">
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Bar, Doughnut } from 'vue-chartjs';
import { 
    MessageSquare, ShieldAlert, FileSearch, 
    MapPin, ChevronRight, Activity 
} from 'lucide-vue-next';
import {
  Chart as ChartJS, Title, Tooltip, Legend, BarElement, 
  CategoryScale, LinearScale, ArcElement 
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const props = defineProps<{
    stats: any;
    chartData: any;
    recent: any[];
}>();

// Chart Setup
// Chart Setup
const barangayChart = computed(() => ({
    labels: Object.keys(props.chartData.barangay || {}),
    datasets: [{
        label: 'Reports',
        backgroundColor: '#6366f1',
        borderRadius: 8,
        // Cast the values to number[] to satisfy Chart.js types
        data: Object.values(props.chartData.barangay || {}) as number[]
    }]
}));

const typeChart = computed(() => ({
    labels: Object.keys(props.chartData.types || {}),
    datasets: [{
        // Cast the values to number[] here as well
        data: Object.values(props.chartData.types || {}) as number[],
        backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6'],
        borderWidth: 0
    }]
}));

const options = { 
    responsive: true, 
    maintainAspectRatio: false, 
    plugins: { legend: { display: false } } 
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div v-for="(val, label) in { 
                    'Inbox (SMS)': [stats.new_sms, 'text-amber-500', MessageSquare],
                    'Total Logs': [stats.total_incidents, 'text-indigo-500', ShieldAlert],
                    'Pending Analysis': [stats.pending_analysis, 'text-rose-500', FileSearch],
                    'Active Feed': [recent.length, 'text-emerald-500', Activity]
                }" :key="label" 
                class="rounded-xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border shadow-sm">
                    <div class="flex justify-between text-muted-foreground mb-4">
                        <component :is="val[2]" class="w-5 h-5" />
                        <span class="text-[10px] font-black uppercase tracking-widest">{{ label }}</span>
                    </div>
                    <div class="text-3xl font-black" :class="val[1]">{{ val[0] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <div class="flex items-center gap-2 mb-6">
                        <MapPin class="w-4 h-4 text-indigo-500" />
                        <h3 class="font-bold text-sm uppercase tracking-tight">Geographic Distribution</h3>
                    </div>
                    <div class="h-[300px]">
                        <Bar :data="barangayChart" :options="options" />
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
                    <h3 class="font-bold text-sm uppercase mb-6 text-center">Classification</h3>
                    <div class="h-[200px]">
                        <Doughnut :data="typeChart" :options="options" />
                    </div>
                    <div class="mt-6 space-y-2">
                        <div v-for="(val, key, index) in chartData.types" :key="key" class="flex justify-between text-xs">
                            <span class="text-muted-foreground flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6'][index] }"></span>
                                {{ key }}
                            </span>
                            <span class="font-bold">{{ val }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border overflow-hidden">
                <div class="p-5 border-b border-sidebar-border/70 flex justify-between items-center">
                    <h3 class="font-bold text-sm uppercase">Recent Incident Assessments</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-muted/50 text-[10px] uppercase font-black text-muted-foreground">
                            <tr>
                                <th class="px-6 py-3">Reference</th>
                                <th class="px-6 py-3">Barangay</th>
                                <th class="px-6 py-3">Subject</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sidebar-border/70">
                            <tr v-for="item in recent" :key="item.id" class="group hover:bg-muted/50 transition-colors">
                                <td class="px-6 py-4 font-mono text-indigo-500 font-bold">#{{ item.reference || 'N/A' }}</td>
                                <td class="px-6 py-4 font-medium">{{ item.barangay?.barangay }}</td>
                                <td class="px-6 py-4 text-muted-foreground truncate max-w-[200px]">{{ item.subject }}</td>
                                <td class="px-6 py-4">
                                    <span :class="item.analysis ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'" class="px-2 py-0.5 rounded text-[10px] font-black uppercase">
                                        {{ item.analysis ? 'Analyzed' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="inline-flex p-1 rounded bg-muted group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                        <ChevronRight class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>