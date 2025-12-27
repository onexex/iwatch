
<style>/* Custom Cluster Look */
.my-custom-cluster {
    background-color: rgba(59, 130, 246, 0.6); /* Blue with transparency */
    border-radius: 50%;
}
.my-custom-cluster div {
    width: 30px;
    height: 30px;
    margin-left: 5px;
    margin-top: 5px;
    text-align: center;
    border-radius: 50%;
    background-color: #2563eb; /* Darker blue center */
    color: white;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
}</style>
<template>
    <Head title="Verified Incident Tracker" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:flex-row">
            <div class="relative min-h-[500px] flex-1 overflow-hidden rounded-xl border bg-muted/50">
                
                <div id="map" class="h-full w-full"></div>

                <div v-if="!isHeatmapEnabled && categoryStats.length > 0" 
                     class="absolute bottom-6 left-3 z-[1000] rounded-xl border bg-background/90 p-4 shadow-xl backdrop-blur-md min-w-[170px] pointer-events-auto">
                    
                    <h4 class="mb-3 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">Classification Filter</h4>
                    
                    <div class="space-y-2.5">
                        <label v-for="cat in categoryStats" :key="cat.name" 
                               class="flex items-center justify-between group cursor-pointer">
                            
                            <div class="flex items-center gap-3">
                                <input type="checkbox" 
                                       :value="cat.name" 
                                       v-model="activeCategories"
                                       @change="updateMapVisualization"
                                       class="peer hidden" />
                                
                                <div class="h-4 w-4 rounded border border-muted-foreground/30 flex items-center justify-center transition-all shadow-sm"
                                     :class="activeCategories.includes(cat.name) ? cat.bgClass : 'bg-transparent'">
                                    <svg v-if="activeCategories.includes(cat.name)" class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>

                                <span class="text-[11px] font-bold text-foreground/80 group-hover:text-foreground transition-colors">
                                    {{ cat.name }}
                                </span>
                            </div>

                            <span class="text-[10px] font-mono font-bold bg-muted px-1.5 py-0.5 rounded text-muted-foreground">
                                {{ cat.count }}
                            </span>
                        </label>
                    </div>

                    <div class="mt-4 pt-2 border-t border-border/50 flex justify-between">
                        <button @click="selectAllCategories" class="text-[9px] font-black uppercase text-red-600 hover:underline">All</button>
                        <button @click="clearCategories" class="text-[9px] font-black uppercase text-muted-foreground hover:underline">None</button>
                    </div>
                </div>

                <div v-if="showFullList" 
                     class="absolute inset-y-0 right-0 z-[1001] w-80 border-l bg-background/95 p-4 shadow-xl backdrop-blur-md animate-in slide-in-from-right">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-bold">Search Incidents</h3>
                        <button @click="showFullList = false" class="text-muted-foreground text-xs hover:text-foreground">✕</button>
                    </div>
                    <input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Search barangay, ID, keyword..."
                        class="mb-4 w-full rounded-md border border-input bg-background px-3 py-2 text-xs focus:ring-2 focus:ring-primary"
                    />
                    <div class="space-y-2 overflow-y-auto h-[calc(100%-120px)] pr-2 custom-scrollbar">
                        <div 
                            v-for="incident in searchedIncidents" 
                            :key="incident.id"
                            @click="focusIncident(incident)"
                            class="group cursor-pointer rounded-lg border p-2 hover:bg-accent transition-all"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-mono font-bold">#{{ incident.id }}</span>
                                <span :class="['h-2 w-2 rounded-full', getCategoryBg(incident.type)]"></span>
                            </div>
                            <p class="text-[11px] font-semibold text-primary truncate">{{ incident.barangay?.ADM4_EN }}</p>
                            <p class="text-[10px] text-muted-foreground line-clamp-2 leading-tight">{{ incident.description }}</p>
                        </div>
                    </div>
                </div>

                <button 
                    @click="showFullList = !showFullList"
                    class="absolute top-4 right-4 z-[1000] flex items-center gap-2 rounded-md bg-white px-4 py-2 text-xs font-bold shadow-md hover:bg-gray-50 dark:bg-slate-900"
                >
                    <span>📋 View List ({{ searchedIncidents.length }})</span>
                </button>

                <div class="absolute top-6 left-3 z-[1000] flex w-72 md:w-80 flex-col gap-4 pointer-events-auto">
                    <div class="rounded-xl border bg-card/90 backdrop-blur-md shadow-xl ring-1 ring-black/5 overflow-hidden">
                        <div 
                            @click="isSidebarCollapsed = !isSidebarCollapsed" 
                            class="flex items-center justify-between p-4 cursor-pointer hover:bg-muted/30 transition-colors"
                        >
                            <label class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground cursor-pointer">
                                Map Controls
                            </label>
                            <button class="text-muted-foreground transition-transform duration-200" :class="{ 'rotate-180': isSidebarCollapsed }">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
                            </button>
                        </div>

                        <div v-show="!isSidebarCollapsed" class="p-4 pt-0 space-y-4 animate-in fade-in slide-in-from-top-2 duration-200">
                            <hr class="border-border/50 mb-4" />
                            
                            <label class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground">Visualization Mode</label>
                            <div class="mt-2 mb-4 flex items-center justify-between rounded-lg border bg-muted/50 p-2">
                                <span class="text-xs font-medium">Heatmap View</span>
                                <button 
                                    @click.stop="toggleHeatmap" 
                                    :class="[isHeatmapEnabled ? 'bg-red-600' : 'bg-slate-300']"
                                    class="relative inline-flex h-5 w-10 items-center rounded-full transition-colors outline-none"
                                >
                                    <span :class="[isHeatmapEnabled ? 'translate-x-5' : 'translate-x-1']" class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform" />
                                </button>
                            </div>

                            <label class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground">Filters</label>
                            <div class="mt-2 space-y-2">
                                <input type="date" v-model="filters.startDate" class="w-full rounded border bg-background px-2 py-1.5 text-xs focus:ring-1 focus:ring-primary" />
                                <input type="date" v-model="filters.endDate" class="w-full rounded border bg-background px-2 py-1.5 text-xs focus:ring-1 focus:ring-primary" />
                                
                                <div class="flex gap-2 pt-1">
                                    <button @click="applyFilters" class="flex-1 rounded bg-primary py-1.5 text-[11px] font-bold text-primary-foreground hover:opacity-90">
                                        Apply
                                    </button>
                                    <button @click="resetFilters" class="rounded border bg-background px-3 py-1.5 text-[11px] hover:bg-muted">
                                        Reset
                                    </button>
                                </div>

                                <button 
                                    @click="exportToCSV"
                                    :disabled="searchedIncidents.length === 0"
                                    class="w-full mt-2 flex items-center justify-center gap-2 rounded border border-green-600/30 bg-green-50/80 px-2 py-2 text-[11px] font-bold text-green-700 hover:bg-green-100 disabled:opacity-50"
                                >
                                    📥 Export ({{ searchedIncidents.length }})
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="selectedIncident" 
                        class="animate-in slide-in-from-left duration-300 rounded-xl border bg-card/95 backdrop-blur-md p-4 shadow-xl ring-2 ring-primary/10 max-h-[400px] overflow-y-auto pointer-events-auto">
                        
                        <div class="sticky top-0 bg-card/0 pb-2 mb-2 flex items-start justify-between border-b border-border/50">
                            <h3 class="text-lg font-bold">Incident Details</h3>
                            <button @click="selectedIncident = null" class="text-muted-foreground hover:text-foreground p-1 text-lg">✕</button>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="rounded-lg bg-red-500/10 p-3 border border-red-500/20">
                                <label class="text-[10px] font-bold uppercase text-muted-foreground">Barangay</label>
                                <p class="text-sm font-semibold text-red-600">{{ selectedIncident.barangay?.ADM4_EN }}</p>
                            </div>
                            
                            <div>
                                <label class="text-[10px] font-bold uppercase text-muted-foreground">Narrative</label>
                                <p class="text-xs leading-relaxed text-foreground/80 mt-1 whitespace-pre-wrap">
                                    {{ selectedIncident.description }}
                                </p>
                            </div>
                            
                            <div class="pt-2 border-t border-border/50">
                                <p class="text-[10px] text-muted-foreground italic">
                                    📅 Recorded: {{ new Date(selectedIncident.created_at).toLocaleString() }}
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
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { defineComponent, onMounted, ref, computed, watch } from 'vue';
 
