import * as React from "react"
import { ChevronDownIcon } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Calendar } from "@/components/ui/calendar"
import { Label } from "@/components/ui/label"
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover-dialog"
import {cn} from "@/lib/utils";

export interface DateFieldProps {
    label?: string;
    value?: Date;
    onChange?: (date: Date) => void;
    error?: string;
}

export const DateField: React.FC<DateFieldProps> = ({
                                                        label,
                                                        value,
                                                        onChange,
                                                        error,
                                                    }) => {
    const [open, setOpen] = React.useState(false)
    const [selectedDate, setSelectedDate] = React.useState<Date | undefined>(value)

    const handleDateChange = (date: Date) => {
        setSelectedDate(date)
        if (onChange) {
            onChange(date)
        }
        setOpen(false)
    }

    return (
        <div>
            {label && (
                <Label htmlFor="date" className="py-2">
                    {label}
                </Label>
            )}
            <Popover open={open} onOpenChange={setOpen}>
                <PopoverTrigger asChild>
                    <Button
                        variant="outline"
                        role="combobox"
                        aria-expanded={open}
                        className={cn(
                            "w-full min-h-[38px] h-auto justify-between flex-wrap gap-1 px-2 py-1 text-left select-none",
                            !selectedDate && "text-muted-foreground"
                        )}
                    >
                        {selectedDate ? selectedDate.toLocaleDateString() : "Select date"}
                        <ChevronDownIcon />
                    </Button>
                </PopoverTrigger>
                <PopoverContent className="w-auto overflow-hidden p-0" align="start">
                    <Calendar
                        mode="single"
                        selected={selectedDate}
                        captionLayout="dropdown"
                        onSelect={handleDateChange}
                        required={true}
                    />

                </PopoverContent>
            </Popover>
            {error && <div className="text-xs text-destructive mt-1">{error}</div>}
        </div>
    )
}
