import { useState } from "react";
import { Button } from "@/components/ui/button";
import { ArrowDownUp } from "lucide-react";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuCheckboxItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

interface SortDropdownProps {
    initialSortType?: string;
    onSortChange?: (value: string) => void;
}

const sortOptions = [
    { value: "date_desc", label: "Date: New" },
    { value: "date_asc", label: "Date: Old" },
    { value: "number_desc", label: "Ticket Number: High" },
    { value: "number_asc", label: "Ticket Number: Low" },
    { value: "status_asc", label: "Status: Asc" },
    { value: "status_desc", label: "Status: Desc" },
];

const SortDropdown = ({ initialSortType = "date_desc", onSortChange }: SortDropdownProps) => {
    const [sortType, setSortType] = useState(initialSortType);

    const handleSortChange = (value: string) => {
        setSortType(value);
        onSortChange?.(value);
    };

    return (
        <div className="flex justify-end">
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="ghost">
                        <ArrowDownUp className="mr-2 h-4 w-4" />
                        Sort
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent className="min-w-52" side="bottom" align="end">
                    {sortOptions.map((option) => (
                        <DropdownMenuCheckboxItem
                            key={option.value}
                            checked={sortType === option.value}
                            onCheckedChange={() => handleSortChange(option.value)}
                        >
                            {option.label}
                        </DropdownMenuCheckboxItem>
                    ))}
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
};

export default SortDropdown;
