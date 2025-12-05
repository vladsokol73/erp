import React, { useState, useEffect } from "react";
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
    options?: number[];
}

const DEFAULT_OPTIONS = [16, 32, 48];

export const PerPageDropdown = ({
                                    initialValue = DEFAULT_OPTIONS[0],
                                    onChange,
                                    options = DEFAULT_OPTIONS,
                                }: PerPageDropdownProps) => {
    const [value, setValue] = useState<number>(initialValue);

    useEffect(() => {
        setValue(initialValue);
    }, [initialValue]);

    const handleSelect = (val: number) => {
        setValue(val);
        onChange?.(val);
    };

    return (
        <div className="flex items-center gap-4">
            <span className="text-sm">
                Per page
            </span>
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="outline">
                        {value}
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent className="min-w-24" side="bottom" align="center">
                    {options.map((option) => (
                        <DropdownMenuCheckboxItem
                            key={option}
                            checked={option === value}
                            onCheckedChange={() => handleSelect(option)}
                        >
                            {option}
                        </DropdownMenuCheckboxItem>
                    ))}
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
};

export default PerPageDropdown;
