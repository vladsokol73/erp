import React from "react";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { cn } from "@/lib/utils";

export interface TextFieldProps {
    label?: string;
    value?: string;
    onChange?: (val: string) => void;
    placeholder?: string;
    error?: string; // ✅ добавлено
}

export const TextField: React.FC<TextFieldProps> = ({
                                                        label,
                                                        value,
                                                        onChange,
                                                        placeholder,
                                                        error,
                                                    }) => (
    <div className="flex flex-col gap-2">
        {label && <Label>{label}</Label>}
        <Input
            value={value ?? ""}
            onChange={onChange ? (e) => onChange(e.target.value) : undefined}
            placeholder={placeholder ?? (label ? `Enter ${label}` : undefined)}
            className={cn(error && "border-destructive focus-visible:ring-destructive")}
        />
        {error && (
            <p className="text-sm text-destructive mt-1">
                {error}
            </p>
        )}
    </div>
);
