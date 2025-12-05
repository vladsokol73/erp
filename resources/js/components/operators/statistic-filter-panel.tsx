import { useState } from "react";
import { ChevronDown, ChevronUp, Funnel } from "lucide-react";
import { IconToggle } from "@/components/ui/icon-toggle";
import MultiSelect from "@/components/ui/multi-select";
import { DateRangeField } from "@/components/ticket/fields/date-range-field";
import { Card, CardHeader, CardContent } from "@/components/ui/card";

interface Props {
    isOpen?: boolean;
    onToggle?: (value: boolean) => void;
    filters: any;
    setFilters: (filters: any) => void;
    operators: App.DTO.OperatorDto[];
    channels: App.DTO.ChannelDto[];
    toUtcDateString: (date?: Date) => string | undefined;

    totalClients: number;
    totalNewClients: number;
}

export default function StatisticFilterPanel({
                                                 isOpen = false,
                                                 onToggle,
                                                 filters,
                                                 setFilters,
                                                 operators,
                                                 channels,
                                                 toUtcDateString,
                                                 totalClients,
                                                 totalNewClients,
                                             }: Props) {
    const [filterOpen, setFilterOpen] = useState(isOpen);

    const handleToggle = (open: boolean) => {
        setFilterOpen(open);
        onToggle?.(open);
    };

    return (
        <Card>
            <CardHeader className={!filterOpen ? "gap-0" : ""}>
                <div className="flex gap-2">
                    <IconToggle
                        onIcon={<ChevronUp />}
                        offIcon={<ChevronDown />}
                        onToggleChange={handleToggle}
                        aria-label="Toggle filter panel"
                    />
                    <span className="flex items-center gap-2 font-semibold">
                        <Funnel size={16} /> Filters
                    </span>
                </div>
            </CardHeader>
            {filterOpen && (
                <CardContent>
                       {/* Поля фильтрации */}
                    <div className="grid items-center md:grid-cols-4 gap-4">
                        {/* Виджеты статистики */}
                        <div className="grid md:grid-cols-2 gap-4">
                            <div className="flex flex-col bg-input rounded-md p-4">
                                <span className="text-xs text-muted-foreground">Total Clients</span>
                                <span className="text-lg font-bold">{totalClients}</span>
                            </div>
                            <div className="flex flex-col bg-input rounded-md p-4">
                                <span className="text-xs text-muted-foreground">Total New Clients</span>
                                <span className="text-lg font-bold">{totalNewClients}</span>
                            </div>
                        </div>


                        <DateRangeField
                            label="Select date"
                            value={{
                                from: filters.filter.date.from
                                    ? new Date(filters.filter.date.from + "T00:00:00Z")
                                    : undefined,
                                to: filters.filter.date.to
                                    ? new Date(filters.filter.date.to + "T00:00:00Z")
                                    : undefined,
                            }}
                            onChange={(range) => {
                                setFilters({
                                    filter: {
                                        ...filters.filter,
                                        date: {
                                            from: toUtcDateString(range?.from),
                                            to: toUtcDateString(range?.to),
                                        },
                                    },
                                    page: 1,
                                });
                            }}
                        />

                        <MultiSelect
                            maxSelected={6}
                            label="Select operators"
                            placeholder="Operators"
                            options={operators.map((operator) => ({
                                value: operator.id.toString(),
                                label: operator.name ?? "Unnamed :" + operator.id,
                            }))}
                            value={filters.filter.operators}
                            onChange={(values: string[]) => {
                                setFilters({
                                    filter: {
                                        ...filters.filter,
                                        operators: values,
                                    },
                                    page: 1,
                                });
                            }}
                        />

                        <MultiSelect
                            maxSelected={6}
                            label="Select channels"
                            placeholder="Channels"
                            options={channels.map((channel) => ({
                                value: channel.id.toString(),
                                label: channel.name ?? "Unnamed: " + channel.id,
                            }))}
                            value={filters.filter.channels}
                            onChange={(values: string[]) => {
                                setFilters({
                                    filter: {
                                        ...filters.filter,
                                        channels: values,
                                    },
                                    page: 1,
                                });
                            }}
                        />
                    </div>
                </CardContent>
            )}
        </Card>
    );
}
