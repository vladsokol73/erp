import React, {useId, useState} from "react";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
    SelectGroup,
    SelectSeparator
} from "@/components/ui/select";
import { useTimezoneStore } from '@/store/useTimezoneStore';
import {Button} from "@/components/ui/button";
import {RotateCcw, TimerReset} from "lucide-react";

interface Timezone {
    value: string;
    label: string;
}

interface TimezoneSelectorProps {
    onTimezoneChange?: (value: string) => void;
}

// Список таймзон со смещениями в минутах
const timezones: Timezone[] = [
    { value: "720", label: "UTC-12" },
    { value: "660", label: "UTC-11" },
    { value: "600", label: "UTC-10" },
    { value: "540", label: "UTC-9" },
    { value: "480", label: "UTC-8" },
    { value: "420", label: "UTC-7" },
    { value: "360", label: "UTC-6" },
    { value: "300", label: "UTC-5" },
    { value: "270", label: "UTC-4:30" },
    { value: "240", label: "UTC-4" },
    { value: "210", label: "UTC-3:30" },
    { value: "180", label: "UTC-3" },
    { value: "120", label: "UTC-2" },
    { value: "60", label: "UTC-1" },
    { value: "0", label: "UTC" },
    { value: "-60", label: "UTC+1" },
    { value: "-120", label: "UTC+2" },
    { value: "-180", label: "UTC+3" },
    { value: "-210", label: "UTC+3:30" },
    { value: "-240", label: "UTC+4" },
    { value: "-270", label: "UTC+4:30" },
    { value: "-300", label: "UTC+5" },
    { value: "-330", label: "UTC+5:30" },
    { value: "-345", label: "UTC+5:45" },
    { value: "-360", label: "UTC+6" },
    { value: "-390", label: "UTC+6:30" },
    { value: "-420", label: "UTC+7" },
    { value: "-480", label: "UTC+8" },
    { value: "-540", label: "UTC+9" },
    { value: "-570", label: "UTC+9:30" },
    { value: "-600", label: "UTC+10" },
    { value: "-660", label: "UTC+11" },
    { value: "-720", label: "UTC+12" },
    { value: "-780", label: "UTC+13" },
    { value: "-840", label: "UTC+14" },
];

export default function TimezoneSelector({
                                             onTimezoneChange,
                                         }: TimezoneSelectorProps) {
    const { timezoneOffset, setTimezoneOffset, resetToSystemTimezone } = useTimezoneStore();
    const [ selectedTimezone, setSelectedTimezone ] = useState(timezoneOffset.toString());

    const id = useId();

    // Обработчик изменения значения
    const handleValueChange = (value: string) => {
        if (onTimezoneChange) {
            onTimezoneChange(value);
        }
        setSelectedTimezone(value);
        setTimezoneOffset(parseInt(value));
    };

    const handleReset = () => {
        const userTimezoneOffset = new Date().getTimezoneOffset()
        setTimezoneOffset(userTimezoneOffset)
        setSelectedTimezone(userTimezoneOffset.toString());
    };

    return (
        <div className="*:not-first:mt-2">
            <Select value={selectedTimezone} defaultValue={timezoneOffset.toString()} onValueChange={handleValueChange}>
                <SelectTrigger className="w-32" id={id}>
                    <SelectValue placeholder="Select timezone" />
                </SelectTrigger>
                <SelectContent className="w-32">
                    <SelectGroup className="max-h-40 overflow-y-auto">
                        {timezones.map(({ value, label }) => (
                            <SelectItem key={value} value={value}>
                                {label}
                            </SelectItem>
                        ))}
                    </SelectGroup>
                    <SelectSeparator />
                    <SelectGroup>
                        <Button
                            className="w-full text-destructive hover:text-destructive-foreground"
                            variant="ghost"
                            size="sm"
                            onClick={handleReset}
                        >
                            <RotateCcw/>
                            Reset
                        </Button>
                    </SelectGroup>
                </SelectContent>
            </Select>
        </div>
    );
}
