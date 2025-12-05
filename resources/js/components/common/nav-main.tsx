"use client";

import { ChevronRight, type LucideIcon } from "lucide-react";
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from "@/components/ui/collapsible";
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from "@/components/ui/sidebar";

import { Link, usePage } from "@inertiajs/react";
import Access from "@/components/common/access";

export type NavSubItem = {
    title: string;
    url: string;
    permission?: string | string[];
    role?: string | string[];
    matchPaths?: string[];
    strategy?: "AND" | "OR";
};

export type NavItem = {
    title: string;
    icon?: LucideIcon;
    permission?: string | string[];
    role?: string | string[];
    strategy?: "AND" | "OR";
    items?: NavSubItem[];
};

type PageProps = Record<string, unknown>;

function normalizePath(path: string): string {
    const noHash = path.split("#")[0];
    const noQuery = noHash.split("?")[0];
    // убираем завершающий слеш, но не у корня
    return noQuery.length > 1 && noQuery.endsWith("/") ? noQuery.slice(0, -1) : noQuery;
}

/** Проверяет, активен ли путь, учитывая matchPaths (если указано) */
function isActivePath(
    pathname: string,
    item: { url: string; matchPaths?: string[] }
): boolean {
    const current = normalizePath(pathname)
    const candidates = (item.matchPaths ?? [item.url]).map(normalizePath)
    return candidates.includes(current)
}


export function NavMain({ items }: { items: NavItem[] }) {
    const page = usePage<PageProps>();
    const pathname = normalizePath(String(page.url || "/"));

    return (
        <SidebarGroup>
            <SidebarGroupLabel>User Interface</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item, idx) => {
                    const hasActiveSubItem = item.items?.some((sub) => isActivePath(pathname, sub)) ?? false;

                    return (
                        <Access
                            key={`group-${idx}-${item.title}`}
                            role={item.role}
                            permission={item.permission}
                            strategy={item.strategy ?? "OR"}
                        >
                            <Collapsible asChild defaultOpen={hasActiveSubItem} className="group/collapsible">
                                <SidebarMenuItem>
                                    <CollapsibleTrigger asChild>
                                        <SidebarMenuButton tooltip={item.title}>
                                            {item.icon && <item.icon />}
                                            <span>{item.title}</span>
                                            <ChevronRight className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                        </SidebarMenuButton>
                                    </CollapsibleTrigger>

                                    <CollapsibleContent>
                                        <SidebarMenuSub>
                                            {item.items?.map((subItem, sIdx) => (
                                                <Access
                                                    key={`sub-${idx}-${sIdx}-${subItem.title}`}
                                                    role={subItem.role}
                                                    permission={subItem.permission}
                                                    strategy={subItem.strategy ?? item.strategy ?? "OR"}
                                                >
                                                    <SidebarMenuSubItem>
                                                        <SidebarMenuSubButton
                                                            asChild
                                                            isActive={isActivePath(pathname, subItem)}
                                                        >
                                                            <Link href={subItem.url}>
                                                                <span>{subItem.title}</span>
                                                            </Link>
                                                        </SidebarMenuSubButton>
                                                    </SidebarMenuSubItem>
                                                </Access>
                                            ))}
                                        </SidebarMenuSub>
                                    </CollapsibleContent>
                                </SidebarMenuItem>
                            </Collapsible>
                        </Access>
                    );
                })}
            </SidebarMenu>
        </SidebarGroup>
    );
}
