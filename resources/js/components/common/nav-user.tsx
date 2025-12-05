"use client"

import {
    ChevronsUpDown, Copy,
    LogOut,
    Settings,
} from "lucide-react"

import {
    Avatar,
    AvatarFallback,
    AvatarImage,
} from "@/components/ui/avatar"
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from "@/components/ui/sidebar"
import { Link } from "@inertiajs/react";
import { useRoute } from "ziggy-js";
import React from "react";

export function NavUser({
                            user,
                        }: {
    user: {
        id : number
        name: string
        email: string
    }
}) {
    const { isMobile } = useSidebar()

    const route = useRoute();

    const [clipboardSuccess, setClipboardSuccess ] = React.useState(false);

    return (
        <SidebarMenu>
            <SidebarMenuItem>
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <SidebarMenuButton
                            size="lg"
                            className="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                        >
                            <Avatar className="h-8 w-8 rounded-lg">
                                <AvatarFallback className="rounded-lg">TS</AvatarFallback>
                            </Avatar>
                            <div className="grid flex-1 text-left text-sm leading-tight">
                                <span className="truncate font-semibold">{user.name}</span>
                                <span className="truncate text-xs">{user.email}</span>
                            </div>
                            <ChevronsUpDown className="ml-auto size-4" />
                        </SidebarMenuButton>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        className="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                        side={isMobile ? "bottom" : "right"}
                        align="end"
                        sideOffset={4}
                    >
                        <DropdownMenuLabel className="p-0 font-normal">
                            <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <Avatar className="h-8 w-8 rounded-lg">
                                    <AvatarFallback className="rounded-lg">TS</AvatarFallback>
                                </Avatar>
                                <div className="grid flex-1 text-left text-sm leading-tight">
                                    <span className="truncate font-semibold">{user.name}</span>
                                    <div className="flex items-center gap-2">
                                        <div className="flex gap-1">
                                            <span className="truncate text-xs text-muted-foreground">USER ID:</span>
                                            <span className="text-xs">{user.id}</span>
                                        </div>
                                        <button onClick={() => {
                                            navigator.clipboard.writeText(user.id.toString()).then();
                                            setClipboardSuccess(true);
                                            setTimeout(() => {
                                                setClipboardSuccess(false);
                                            }, 2000);
                                        }}>
                                            <Copy className="size-3" />
                                        </button>
                                        {
                                            clipboardSuccess
                                                ? <span className="text-xs text-green">Copied</span>
                                                : null
                                        }
                                    </div>
                                </div>
                            </div>
                        </DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuGroup>
                            <Link href={route('account.settings.show')}>
                                <DropdownMenuItem>
                                    <Settings />
                                    Settings
                                </DropdownMenuItem>
                            </Link>
                        </DropdownMenuGroup>
                        <DropdownMenuSeparator />
                        <Link href={route('logout')}>
                            <DropdownMenuItem
                                className="cursor-pointer"
                                variant="destructive"
                            >
                                <LogOut />
                                Log out
                            </DropdownMenuItem>
                        </Link>
                    </DropdownMenuContent>
                </DropdownMenu>
            </SidebarMenuItem>
        </SidebarMenu>
    )
}
