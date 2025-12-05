import * as React from "react";
import {
    Headset,
    Settings2,
    MonitorPlay,
    Users,
    Link,
    Tickets,
    UserCheck,
} from "lucide-react";

import { NavMain } from "@/components/common/nav-main";
import type { NavItem } from "@/components/common/nav-main";
import { NavUser } from "@/components/common/nav-user";
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarRail,
} from "@/components/ui/sidebar";
import { useRoute } from "ziggy-js";
import { usePage, Link as InertiaLink } from "@inertiajs/react";
import { useSidebar } from "@/components/ui/sidebar";
import { cn } from "@/lib/utils";

// Интерфейсы для типизации
interface User {
    id: number;
    name: string;
    email: string;
}

interface Auth {
    user: User;
}

interface PageProps {
    auth: Auth;
    [key: string]: any;
}

// Основной компонент
export function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
    const route = useRoute();
    const { auth } = usePage<PageProps>().props;

    // Генератор URL по имени маршрута
    const pathRoute = (routeName: string) => route(routeName, {}, false);

    // Данные пользователя для NavUser
    const userData = {
        id: auth.user.id,
        name: auth.user.name,
        email: auth.user.email,
    };

    // Данные для навигации
    const navItems: NavItem[] = [
        {
            title: "Creatives",
            icon: MonitorPlay,
            items: [
                { title: "Library", url: pathRoute("creatives.library.show") },
                {
                    title: "New creative",
                    permission: "creatives.create",
                    url: pathRoute("creatives.new_creative.show"),
                },
                {
                    title: "Tags",
                    permission: "creatives.tags",
                    url: pathRoute("creatives.tags.show"),
                },
                { title: "Favorites", url: pathRoute("creatives.favorites.show") },
            ],
        },
        {
            title: "Operators",
            icon: Headset,
            permission: "operators.show",
            items: [{ title: "Statistic", url: pathRoute("operators.statistic.show") }],
        },
        {
            title: "Clients",
            icon: Users,
            permission: "clients.show",
            items: [
                { title: "All Clients", url: pathRoute("clients.all.clients.show") },
                { title: "Failed Jobs", url: pathRoute("clients.failed.jobs.show") },
            ],
        },
        {
            title: "Shorter URL",
            icon: Link,
            permission: "shorter.show",
            items: [
                { title: "Short URL", url: pathRoute("shorter.show") },
                { title: "Manage URL", url: pathRoute("shorter.url.show") },
                { title: "Manage Domain", url: pathRoute("shorter.domains.show") },
            ],
        },
        {
            title: "Tickets",
            icon: Tickets,
            items: [
                { title: "Create Ticket", url: pathRoute("tickets.create.show") },
                { title: "My Tickets", url: pathRoute("tickets.my.show") },
                {
                    title: "Moderation",
                    permission: "tickets.moderation",
                    url: pathRoute("tickets.moderation.show"),
                },
                {
                    title: "All Tickets",
                    role: "admin",
                    url: pathRoute("tickets.all.show"),
                },
                {
                    title: "Ticket Settings",
                    permission: "tickets.settings",
                    matchPaths: [
                        pathRoute("tickets.settings.categories.show"),
                        pathRoute("tickets.settings.statuses.show"),
                        pathRoute("tickets.settings.topic.show"),
                        pathRoute("tickets.settings.fields.show"),
                    ],
                    url: pathRoute("tickets.settings.categories.show"),
                },
            ],
        },
        {
            title: "Check Player",
            icon: UserCheck,
            role: "operator",
            permission: "check_player.show",
            strategy: "OR",
            items: [
                { title: "My Tickets", url: pathRoute("tickets.player.show") },
                {
                    title: "Moderation",
                    permission: "check_player.moderation",
                    url: pathRoute("tickets.player.show.moderation"),
                },
                {
                    title: "Product Logs",
                    permission: "check_player.moderation",
                    url: pathRoute("tickets.player.logs.show"),
                }
            ],
        },
        {
            title: "Admin Settings",
            icon: Settings2,
            role: "admin",
            permission: "ai.retentions.show",
            strategy: "OR",
            items: [
                { title: "Users", url: pathRoute("admin-panel.users.show"), role: "admin" },
                { title: "Operators", url: pathRoute("admin-panel.operators.show"), role: "admin" },
                { title: "Api tokens", url: pathRoute("admin-panel.api.tokens.show"), role: "admin" },
                {
                    title: "Ai Retentions",
                    url: pathRoute("admin-panel.ai-reports.show"),
                    permission: "ai.retentions.show",
                },
            ],
        },
    ];

    const { open, openMobile, isMobile } = useSidebar();
    const isSidebarOpen = isMobile ? openMobile : open;

    return (
        <Sidebar collapsible="icon" {...props}>
            <InertiaLink href={route("home")}>
                <div
                    className={cn(
                        "flex items-center transition-all duration-300",
                        isMobile ? "justify-start ml-2" : "justify-center",
                        isSidebarOpen ? "p-2 opacity-100 scale-100" : "p-1 my-2 opacity-100 scale-90"
                    )}
                >
                    <img
                        src={isSidebarOpen ? "/assets/images/logo.svg" : "/assets/images/favicon-dark.svg"}
                        alt="logo"
                        className={cn(
                            "transition-all duration-300",
                            isSidebarOpen ? "h-8 w-auto" : "h-6 w-6"
                        )}
                    />
                </div>
            </InertiaLink>

            <SidebarContent>
                <NavMain items={navItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavUser user={userData} />
            </SidebarFooter>

            <SidebarRail />
        </Sidebar>
    );
}
