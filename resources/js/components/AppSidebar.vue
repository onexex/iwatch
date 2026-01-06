<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
// import { sms } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, ListIcon, Minus } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { MessageSquare } from 'lucide-vue-next';
import { MapPinMinus } from 'lucide-vue-next';
import { UserRoundCog } from 'lucide-vue-next';
import { ChartNoAxesCombined } from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
     {
        title: 'Incoming Alerts',
        href: '/sms',
        icon: MessageSquare,
    },

     {
        title: 'Processed Messages',
        href: '/processed-messages',
        icon: MessageSquare,
    },
     {
        title: 'Verified Incident Tracker',
        href: '/mapping',
        icon: MapPinMinus,
    },
    {
        title: 'Classifications',
        href: '/classifications',
        icon: ListIcon,
    },

    
];

const footerNavItems: NavItem[] = [
    {
        title: 'Assesment / Analytics',
        href: '',
        icon: BookOpen,
    },
    {
        title: 'Data Graphs',
        href: '',
        icon: ChartNoAxesCombined,
    },
     {
        title: 'Users Management',
        href: '/users',
        icon: UserRoundCog,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
