import React, { useEffect, useState } from "react";
import { cn } from "@/lib/utils";
import { Link } from "@inertiajs/react";

type TabItem = {
    label: string;
    href: string;
};

interface HorizontalTabsProps {
    tabs: TabItem[];
}

export const HorizontalTabs: React.FC<HorizontalTabsProps> = ({ tabs }) => {
    const [currentPath, setCurrentPath] = useState("");

    useEffect(() => {
        const fullPath = window.location.origin + window.location.pathname;
        setCurrentPath(fullPath);
    }, []);

    return (
        <div className="border-b">
            <nav className="flex space-x-4">
                {tabs.map((tab) => {
                    const isActive = currentPath === tab.href;
                    return (
                        <Link
                            key={tab.href}
                            href={tab.href}
                            className={cn(
                                "px-3 py-2 -mb-px border-b-2 text-sm font-medium transition-colors",
                                isActive
                                    ? "border-primary text-primary"
                                    : "border-transparent text-muted-foreground hover:text-primary"
                            )}
                        >
                            {tab.label}
                        </Link>
                    );
                })}
            </nav>
        </div>
    );
};
