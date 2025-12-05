import { useState } from "react";
import { Button } from "@/components/ui/button";
import { ListOrdered } from "lucide-react";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuCheckboxItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

interface PerPageDropdownProps {
    initialValue?: number;
    onChange?: (value: number) => void;
}

const options = [16, 32, 48];

const PerPageDropdown = ({ initialValue = 16, onChange }: PerPageDropdownProps) => {
    const [selectedValue, setSelectedValue] = useState<number>(initialValue);

    const handleSelect = (option: number) => {
        setSelectedValue(option);
        onChange?.(option);
    };

    return (
        <div className="flex justify-end">
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="ghost">
                        <ListOrdered className="mr-2 h-4 w-4" />
                        Per Page: {selectedValue}
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent className="min-w-44" side="bottom" align="end">
                    {options.map((opt) => (
                        <DropdownMenuCheckboxItem
                            key={opt}
                            checked={selectedValue === opt}
                            onCheckedChange={() => handleSelect(opt)}
                        >
                            {opt}
                        </DropdownMenuCheckboxItem>
                    ))}
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
};

export default PerPageDropdown;
