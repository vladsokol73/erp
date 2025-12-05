import * as React from "react"
import { ChevronDownIcon, X } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Calendar } from "@/components/ui/calendar"
import { Label } from "@/components/ui/label"
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover-dialog"
import { cn } from "@/lib/utils"
import { DateRange } from "react-day-picker"

export interface DateRangeFieldProps {
    label?: string
    value?: DateRange
    onChange?: (range: DateRange | undefined) => void
    error?: string
}

export const DateRangeField: React.FC<DateRangeFieldProps> = ({
                                                                  label,
                                                                  value,
                                                                  onChange,
                                                                  error,
                                                              }) => {
    const [open, setOpen] = React.useState(false)
    const [tempRange, setTempRange] = React.useState<DateRange | undefined>(value)

    React.useEffect(() => {
        setTempRange(value)
    }, [value])

    const formatDateRange = (range: DateRange | undefined) => {
        if (!range?.from) return "Select date range"
        const from = range.from.toLocaleDateString()
        const to = range.to ? range.to.toLocaleDateString() : "..."
        return `${from} - ${to}`
    }

    const handleApply = () => {
        onChange?.(tempRange)
        setOpen(false)
    }

    const handleClear = () => {
        setTempRange(undefined)
        onChange?.(undefined)
        setOpen(false)
    }

    return (
        <div className="flex flex-col gap-2">
            {label && (
                <Label htmlFor="date" className="block">
                    {label}
                </Label>
            )}
            <div className="relative">
                <Popover open={open} onOpenChange={setOpen}>
                    <PopoverTrigger asChild>
                        <Button
                            variant="outline"
                            role="combobox"
                            aria-expanded={open}
                            className={cn(
                                "w-full min-h-[38px] h-auto justify-between flex-wrap gap-1 px-2 py-1 pr-8 text-left select-none",
                                !value?.from && "text-muted-foreground"
                            )}
                        >
                            <span className="flex-grow">{formatDateRange(value)}</span>
                            {!value?.from && (
                                <ChevronDownIcon className="w-4 h-4 shrink-0 opacity-70 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" />
                            )}
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto overflow-hidden p-2" align="start">
                        <Calendar
                            mode="range"
                            numberOfMonths={2}
                            defaultMonth={new Date(new Date().getFullYear(), new Date().getMonth() - 1)}
                            selected={tempRange}
                            showOutsideDays={false}
                            onSelect={setTempRange}
                            disabled={{ after: new Date() }}
                            initialFocus
                        />
                        <div className="flex justify-end gap-2 p-2">
                            <Button variant="ghost" size="sm" onClick={handleClear}>
                                Clear
                            </Button>
                            <Button
                                variant="default"
                                size="sm"
                                onClick={handleApply}
                                disabled={!tempRange?.from}
                            >
                                Apply
                            </Button>
                        </div>
                    </PopoverContent>
                </Popover>

                {value?.from && (
                    <X
                        className="absolute right-2 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground hover:text-foreground cursor-pointer z-10"
                        onClick={(e) => {
                            e.stopPropagation()
                            e.preventDefault()
                            handleClear()
                        }}
                    />
                )}
            </div>
            {error && <div className="text-xs text-destructive mt-1">{error}</div>}
        </div>
    )
}
