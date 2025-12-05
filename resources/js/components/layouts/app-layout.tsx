import React from "react";
import { AppSidebar } from "@/components/common/app-sidebar"
import { Separator } from "@/components/ui/separator"
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from "@/components/ui/sidebar"
import AppBreadcrumb from "@/components/common/app-breadcrumb"
import TimezoneSelector from "@/components/common/timezone-selector";
import {NotificationsPopover} from "@/components/common/notifications-popover";
import { Button } from "@/components/ui/button";
import {Presentation} from "lucide-react";
import { Link } from "@inertiajs/react";
import { route } from "ziggy-js";

export default function AppLayout({ children }: { children?: React.ReactNode }) {
    return (
        <SidebarProvider>
            <AppSidebar />
            <SidebarInset>
                <header className="flex justify-between h-16 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12">
                    <div className="flex items-center gap-2 px-4">
                        <SidebarTrigger className="-ml-1" />
                        <Separator orientation="vertical" className="mr-2 h-4" />
                        <AppBreadcrumb
                            homeTitle="Home"
                            excludePaths={["/", "/login"]}
                        />
                    </div>
                    <div className="flex gap-4 items-center px-5">
                        <Link href={route("meet.show")}>
                            <Button
                                variant="outline"
                                title="Видеозвонки"
                            >
                                <Presentation className="h-4 w-4" />
                                Meet
                            </Button>
                        </Link>
                        <NotificationsPopover/>
                        <TimezoneSelector/>
                    </div>
                </header>
                <div className="flex flex-1 flex-col gap-4 p-4 pt-0">
                    {children}
                </div>
            </SidebarInset>
        </SidebarProvider>
    )
}
