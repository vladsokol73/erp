import React from "react";
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from "@/components/ui/select";
import { Label } from "@/components/ui/label";
import { cn } from "@/lib/utils";

export interface SelectFieldProps {
    label?: string;
    value?: string;
    onChange?: (val: string) => void;
    options: string[];
    placeholder?: string;
    error?: string; // ✅ добавлено
}

export const SelectField: React.FC<SelectFieldProps> = ({
                                                            label,
                                                            value,
                                                            onChange,
                                                            options,
                                                            placeholder,
                                                            error,
                                                        }) => (
    <div className="flex flex-col gap-2">
        {label && <Label>{label}</Label>}
        <Select value={value} onValueChange={onChange}>
            <SelectTrigger
                className={cn("w-full", error && "border-destructive focus:ring-destructive")}
            >
                <SelectValue placeholder={placeholder ?? `Select ${label}`} />
            </SelectTrigger>
            <SelectContent>
                {options.map(option => (
                    <SelectItem key={option} value={option}>
                        {option}
                    </SelectItem>
                ))}
            </SelectContent>
        </Select>
        {error && (
            <p className="text-sm text-destructive mt-1">
                {error}
            </p>
        )}
    </div>
);
