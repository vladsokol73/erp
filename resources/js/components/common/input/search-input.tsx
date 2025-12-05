import React, { InputHTMLAttributes, useEffect, useRef, useState } from "react";
import { Search as SearchIcon, X as XIcon } from "lucide-react";
import { Input } from "@/components/ui/input";
import { LucideIcon } from "lucide-react";
import { useDebounce } from "use-debounce";

interface InputSearchProps extends Omit<InputHTMLAttributes<HTMLInputElement>, 'type' | 'onChange'> {
    placeholder?: string;
    icon?: LucideIcon;
    iconSize?: number;
    debounceDelay?: number;
    onChangeDebounced?: (value: string) => void;
    onChange?: (e: React.ChangeEvent<HTMLInputElement> | string) => void;
}

export default function InputSearch({
                                        placeholder = "Search...",
                                        icon: Icon = SearchIcon,
                                        iconSize = 16,
                                        debounceDelay = 500,
                                        onChangeDebounced,
                                        onChange,
                                        value: externalValue,
                                        defaultValue,
                                        disabled = false,
                                        ...props
                                    }: InputSearchProps) {
    const [inputValue, setInputValue] = useState<string>(
        (externalValue as string) || (defaultValue as string) || ""
    );

    const userChangedValue = useRef(false);
    const isFirstRender = useRef(true);
    const prevDebouncedValue = useRef<string>("");

    useEffect(() => {
        if (externalValue !== undefined) {
            setInputValue(externalValue as string);
        }
    }, [externalValue]);

    const [debouncedValue] = useDebounce(inputValue, debounceDelay);

    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            prevDebouncedValue.current = debouncedValue;
            return;
        }

        if (
            userChangedValue.current &&
            onChangeDebounced &&
            debouncedValue !== undefined &&
            debouncedValue !== prevDebouncedValue.current
        ) {
            prevDebouncedValue.current = debouncedValue;
            onChangeDebounced(debouncedValue);
        }
    }, [debouncedValue, onChangeDebounced]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const newValue = e.target.value;
        userChangedValue.current = true;

        if (externalValue === undefined) {
            setInputValue(newValue);
        }

        onChange?.(e);
    };

    const handleClear = () => {
        userChangedValue.current = true;
        setInputValue("");
        onChange?.("");
        onChangeDebounced?.("");
    };

    const currentValue = externalValue !== undefined ? externalValue : inputValue;

    return (
        <div className="*:not-first:mt-2">
            <div className="relative">
                <Input
                    className="peer ps-9 pe-9 py-4"
                    placeholder={placeholder}
                    type="search"
                    disabled={disabled}
                    value={currentValue}
                    onChange={handleChange}
                    {...props}
                />
                <div className="text-muted-foreground/80 pointer-events-none absolute inset-y-0 start-0 flex items-center justify-center ps-3 peer-disabled:opacity-50">
                    <Icon size={iconSize} aria-hidden="true" />
                </div>

                {currentValue && (
                    <button
                        type="button"
                        onClick={handleClear}
                        className="text-muted-foreground/80 hover:text-foreground absolute inset-y-0 end-0 flex w-9 items-center justify-center rounded-e-md transition-colors"
                    >
                        <XIcon size={16} />
                    </button>
                )}
            </div>
        </div>
    );
}
