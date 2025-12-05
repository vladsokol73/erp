import React from "react";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import { cn } from "@/lib/utils";

export interface TextareaFieldProps {
    label?: string;
    value?: string;
    onChange?: (val: string) => void;
    placeholder?: string;
    error?: string;
}

export const TextareaField: React.FC<TextareaFieldProps> = ({
                                                                label,
                                                                value,
                                                                onChange,
                                                                placeholder,
                                                                error,
                                                            }) => (
    <div className="flex flex-col gap-2">
        {label && <Label>{label}</Label>}
        <Textarea
            {...(onChange
                ? { value: value ?? "", onChange: (e) => onChange(e.target.value) }
                : { defaultValue: value })}
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
