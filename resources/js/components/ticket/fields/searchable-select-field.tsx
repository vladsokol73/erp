import React, { useMemo, useState, useRef, useEffect, KeyboardEvent } from "react";
import { Button } from "@/components/ui/button";
import { ChevronDownIcon, CheckIcon } from "lucide-react";
import { Popover, PopoverTrigger, PopoverContent } from "@/components/ui/popover-dialog";
import { cn } from "@/lib/utils";
import { useVirtualizer } from "@tanstack/react-virtual";

interface Option {
    id: number;
    label: string;
}

export interface SearchableSelectFieldProps {
    label?: string;
    value?: number | null;
    onChange: (val: number | null) => void;
    placeholder?: string;
    options: { id: number; label: string }[];
    error?: string;
}
export const SearchableSelectField: React.FC<SearchableSelectFieldProps> = ({
                                                                                label,
                                                                                value,
                                                                                onChange,
                                                                                placeholder = "Select option...",
                                                                                options,
                                                                                error
                                                                            }) => {
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState("");

    const selectedOption = useMemo(() => options.find(opt => opt.id === value), [value, options]);
    const filtered = useMemo(() => {
        const s = search.toLowerCase().trim();
        return s ? options.filter(o => o.label.toLowerCase().includes(s)) : options;
    }, [options, search]);

    const parentRef = useRef<HTMLDivElement>(null);
    const virtualizer = useVirtualizer({
        count: filtered.length,
        getScrollElement: () => parentRef.current,
        estimateSize: () => 36,
    });

    useEffect(() => {
        if (open) {
            setTimeout(() => virtualizer.measure(), 0);
        }
    }, [open, filtered.length]);

    const handleSelect = (id: number) => {
        onChange(id === value ? null : id);
        setOpen(false);
    };

    return (
        <div className="flex flex-col w-full gap-2">
            {label && <label className="text-sm font-medium">{label}</label>}

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
                        {selectedOption ? selectedOption.label : <span className="text-muted-foreground">{placeholder}</span>}
                        <ChevronDownIcon size={16} />
                    </Button>
                </PopoverTrigger>

                <PopoverContent
                    className="z-50 w-full min-w-[var(--radix-popper-anchor-width)] p-0"
                    align="start"
                >
                    <div className="border-b px-3 py-2">
                        <input
                            className="w-full bg-transparent outline-none text-sm"
                            placeholder="Search..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e: KeyboardEvent) => e.key === "Escape" && setOpen(false)}
                        />
                    </div>

                    {filtered.length === 0 ? (
                        <div className="p-4 text-center text-sm text-muted-foreground">Nothing found</div>
                    ) : (
                        <div ref={parentRef} style={{ height: 200, overflowY: "auto" }}>
                            <div style={{ height: virtualizer.getTotalSize(), position: "relative" }}>
                                {virtualizer.getVirtualItems().map((v) => {
                                    const item = filtered[v.index];
                                    return (
                                        <button
                                            key={item.id}
                                            style={{
                                                position: "absolute",
                                                top: 0,
                                                left: 0,
                                                width: "100%",
                                                transform: `translateY(${v.start}px)`
                                            }}
                                            className={cn(
                                                "text-left px-3 py-2 text-sm w-full hover:bg-accent/10 flex items-center justify-between",
                                                item.id === value && "bg-accent/20"
                                            )}
                                            onClick={() => handleSelect(item.id)}
                                            ref={virtualizer.measureElement}
                                            data-index={v.index}
                                        >
                                            <span>{item.label}</span>
                                            {item.id === value && <CheckIcon size={16} className="text-primary" />}
                                        </button>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </PopoverContent>
            </Popover>
            {error && <p className="text-xs text-destructive">{error}</p>}
        </div>
    );
};