import * as L from 'leaflet';
import 'leaflet.heat';

import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import 'leaflet.markercluster';

interface BarangayProperties {
    ADM4_EN: string;
    latitude: number;
    longitude: number;
}

interface Incident {
    id: number;
    sms_id: number;
    description: string;
    type: string; 
    created_at: string; 
    barangay: BarangayProperties; 
}
        
export default defineComponent({
    name: 'MapView',
    components: { AppLayout, Head },
    data() {
        return {
            isSidebarCollapsed: false,
        }
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
            if (!filters.value.startDate || !filters.value.endDate) return allIncidents.value;
            const start = new Date(filters.value.startDate).setHours(0,0,0,0);
            const end = new Date(filters.value.endDate).setHours(23,59,59,999);
            return allIncidents.value.filter(inc => {
                const incDate = new Date(inc.created_at).getTime();
                return incDate >= start && incDate <= end;
            });
        });

        // Combined Filtering (Dates + Search + Classification Checkboxes)
        const searchedIncidents = computed(() => {
            const query = searchQuery.value.toLowerCase().trim();
            
            return filteredIncidents.value.filter(inc => {
                const matchesSearch = !query || 
                    inc.description.toLowerCase().includes(query) || 
                    inc.barangay?.ADM4_EN?.toLowerCase().includes(query) ||
                    inc.id.toString().includes(query);

                const matchesCategory = activeCategories.value.length === 0 || activeCategories.value.includes(inc.type);

                return matchesSearch && matchesCategory;
            });
        });

        // Legend logic with counts
        const categoryStats = computed(() => {
            const types = [...new Set(allIncidents.value.map(i => i.type))].filter(Boolean);
            return types.map(type => ({
                name: type,
                count: allIncidents.value.filter(i => i.type === type).length,
                bgClass: getCategoryBg(type)
            })).sort((a, b) => b.count - a.count);
        });

        const getCategoryColor = (type: string) => {
            const t = (type || '').toLowerCase().trim();
            if (t === 'tg1') return '#dc2626';
            if (t === 'tg2') return '#2563eb';
            if (t === 'ciss') return '#000000';
            if (t === 'wanted') return '#eab308';
            return '#16a34a';
        };

        const getCategoryBg = (type: string) => {
            const t = (type || '').toLowerCase().trim();
            if (t === 'tg1') return 'bg-red-600';
            if (t === 'tg2') return 'bg-blue-600';
            if (t === 'ciss') return 'bg-black';
            if (t === 'wanted') return 'bg-yellow-500';
            return 'bg-green-600';
        };

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
                        iconSize: [22, 22], iconAnchor: [11, 22]
                    });
                    const marker = L.marker([incident.barangay.latitude, incident.barangay.longitude], { icon: customIcon });
                    marker.on('click', () => {
                        selectedIncident.value = incident;
                        map.setView([incident.barangay.latitude, incident.barangay.longitude], 16);
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
                    .filter(inc => inc.barangay?.latitude && inc.barangay?.longitude)
                    .map(inc => [
                        Number(inc.barangay.latitude), 
                        Number(inc.barangay.longitude), 
                        0.7 
                    ]);

                if ((L as any).heatLayer) {
                    heatLayer = (L as any).heatLayer(heatPoints, {
                        radius: 25,
                        blur: 15,
                        maxZoom: 17
                    }).addTo(map);
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
            activeCategories.value = [...new Set(allIncidents.value.map(i => i.type))].filter(Boolean);
            updateMapVisualization();
        };

        const selectAllCategories = () => {
            activeCategories.value = categoryStats.value.map(s => s.name);
            updateMapVisualization();
        };

        const clearCategories = () => {
            activeCategories.value = [];
            updateMapVisualization();
        };

        const exportToCSV = () => {
            const data = searchedIncidents.value;
            const headers = ["ID", "Date", "Type", "Barangay", "Description"];
            const rows = data.map(inc => [inc.id, inc.created_at, inc.type, inc.barangay?.ADM4_EN, `"${inc.description}"`]);
            const csv = [headers, ...rows].map(e => e.join(",")).join("\n");
            const blob = new Blob([csv], { type: 'text/csv' });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = `incident_report_${new Date().getTime()}.csv`;
            link.click();
        };

        const focusIncident = (incident: Incident) => {
            selectedIncident.value = incident;
            showFullList.value = false;
            isHeatmapEnabled.value = false;
            updateMapVisualization();
            map.setView([incident.barangay.latitude, incident.barangay.longitude], 16);
        };

        // Initialize checkboxes when data loads
        watch(allIncidents, (newVal) => {
            if (newVal.length > 0 && activeCategories.value.length === 0) {
                activeCategories.value = [...new Set(newVal.map(i => i.type))].filter(Boolean);
            }
        }, { immediate: true });

        onMounted(async () => {
            map = L.map('map', { zoomControl: false }).setView([8.1541, 123.2588], 8);
            
            const googleStyle = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], attribution: '© Google'
            });
            const darkStyle = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png');
            const voyagerStyle = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png');

            // googleStyle.addTo(map);
            voyagerStyle.addTo(map);

            const baseMaps = {
                "Standard": googleStyle,
                "Night Mode": darkStyle,
                "Voyager": voyagerStyle 
            };

            // L.control.layers(baseMaps, {} , { position: 'bottomright' }).addTo(map);
            // markers = (L as any).markerClusterGroup();
            // map.addLayer(markers);

            L.control.layers(baseMaps, {}, { position: 'bottomright' }).addTo(map);

// Initialize with a custom icon function
markers = (L as any).markerClusterGroup({
    iconCreateFunction: function(cluster: any) {
        const count = cluster.getChildCount();
        
        // Return a custom DivIcon
        return L.divIcon({
            html: `<div><span>${count}</span></div>`,
            className: 'my-custom-cluster', // Use our CSS class here
            iconSize: L.point(40, 40)
        });
    }
});

map.addLayer(markers);

            // markers = L.layerGroup().addTo(map);

            try {
                const res = await fetch('/mapping_incidents');
                allIncidents.value = await res.json();
                updateMapVisualization();
            } catch (error) {
                console.error("Failed to fetch incidents:", error);
            }
        });

        return { 
            breadcrumbs, selectedIncident, allIncidents, searchedIncidents, filteredIncidents,
            showFullList, focusIncident, getCategoryBg, filters, applyFilters, resetFilters,
            exportToCSV, searchQuery, isHeatmapEnabled, toggleHeatmap, 
            activeCategories, categoryStats, selectAllCategories, clearCategories, updateMapVisualization
        };
    },
});
</script>

<style scoped>


.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>