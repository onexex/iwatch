<style>
/* Custom Cluster Look */
.my-custom-cluster {
    background-color: rgba(59, 130, 246, 0.6);
    /* Blue with transparency */
    border-radius: 50%;
}

.my-custom-cluster div {
    width: 30px;
    height: 30px;
    margin-left: 5px;
    margin-top: 5px;
    text-align: center;
    border-radius: 50%;
    background-color: #2563eb;
    /* Darker blue center */
    color: white;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Modern Leaflet Tooltip Styling */
.leaflet-tooltip.modern-tooltip {
    background-color: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    padding: 8px 12px;
}

.leaflet-tooltip-top.modern-tooltip:before {
    border-top-color: white;
}

/* Custom Cluster Look */
.my-custom-cluster {
    background-color: rgba(59, 130, 246, 0.6);
    border-radius: 50%;
}
</style>
<template>

    <Head title="Verified Incident Tracker" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:flex-row">
            <div class="relative min-h-[500px] flex-1 overflow-hidden rounded-xl border bg-muted/50">
                <div id="map" class="h-full w-full"></div>

                <div v-if="!isHeatmapEnabled && categoryStats.length > 0"
                    class="pointer-events-auto absolute bottom-6 left-3 z-1000 min-w-[170px] rounded-xl border bg-background/90 p-4 shadow-xl backdrop-blur-md">
                    <h4 class="mb-3 text-[10px] font-bold tracking-wider text-muted-foreground uppercase">
                        Classification Filter
                    </h4>

                    <div class="space-y-2.5">
                        <label v-for="cat in categoryStats" :key="cat.name"
                            class="group flex cursor-pointer items-center justify-between">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" :value="cat.name" v-model="activeCategories"
                                    @change="updateMapVisualization" class="peer hidden" />

                                <div class="flex h-4 w-4 items-center justify-center rounded border border-muted-foreground/30 shadow-sm transition-all"
                                    :class="activeCategories.includes(cat.name)
                                            ? cat.bgClass
                                            : 'bg-transparent'
                                        ">
                                    <svg v-if="
                                        activeCategories.includes(cat.name)
                                    " class="h-2.5 w-2.5 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>

                                <span
                                    class="text-[11px] font-bold text-foreground/80 transition-colors group-hover:text-foreground">
                                    {{ cat.name }}
                                </span>
                            </div>

                            <span
                                class="rounded bg-muted px-1.5 py-0.5 font-mono text-[10px] font-bold text-muted-foreground">
                                {{ cat.count }}
                            </span>
                        </label>
                    </div>

                    <div class="mt-4 flex justify-between border-t border-border/50 pt-2">
                        <button @click="selectAllCategories"
                            class="text-[9px] font-black text-red-600 uppercase hover:underline">
                            All
                        </button>
                        <button @click="clearCategories"
                            class="text-[9px] font-black text-muted-foreground uppercase hover:underline">
                            None
                        </button>
                    </div>
                </div>

                <div v-if="showFullList"
                    class="absolute inset-y-0 right-0 z-[1001] w-80 animate-in border-l bg-background/95 p-4 shadow-xl backdrop-blur-md slide-in-from-right">
                    <div class="mb-2 flex items-center justify-between">
                        <h3 class="text-sm font-bold">Search Incidents</h3>
                        <button @click="showFullList = false"
                            class="text-xs text-muted-foreground hover:text-foreground">
                            ✕
                        </button>
                    </div>
                    <input v-model="searchQuery" type="text" placeholder="Search barangay, ID, keyword..."
                        class="mb-4 w-full rounded-md border border-input bg-background px-3 py-2 text-xs focus:ring-2 focus:ring-primary" />
                    <div class="custom-scrollbar h-[calc(100%-120px)] space-y-2 overflow-y-auto pr-2">
                        <div v-for="incident in searchedIncidents" :key="incident.id" @click="focusIncident(incident)"
                            class="group cursor-pointer rounded-lg border p-2 transition-all hover:bg-accent">
                            <div class="mb-1 flex items-center justify-between">
                                <span class="font-mono text-[10px] font-bold">#{{ incident.id }}</span>
                                <span :class="[
                                    'h-2 w-2 rounded-full',
                                    getCategoryBg(incident.type),
                                ]"></span>
                            </div>
                            <p class="truncate text-[11px] font-semibold text-primary">
                                {{ incident.description }}

                            </p>
                            <p class="line-clamp-2 text-[10px] leading-tight text-muted-foreground">


                                {{ incident.barangay?.province }}, {{ incident.barangay?.city_municipality }}, {{
                                incident.barangay?.barangay }}
                            </p>
                        </div>
                    </div>
                </div>

                <button @click="showFullList = !showFullList"
                    class="absolute top-4 right-4 z-1000 flex items-center gap-2 rounded-md bg-white px-4 py-2 text-xs font-bold shadow-md hover:bg-gray-50 dark:bg-slate-900">
                    <span>📋 View List ({{ searchedIncidents.length }})</span>
                </button>

                <div class="pointer-events-auto absolute top-6 left-3 z-1000 flex w-72 flex-col gap-4 md:w-80">
                    <div
                        class="overflow-hidden rounded-xl border bg-card/90 shadow-xl ring-1 ring-black/5 backdrop-blur-md">
                        <div @click="isSidebarCollapsed = !isSidebarCollapsed"
                            class="flex cursor-pointer items-center justify-between p-4 transition-colors hover:bg-muted/30">
                            <label
                                class="cursor-pointer text-[10px] font-bold tracking-wider text-muted-foreground uppercase">
                                Map Controls
                            </label>
                            <button class="text-muted-foreground transition-transform duration-200"
                                :class="{ 'rotate-180': isSidebarCollapsed }">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="m18 15-6-6-6 6" />
                                </svg>
                            </button>
                        </div>

                        <div v-show="!isSidebarCollapsed"
                            class="animate-in space-y-4 p-4 pt-0 duration-200 fade-in slide-in-from-top-2">
                            <hr class="mb-4 border-border/50" />

                            <label
                                class="text-[10px] font-bold tracking-wider text-muted-foreground uppercase">Visualization
                                Mode</label>
                            <div class="mt-2 mb-4 flex items-center justify-between rounded-lg border bg-muted/50 p-2">
                                <span class="text-xs font-medium">Heatmap View</span>
                                <button @click.stop="toggleHeatmap" :class="[
                                    isHeatmapEnabled
                                        ? 'bg-red-600'
                                        : 'bg-slate-300',
                                ]"
                                    class="relative inline-flex h-5 w-10 items-center rounded-full transition-colors outline-none">
                                    <span :class="[
                                        isHeatmapEnabled
                                            ? 'translate-x-5'
                                            : 'translate-x-1',
                                    ]"
                                        class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform" />
                                </button>
                            </div>

                            <label
                                class="text-[10px] font-bold tracking-wider text-muted-foreground uppercase">Filters</label>
                            <div class="mt-2 space-y-2">
                                <input type="date" v-model="filters.startDate"
                                    class="w-full rounded border bg-background px-2 py-1.5 text-xs focus:ring-1 focus:ring-primary" />
                                <input type="date" v-model="filters.endDate"
                                    class="w-full rounded border bg-background px-2 py-1.5 text-xs focus:ring-1 focus:ring-primary" />

                                <div class="flex gap-2 pt-1">
                                    <button @click="applyFilters"
                                        class="flex-1 rounded bg-primary py-1.5 text-[11px] font-bold text-primary-foreground hover:opacity-90">
                                        Apply
                                    </button>
                                    <button @click="resetFilters"
                                        class="rounded border bg-background px-3 py-1.5 text-[11px] hover:bg-muted">
                                        Reset
                                    </button>
                                </div>

                                <button @click="exportToCSV" :disabled="searchedIncidents.length === 0"
                                    class="mt-2 flex w-full items-center justify-center gap-2 rounded border border-green-600/30 bg-green-50/80 px-2 py-2 text-[11px] font-bold text-green-700 hover:bg-green-100 disabled:opacity-50">
                                    📥 Export ({{ searchedIncidents.length }})
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="selectedIncident"
                        class="pointer-events-auto max-h-[400px] animate-in overflow-y-auto rounded-xl border bg-card/95 p-4 shadow-xl ring-2 ring-primary/10 backdrop-blur-md duration-300 slide-in-from-left">
                        <div
                            class="sticky top-0 mb-2 flex items-start justify-between border-b border-border/50 bg-card/0 pb-2">
                            <h3 class="text-sm">Incident Details</h3>
                            <button @click="selectedIncident = null"
                                class="p-1 text-lg text-muted-foreground hover:text-foreground">
                                ✕
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div class="rounded-lg border border-red-500/20 bg-red-500/10 p-2">
                                <label class="text-[10px] font-bold text-muted-foreground uppercase">Location</label>
                                <p class="text-sm   text-red-600">
                                    {{ selectedIncident.barangay?.province }}, {{
                                    selectedIncident.barangay?.city_municipality }}, {{
                                    selectedIncident.barangay?.barangay }}
                                </p>
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-muted-foreground uppercase">Narrative</label>
                                <p class="mt-1 text-xs leading-relaxed whitespace-pre-wrap text-foreground/80">
                                    {{ selectedIncident.description }}
                                </p>
                            </div>

                            <div v-if="selectedIncident.attachments && selectedIncident.attachments.length > 0">
                                <label class="text-[10px] font-bold text-muted-foreground uppercase">Attachments</label>
                                <div class="mt-2 grid grid-cols-2 gap-2">
                                    <div 
                                        v-for="file in selectedIncident.attachments" 
                                        :key="file.id"
                                        class="group relative overflow-hidden rounded-md border border-border bg-muted"
                                    >
                                        <a :href="`/storage/${file.url}`" target="_blank">
                                            <img 
                                                :src="`/storage/${file.url}`" 
                                                class="h-24 w-full object-cover transition-transform duration-300 group-hover:scale-110" 
                                                alt="Incident Attachment"
                                            />
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-border/50 pt-2">
                                <p class="text-[10px] text-muted-foreground italic">
                                    📅 Recorded:
                                    {{
                                        new Date(
                                            selectedIncident.created_at,
                                        ).toLocaleString()
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, defineComponent, onMounted, ref, watch } from 'vue';


import * as L from 'leaflet';
import 'leaflet.heat';

import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet/dist/leaflet.css';

interface BarangayProperties {
    ADM4_EN: string;
    latitude: number;
    longitude: number;
    province: string;
    city_municipality: string;
    barangay: string;
}

interface Incident {
    id: number;
    sms_id: number;
    description: string;
    type: string;
    created_at: string;
    url: string;
    barangay: BarangayProperties;
    attachments: { id: number; url: string }[];
}

export default defineComponent({
    name: 'MapView',
    components: { AppLayout, Head },
    data() {
        return {
            isSidebarCollapsed: false,
        };
    },
    setup() {
        const breadcrumbs: BreadcrumbItem[] = [
            { title: 'Messages', href: '/sms' },
            { title: 'Incident Map', href: '/mapping' },
        ];

        const selectedIncident = ref<Incident | null>(null);
        const allIncidents = ref<Incident[]>([]);
        const showFullList = ref(false);
        const searchQuery = ref('');
        const isHeatmapEnabled = ref(false);
        const filters = ref({ startDate: '', endDate: '' });

        // NEW: Checkbox state for classifications
        const activeCategories = ref<string[]>([]);

        let map: L.Map;
        let markers: L.MarkerClusterGroup;
        let heatLayer: any = null;

        // Reactive Filtering Logic (Dates)
        const filteredIncidents = computed(() => {
            if (!filters.value.startDate || !filters.value.endDate)
                return allIncidents.value;
            const start = new Date(filters.value.startDate).setHours(
                0,
                0,
                0,
                0,
            );
            const end = new Date(filters.value.endDate).setHours(
                23,
                59,
                59,
                999,
            );
            return allIncidents.value.filter((inc) => {
                const incDate = new Date(inc.created_at).getTime();
                return incDate >= start && incDate <= end;
            });
        });

        // Combined Filtering (Dates + Search + Classification Checkboxes)
        const searchedIncidents = computed(() => {
            const query = searchQuery.value.toLowerCase().trim();

            return filteredIncidents.value.filter((inc) => {
                const matchesSearch =
                    !query ||
                    inc.description.toLowerCase().includes(query) ||
                    inc.barangay?.barangay?.toLowerCase().includes(query) ||
                    inc.barangay?.city_municipality?.toLowerCase().includes(query) ||
                    inc.barangay?.province?.toLowerCase().includes(query) ||
                    inc.id.toString().includes(query);

                const matchesCategory =
                    activeCategories.value.length === 0 ||
                    activeCategories.value.includes(inc.type);

                return matchesSearch && matchesCategory;
            });
        });

        // Legend logic with counts
        const categoryStats = computed(() => {
            const types = [
                ...new Set(allIncidents.value.map((i) => i.type)),
            ].filter(Boolean);
            return types
                .map((type) => ({
                    name: type,
                    count: allIncidents.value.filter((i) => i.type === type)
                        .length,
                    bgClass: getCategoryBg(type),
                }))
                .sort((a, b) => b.count - a.count);
        });

        const getCategoryColor = (type: string) => {
            const t = (type || '').toLowerCase().trim();
            if (t === 'TG1(CTG)' || t === 'tg1(ctg)') return '#dc2626';
            if (t === 'TG2(LTG)' || t === 'tg2(ltg)') return '#2563eb';
            if (t === 'TG3(CG)' || t === 'tg3(cg)') return '#000000';
            if (t === 'PAGS/PPAGS' || t === 'pags/ppags') return '#eab308';

            if (t === 'CFO(CPPNPANDF)' || t === 'cfo(cppnpandf)') return '#980404';
            if (t === 'MWP/OWP' || t === 'mwp/owp') return '#FDAAAA';
            if (t === 'CIW' || t === 'ciw') return '#DE802B';
            if (t === 'Others' || t === 'others') return '#F1E6C9';
            if (t === 'MILF/MNLF' || t === 'milt/mnlf') return '#15c799';
           

            return '#16a34a';
        };

        const getCategoryBg = (type: string) => {
            const t = (type || '').toLowerCase().trim();
            if (t === 'TG1(CTG)' || t === 'tg1(ctg)') return 'bg-red-600';
            if (t === 'TG2(LTG)' || t === 'tg2(ltg)') return 'bg-blue-600';
            if (t === 'TG3(CG)' || t === 'tg3(cg)') return 'bg-black';
            if (t === 'PAGS/PPAGS' || t === 'pags/ppags') return 'bg-yellow-500';
            if (t === 'CFO(CPPNPANDF)' || t === 'cfo(cppnpandf)') return 'bg-red-800';
            if (t === 'MWP/OWP' || t === 'mwp/owp') return 'bg-red-200';
            if (t === 'CIW' || t === 'ciw') return 'bg-orange-500';
            if (t === 'Others' || t === 'others') return 'bg-yellow-200';
            if (t === 'MILF/MNLF' || t === 'milt/mnlf') return 'bg-green-600';

            return 'bg-green-600';
        };

        // const renderMarkers = (data: Incident[]) => {
        //     if (!markers) return;
        //     markers.clearLayers();
        //     data.forEach((incident) => {
        //         if (incident.barangay?.latitude) {
        //             const color = getCategoryColor(incident.type);
        //             const customIcon = L.divIcon({
        //                 className: 'custom-pin',
        //                 html: `<div style="position: relative; display: flex; justify-content: center;">
        //                             <div style="background-color: ${color}; width: 22px; height: 22px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.4);"></div>
        //                             <div style="position: absolute; top: 5px; width: 7px; height: 7px; background: white; border-radius: 50%; z-index: 10;"></div>
        //                        </div>`,
        //                 iconSize: [22, 22], iconAnchor: [11, 22]
        //             });
        //             const marker = L.marker([incident.barangay.latitude, incident.barangay.longitude], { icon: customIcon });
        //             marker.on('click', () => {
        //                 selectedIncident.value = incident;
        //                 map.setView([incident.barangay.latitude, incident.barangay.longitude], 16);
        //             });
        //             markers.addLayer(marker);
        //         }
        //     });
        // };
        const renderMarkers = (data: Incident[]) => {
            if (!markers) return;
            markers.clearLayers();
            data.forEach((incident) => {
                if (incident.barangay?.latitude) {
                    const color = getCategoryColor(incident.type);
                    const customIcon = L.divIcon({
                        className: 'custom-pin',
                        html: `<div style="position: relative; display: flex; justify-content: center;">
                            <div style="background-color: ${color}; width: 22px; height: 22px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.4);"></div>
                            <div style="position: absolute; top: 5px; width: 7px; height: 7px; background: white; border-radius: 50%; z-index: 10;"></div>
                       </div>`,
                        iconSize: [22, 22],
                        iconAnchor: [11, 22],
                    });


                    const marker = L.marker(
                        [
                            incident.barangay.latitude,
                            incident.barangay.longitude,
                        ],
                        { icon: customIcon },
                    );

                    // --- ADDED HOVER TOOLTIP ---
                    const hasImage = incident.attachments && incident.attachments.length > 0;
                    const imageUrl = hasImage ? `/storage/${incident.attachments[0].url}` : null;
                    marker.bindTooltip(
                       `
                        <div style="padding: 4px; font-family: sans-serif; width: 200px;">
                            <div style="display: flex; items-center; gap: 6px; margin-bottom: 6px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: ${color}; display: inline-block;"></span>
                                <b style="font-size: 12px; color: #1e293b;">${incident.type.toUpperCase()}</b>
                            </div>

                            ${hasImage ? `
                                <div style="margin-bottom: 6px; border-radius: 4px; overflow: hidden; border: 1px solid #e2e8f0;">
                                    <img src="${imageUrl}" style="width: 100%; height: 80px; object-fit: cover; display: block;" />
                                </div>
                            ` : ''}

                            <div style="font-size: 11px; font-weight: 600; color: #ef4444; margin-bottom: 2px;">
                                ${incident.description.substring(0, 60)}${incident.description.length > 60 ? '...' : ''}
                            </div>
                            
                            <div style="font-size: 10px; color: #64748b; line-height: 1.3;">
                                ${incident.barangay.province}, ${incident.barangay.city_municipality}, ${incident.barangay.barangay}
                            </div>
                        </div>
                        `,
                        {
                            direction: 'top',
                            offset: [0, -20],
                            opacity: 0.98,
                            className: 'modern-tooltip',
                        },
                    );
                    // ---------------------------

                    marker.on('click', () => {
                        selectedIncident.value = incident;
                        map.setView(
                            [
                                incident.barangay.latitude,
                                incident.barangay.longitude,
                            ],
                            13,
                        );
                    });

                    markers.addLayer(marker);
                }
            });
        };
        const updateMapVisualization = () => {
            if (!map) return;
            if (markers) markers.clearLayers();
            if (heatLayer) map.removeLayer(heatLayer);

            if (isHeatmapEnabled.value) {
                const heatPoints = searchedIncidents.value
                    .filter(
                        (inc) =>
                            inc.barangay?.latitude && inc.barangay?.longitude,
                    )
                    .map((inc) => [
                        Number(inc.barangay.latitude),
                        Number(inc.barangay.longitude),
                        0.7,
                    ]);

                if ((L as any).heatLayer) {
                    heatLayer = (L as any)
                        .heatLayer(heatPoints, {
                            radius: 25,
                            blur: 15,
                            maxZoom: 17,
                        })
                        .addTo(map);
                }
            } else {
                renderMarkers(searchedIncidents.value);
            }
        };

        const toggleHeatmap = () => {
            isHeatmapEnabled.value = !isHeatmapEnabled.value;
            updateMapVisualization();
        };

        const applyFilters = () => updateMapVisualization();

        const resetFilters = () => {
            filters.value.startDate = '';
            filters.value.endDate = '';
            searchQuery.value = '';
            isHeatmapEnabled.value = false;
            activeCategories.value = [
                ...new Set(allIncidents.value.map((i) => i.type)),
            ].filter(Boolean);
            updateMapVisualization();
        };

        const selectAllCategories = () => {
            activeCategories.value = categoryStats.value.map((s) => s.name);
            updateMapVisualization();
        };

        const clearCategories = () => {
            activeCategories.value = [];
            updateMapVisualization();
        };

        const exportToCSV = () => {
            const data = searchedIncidents.value;
            const headers = ['ID', 'Date', 'Type', 'Barangay', 'Description'];
            const rows = data.map((inc) => [
                inc.id,
                inc.created_at,
                inc.type,
                inc.barangay?.barangay,
                `"${inc.description}"`,
            ]);
            const csv = [headers, ...rows].map((e) => e.join(',')).join('\n');
            const blob = new Blob([csv], { type: 'text/csv' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `incident_report_${new Date().getTime()}.csv`;
            link.click();
        };

        const focusIncident = (incident: Incident) => {
            selectedIncident.value = incident;
            showFullList.value = false;
            isHeatmapEnabled.value = false;
            updateMapVisualization();
            map.setView(
                [incident.barangay.latitude, incident.barangay.longitude],
                16,
            );
        };

        // Initialize checkboxes when data loads
        watch(
            allIncidents,
            (newVal) => {
                if (newVal.length > 0 && activeCategories.value.length === 0) {
                    activeCategories.value = [
                        ...new Set(newVal.map((i) => i.type)),
                    ].filter(Boolean);
                }
            },
            { immediate: true },
        );

        onMounted(async () => {
            // map = L.map('map', { zoomControl: false }).setView(
            //     [8.1541, 123.2588],
            //     8,
            // );
            map = L.map('map', {
                zoomControl: false,
                preferCanvas: true, // <--- Add this line
                attributionControl: false,
            }).setView([8.1541, 123.2588], 8)

            const googleStyle = L.tileLayer(
                'http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
                {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '© Google',
                },
            );
            const darkStyle = L.tileLayer(
                'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
            );
            const voyagerStyle = L.tileLayer(
                'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
            );

            const blankStyle = L.layerGroup();

            // googleStyle.addTo(map);
            voyagerStyle.addTo(map);

            const baseMaps = {
                Standard: googleStyle,
                'Night Mode': darkStyle,
                Voyager: voyagerStyle,
                'Blank': blankStyle, // No labels, no tiles
            };

            // L.control.layers(baseMaps, {} , { position: 'bottomright' }).addTo(map);
            // markers = (L as any).markerClusterGroup();
            // map.addLayer(markers);

            L.control
                .layers(baseMaps, {}, { position: 'bottomright' })
                .addTo(map);

            // Initialize with a custom icon function
            markers = (L as any).markerClusterGroup({
                iconCreateFunction: function (cluster: any) {
                    const count = cluster.getChildCount();

                    // Return a custom DivIcon
                    return L.divIcon({
                        html: `<div><span>${count}</span></div>`,
                        className: 'my-custom-cluster', // Use our CSS class here
                        iconSize: L.point(40, 40),
                    });
                },
            });

            map.addLayer(markers);

            // markers = L.layerGroup().addTo(map);s

            try {
                const res = await fetch('/mapping_incidents');
                allIncidents.value = await res.json();
                updateMapVisualization();
            } catch (error) {
                console.error('Failed to fetch incidents:', error);
            }

            //enable below to handle the geojson loading (shape file)
            // try {
            //     const res = await fetch('/mapping_incidents');
            //     allIncidents.value = await res.json();
            //     updateMapVisualization();

            //     // 2. NEW: Fetch and Load GeoJSON
            //     const geoRes = await fetch('/geojson/shape.json'); // Replace with your actual filename
            //     const geoData = await geoRes.json();

            //     // L.geoJSON(geoData, {
            //     //     style: {
            //     //         color: '#3388ff', // Border color
            //     //         weight: 0.2,        // Border thickness
            //     //         fillOpacity: 0.1  // Area transparency
            //     //     },
            //     //    onEachFeature: (feature, layer) => {
            //     //         const props = feature.properties;

            //     //         // Check the console to see the real keys
            //     //         console.log("Available properties:", props);

            //     //         // Try all common GIS naming conventions
            //     //         const areaName = props.name || 
            //     //                         props.ADM4_EN ||
            //     //                         "Data not found";

            //     //         layer.bindPopup(`<strong>${areaName}</strong>`);
            //     //     }
            //     // }).addTo(map);

            //     // 1.This code use chunk so it will not crash the browser when rendering many shapes

            //     // const features = geoData.features;
            //     // const chunkSize = 200; // Adjust this: smaller = smoother, larger = faster
            //     // let index = 0;

            //     // // Create a LayerGroup to hold all chunks
            //     // const shapeLayer = L.layerGroup().addTo(map);

            //     // function renderNextChunk() {
            //     //     // Get the next slice of features
            //     //     const chunk = features.slice(index, index + chunkSize);

            //     //     // Create a GeoJSON layer for this specific chunk
            //     //     L.geoJSON(chunk, {
            //     //         style: {
            //     //             color: '#3388ff',
            //     //             weight: 0.2,
            //     //             fillOpacity: 0.1
            //     //         },
            //     //         onEachFeature: (feature, layer) => {
            //     //             const props = feature.properties;
            //     //             const areaName = props.name || props.ADM4_EN || "Data not found";
            //     //             layer.bindPopup(`<strong>${areaName}</strong>`);
            //     //         }
            //     //     }).addTo(shapeLayer);

            //     //     index += chunkSize;

            //     //     // If there are more features, schedule the next batch
            //     //     if (index < features.length) {
            //     //         // requestAnimationFrame is better than setTimeout for smooth map performance
            //     //         requestAnimationFrame(renderNextChunk); 
            //     //     } else {
            //     //         console.log("All shapes rendered successfully.");
            //     //         // shapeLayer.bringToBack(); // Ensure they stay behind your incident markers
            //     //     }
            //     // }

            //     // // Start the chunked rendering
            //     // renderNextChunk();

            // } catch (error) {
            //     console.error('Failed to fetch data:', error);
            // }
        });

        return {
            breadcrumbs,
            selectedIncident,
            allIncidents,
            searchedIncidents,
            filteredIncidents,
            showFullList,
            focusIncident,
            getCategoryBg,
            filters,
            applyFilters,
            resetFilters,
            exportToCSV,
            searchQuery,
            isHeatmapEnabled,
            toggleHeatmap,
            activeCategories,
            categoryStats,
            selectAllCategories,
            clearCategories,
            updateMapVisualization,
        };
    },
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
</style>
