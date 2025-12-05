import React from "react";
import {
    Tabs,
    TabsList,
    TabsTrigger,
} from "@/components/ui/tabs";
import { cn } from "@/lib/utils";

interface TabDefinition {
    label: React.ReactNode;
    value: string;
}

interface CustomTabsProps {
    tabs: TabDefinition[];
    value: string;
    onChange?: (value: string) => void;
    className?: string;
}

const CustomTabs: React.FC<CustomTabsProps> = ({
                                                   tabs,
                                                   value,
                                                   onChange,
                                                   className = "",
                                               }) => {
    const handleChange = (val: string) => {
        if (val !== value) {
            onChange?.(val);
        }
    };

    return (
        <Tabs value={value} onValueChange={handleChange} className={cn("w-full", className)}>
            <TabsList className="bg-transparent">
                {tabs.map((tab) => (
                    <TabsTrigger
                        key={tab.value}
                        value={tab.value}
                        className="data-[state=active]:bg-muted data-[state=active]:shadow-none"
                    >
                        {tab.label}
                    </TabsTrigger>
                ))}
            </TabsList>
        </Tabs>
    );
};

export default CustomTabs;
