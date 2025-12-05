import React from "react";
import {
    NumberField,
    Label as AriaLabel,
    Group,
    Input as AriaInput,
    Button as AriaButton,
    FieldError,
} from "react-aria-components";
import { ChevronUpIcon, ChevronDownIcon } from "lucide-react";
import { cn } from "@/lib/utils";

export interface NumberFieldAriaProps {
    label?: string;
    value?: number;
    onChange?: (val: number) => void;
    placeholder?: string;
    error?: string;
}

export const NumberFieldAria: React.FC<NumberFieldAriaProps> = ({
                                                                    label,
                                                                    value,
                                                                    onChange,
                                                                    placeholder,
                                                                    error,
                                                                }) => (
    <NumberField
        className="flex flex-col gap-2"
        value={value}
        onChange={(val) => {
            if (!isNaN(val)) {
                onChange?.(val);
            }
        }}
        isInvalid={!!error}
        aria-label={label ?? placeholder ?? "Number input"}
    >
    {label && (
            <AriaLabel className="text-foreground text-sm font-medium">
                {label}
            </AriaLabel>
        )}

        <Group
            className={cn(
                "border-input outline-none data-focus-within:border-ring data-focus-within:ring-ring/50 data-focus-within:has-aria-invalid:ring-destructive/20 dark:data-focus-within:has-aria-invalid:ring-destructive/40 data-focus-within:has-aria-invalid:border-destructive relative inline-flex h-9 w-full items-center overflow-hidden rounded-md border text-sm whitespace-nowrap shadow-xs transition-[color,box-shadow] data-disabled:opacity-50 data-focus-within:ring-[3px]",
                error && "border-destructive ring-destructive/30"
            )}
        >
            <AriaInput
                className="bg-background text-foreground flex-1 px-3 py-2 tabular-nums"
                placeholder={placeholder ?? label}
            />
            <div className="flex flex-col h-[calc(100%+2px)]">
                <AriaButton
                    slot="increment"
                    className="border-input bg-background text-muted-foreground/80 hover:bg-accent hover:text-foreground -me-px flex h-1/2 w-6 flex-1 items-center justify-center border text-sm transition-[color,box-shadow] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <ChevronUpIcon size={12} />
                </AriaButton>
                <AriaButton
                    slot="decrement"
                    className="border-input bg-background text-muted-foreground/80 hover:bg-accent hover:text-foreground -me-px -mt-px flex h-1/2 w-6 flex-1 items-center justify-center border text-sm transition-[color,box-shadow] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <ChevronDownIcon size={12} />
                </AriaButton>
            </div>
        </Group>

        {error && (
            <FieldError className="text-sm text-destructive mt-1">
                {error}
            </FieldError>
        )}
    </NumberField>
);
