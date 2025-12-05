import React, {
    useMemo,
    useRef,
    useState,
    useEffect,
    KeyboardEvent,
} from "react";
import { Button } from "@/components/ui/button";
import { ChevronDownIcon, CheckIcon } from "lucide-react";
import {
    Popover,
    PopoverTrigger,
    PopoverContent,
} from "@/components/ui/popover-dialog";
import { cn } from "@/lib/utils";
import { useVirtualizer } from "@tanstack/react-virtual";

export interface CountryFieldProps {
    label?: string;
    value?: number; // выбранный ID страны
    onChange?: (val: number) => void;
    placeholder?: string;
    options?: App.DTO.CountryDto[];
    error?: string;
}

export const CountryField: React.FC<CountryFieldProps> = ({
                                                              label,
                                                              value,
                                                              onChange,
                                                              placeholder,
                                                              options,
                                                              error,
                                                          }) => {
    const [open, setOpen] = useState(false);
    const [internalValue, setInternalValue] = useState<number | null>(null);
    const [search, setSearch] = useState("");

    const selectedId = value ?? internalValue;

    const defaultOptions = useMemo(
        () =>
            [
                "United States",
                "Canada",
                "United Kingdom",
                "Germany",
                "France",
                "Japan",
                "South Korea",
                "Australia",
                "Brazil",
                "Russia",
            ].map((country, index) => ({
                id: index + 1,
                label: country,
                value: country.toLowerCase().replace(/\s+/g, "_"),
                img: null as string | null,
            })),
        []
    );

    const mappedOptions = useMemo(() => {
        if (options?.length) {
            return options.map((c) => ({
                id: c.id,
                label: c.name,
                value: c.iso?.toLowerCase() || `country_${c.id}`,
                img: c.img,
            }));
        }
        return defaultOptions;
    }, [options, defaultOptions]);

    const filteredOptions = useMemo(() => {
        const term = search.trim().toLowerCase();
        if (!term) return mappedOptions;
        return mappedOptions.filter((opt) =>
            opt.label.toLowerCase().includes(term)
        );
    }, [mappedOptions, search]);

    const selectedOption = mappedOptions.find((o) => o.id === Number(selectedId));

    const parentRef = useRef<HTMLDivElement>(null);

    const virtualizer = useVirtualizer({
        count: filteredOptions.length,
        getScrollElement: () => parentRef.current,
        estimateSize: () => 44,
    });

    useEffect(() => {
        if (open) {
            setTimeout(() => {
                virtualizer.measure();
            }, 0);
        }
    }, [open, filteredOptions.length, search]);

    const handleSelect = (id: number) => {
        if (onChange) {
            onChange(id === selectedId ? 0 : id);
        } else {
            setInternalValue(id === selectedId ? null : id);
        }
        setOpen(false);
    };

    const onKeyDownInput = (e: KeyboardEvent<HTMLInputElement>) => {
        if (e.key === "Escape") {
            setOpen(false);
            e.stopPropagation();
        }
    };

    return (
        <div className="flex flex-col gap-2">
            {label && (
                <label className="text-sm font-medium" htmlFor="country-field">
                    {label}
                </label>
            )}
            <Popover open={open} onOpenChange={setOpen}>
                <PopoverTrigger asChild>
                    <Button
                        variant="outline"
                        role="combobox"
                        className={cn(
                            "bg-background border-input w-full justify-between px-3 font-normal h-9 inline-flex items-center rounded-md border text-sm shadow-sm",
                            error && "border-destructive focus:ring-destructive"
                        )}
                    >
                        <span
                            className={cn(
                                "flex items-center gap-2 truncate",
                                !selectedOption && "text-muted-foreground"
                            )}
                        >
                            {selectedOption ? (
                                <>
                                    {selectedOption.img && (
                                        <img
                                            src={selectedOption.img}
                                            alt={selectedOption.label}
                                            className="h-4 w-4 rounded-full object-contain"
                                            loading="lazy"
                                        />
                                    )}
                                    <span>{selectedOption.label}</span>
                                </>
                            ) : (
                                placeholder ?? "Select country"
                            )}
                        </span>
                        <ChevronDownIcon
                            size={16}
                            className="text-muted-foreground/80 ml-auto"
                        />
                    </Button>
                </PopoverTrigger>

                <PopoverContent
                    className="z-50 w-full min-w-[var(--radix-popper-anchor-width)] p-0"
                    align="start"
                >
                    <div className="border-b px-3 py-2">
                        <input
                            type="text"
                            autoFocus
                            className="w-full bg-transparent outline-none text-sm"
                            placeholder="Search country..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={onKeyDownInput}
                        />
                    </div>

                    {filteredOptions.length === 0 ? (
                        <div className="p-4 text-center text-sm text-muted-foreground">
                            No country found.
                        </div>
                    ) : (
                        <div
                            ref={parentRef}
                            style={{ height: 300, overflowY: "auto" }}
                        >
                            <div
                                style={{
                                    height: virtualizer.getTotalSize(),
                                    position: "relative",
                                }}
                            >
                                {virtualizer.getVirtualItems().map((virtualRow) => {
                                    const option = filteredOptions[virtualRow.index];
                                    return (
                                        <div
                                            key={option.id}
                                            style={{
                                                position: "absolute",
                                                top: 0,
                                                left: 0,
                                                width: "100%",
                                                transform: `translateY(${virtualRow.start}px)`,
                                            }}
                                            className="flex flex-col gap-2"
                                        >
                                            <button
                                                onClick={() => handleSelect(option.id)}
                                                className={cn(
                                                    "w-full text-left px-3 py-2 flex rounded-lg items-center justify-between hover:bg-accent/10",
                                                    selectedId === option.id
                                                        ? "bg-accent/20"
                                                        : "bg-transparent"
                                                )}
                                                ref={virtualizer.measureElement}
                                                data-index={virtualRow.index}
                                                type="button"
                                            >
                                                <div className="flex items-center gap-2">
                                                    {option.img && (
                                                        <img
                                                            src={option.img}
                                                            alt={option.label}
                                                            className="h-5 w-5 object-contain rounded-full"
                                                            loading="lazy"
                                                        />
                                                    )}
                                                    <span className="text-sm">{option.label}</span>
                                                </div>
                                                {selectedId === option.id && (
                                                    <CheckIcon size={16} className="text-primary" />
                                                )}
                                            </button>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </PopoverContent>
            </Popover>
            {error && (
                <p className="text-sm text-destructive mt-1">
                    {error}
                </p>
            )}
        </div>
    );
};
