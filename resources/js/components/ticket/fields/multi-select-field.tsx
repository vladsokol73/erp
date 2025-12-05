import React from "react";
import { Label } from "@/components/ui/label";
import MultiSelect from "@/components/ui/multi-select";

export interface MultiSelectFieldProps {
    label?: string;
    value?: string[];
    onChange?: (val: string[]) => void;
    options: { label: string; value: string }[];
    placeholder?: string;
    error?: string;
}

export const MultiSelectField: React.FC<MultiSelectFieldProps> = ({
                                                                      label,
                                                                      value,
                                                                      onChange,
                                                                      options,
                                                                      placeholder,
                                                                      error,
                                                                  }) => (
    <div className="flex flex-col gap-2">
        {label && <Label>{label}</Label>}
        <MultiSelect
            options={options}
            value={value ?? []}
            onChange={onChange}
            placeholder={placeholder}
            error={error}
        />
    </div>
);
