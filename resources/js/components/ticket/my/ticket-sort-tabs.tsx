import React from "react";
import CustomTabs from "@/components/common/input/custom-tabs";

interface TicketSortTabsProps {
    value: string;
    onChange: (value: string) => void;
    tabs: { label: React.ReactNode; value: string }[];
}

const TicketSortTabs = ({ value, onChange, tabs }: TicketSortTabsProps) => {
    return (
        <div className="py-1 overflow-y-auto min-h-16">
            <CustomTabs value={value} onChange={onChange} tabs={tabs} />
        </div>
    );
};

export default TicketSortTabs;
