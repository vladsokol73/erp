"use client"

import * as React from "react"
import { CheckIcon, XIcon, ChevronDown } from "lucide-react"
import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from "@/components/ui/command"
import { Label } from "@/components/ui/label"
import {
    Popover,
    PopoverTrigger,
    PopoverContent,
} from "@/components/ui/popover-dialog"

export interface Option {
    value: string
    label: string
}

export interface MultiSelectProps {
    label?: string
    placeholder?: string
    options: Option[]
    maxSelected?: number
    emptyMessage?: string
    commandLabel?: string
    value?: string[]
    onChange?: (values: string[]) => void
    error?: string
}

export default function MultiSelect({
                                        label,
                                        placeholder = "Select items",
                                        options = [],
                                        emptyMessage = "No results found",
                                        maxSelected = 1000,
                                        commandLabel,
                                        value = [],
                                        onChange,
                                        error,
                                    }: MultiSelectProps) {
    const [open, setOpen] = React.useState(false)
    const triggerRef = React.useRef<HTMLButtonElement>(null)
    const [popoverWidth, setPopoverWidth] = React.useState<number | undefined>(undefined)

    React.useLayoutEffect(() => {
        if (triggerRef.current) {
            setPopoverWidth(triggerRef.current.offsetWidth)
        }
    }, [open, value])

    const handleToggle = (val: string) => {
        if (value.includes(val)) {
            onChange?.(value.filter((v) => v !== val))
        } else if (value.length < maxSelected) {
            onChange?.([...value, val])
        }
    }

    const handleClear = () => {
        onChange?.([])
    }

    return (
        <div className="flex flex-col gap-2">
            {label && <Label>{label}</Label>}

            <Popover modal open={open} onOpenChange={setOpen}>
                <PopoverTrigger asChild>
                    <Button
                        ref={triggerRef}
                        variant="outline"
                        role="combobox"
                        aria-expanded={open}
                        className={cn(
                            "w-full !h-auto !min-h-[38px] justify-start items-center flex-wrap gap-1 px-3 !py-1 text-left select-none",
                            !value.length && "text-muted-foreground",
                            error && "border-destructive focus:ring-destructive"
                        )}
                    >
                        <div className="flex w-full justify-between items-center gap-2">
                            <div className="flex flex-wrap gap-1 flex-grow">
                                {value.length === 0 ? (
                                    <span className="text-sm text-muted-foreground truncate">{placeholder}</span>
                                ) : (
                                    value.map((val) => {
                                        const option = options.find((o) => o.value === val)
                                        return (
                                            <Badge
                                                key={val}
                                                variant="outline"
                                                className="text-xs relative pr-6 max-w-[180px] pl-2 truncate"
                                            >
                                                <span className="truncate block max-w-full">
                                                  {option?.label || val}
                                                </span>
                                                <span
                                                    role="button"
                                                    onClick={(e) => {
                                                        e.stopPropagation()
                                                        handleToggle(val)
                                                    }}
                                                    className="absolute right-0 top-0 flex h-full w-5 items-center justify-center text-muted-foreground hover:text-foreground cursor-pointer"
                                                >
                                              <XIcon size={10} />
                                            </span>
                                            </Badge>
                                        )
                                    })
                                )}
                            </div>

                            <div className="flex items-center gap-1 shrink-0">
                                {value.length > 0 && (
                                    <span
                                        role="button"
                                        onClick={(e) => {
                                            e.stopPropagation()
                                            handleClear()
                                        }}
                                        className="text-muted-foreground hover:text-foreground cursor-pointer"
                                    >
                                    <XIcon size={16} />
                                  </span>
                                )}
                                <ChevronDown
                                    size={16}
                                    className="text-muted-foreground group-hover:text-foreground shrink-0"
                                />
                            </div>
                        </div>
                    </Button>
                </PopoverTrigger>

                <PopoverContent
                    className="z-50 p-0"
                    style={{
                        width: popoverWidth,
                        minWidth: popoverWidth,
                        maxWidth: popoverWidth,
                    }}
                >
                    <Command>
                        {commandLabel && (
                            <div className="px-3 pt-3 text-sm font-medium">{commandLabel}</div>
                        )}
                        <CommandInput placeholder="Search..." />
                        <CommandList>
                            <CommandEmpty>{emptyMessage}</CommandEmpty>
                            <CommandGroup>
                                {options.map((option) => (
                                    <CommandItem
                                        key={option.value}
                                        keywords={[option.value]}
                                        onSelect={() => handleToggle(option.value)}
                                        disabled={
                                            !value.includes(option.value) && value.length >= maxSelected
                                        }
                                        className="truncate"
                                    >
                                        <span className="truncate max-w-full">{option.label}</span>
                                        {value.includes(option.value) && (
                                            <CheckIcon className="ml-auto h-4 w-4 shrink-0" />
                                        )}
                                    </CommandItem>
                                ))}
                            </CommandGroup>
                        </CommandList>
                    </Command>
                </PopoverContent>
            </Popover>

            {error && <p className="text-sm text-destructive mt-1">{error}</p>}
        </div>
    )
}
