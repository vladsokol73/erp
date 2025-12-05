import React, { useEffect, useState } from "react";
import { cn } from "@/lib/utils";
import {Link} from "@inertiajs/react";

type TabItem = {
    label: string;
    href: string;
};

interface SidebarTabsProps {
    tabs: TabItem[];
}

export const SidebarTabs: React.FC<SidebarTabsProps> = ({ tabs }) => {
    const [currentPath, setCurrentPath] = useState<string>("");

    useEffect(() => {
        const fullPath = window.location.origin + window.location.pathname;
        setCurrentPath(fullPath);
    }, []);


    return (
        <div className="flex text-sm flex-col rounded-none border-l bg-transparent p-0 h-fit min-w-32 gap-2 text-muted-foreground cursor-default">
            {tabs.map((tab) => {
                const isActive = currentPath === tab.href;

                return (
                    <Link
                        key={tab.href}
                        href={tab.href}
                        data-state={isActive ? "active" : undefined}
                        className={cn(
                            "pl-3 relative w-full justify-start rounded-none hover:text-primary/75 after:absolute after:inset-y-0 after:start-0 after:w-0.5",
                            "data-[state=active]:text-primary data-[state=active]:after:bg-primary"
                        )}
                    >
                        {tab.label}
                    </Link>
                );
            })}
        </div>
    );
};
