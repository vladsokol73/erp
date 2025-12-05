import React from "react";
import { ChevronUp, ChevronDown, Funnel } from "lucide-react";
import { IconToggle } from "@/components/ui/icon-toggle";
import MultiSelect from "@/components/ui/multi-select";
import { DateRangeField } from "@/components/ticket/fields/date-range-field";

interface FilterPanelProps {
    isOpen: boolean;
    onToggle: (value: boolean) => void;
    filters: any;
    setFilters: (filters: any) => void;
    statuses: App.DTO.Ticket.TicketStatusDto[];
    topics: App.DTO.Ticket.TicketTopicDto[];
    categories: App.DTO.Ticket.TicketCategoryDto[];
    toUtcDateString: (date?: Date) => string | undefined;
}

const FilterPanel = ({
                         isOpen,
                         onToggle,
                         filters,
                         setFilters,
                         statuses,
                         topics,
                         categories,
                         toUtcDateString
                     }: FilterPanelProps) => {
    return (
        <>
            <div className="flex gap-2">
                <IconToggle
                    onIcon={<ChevronUp />}
                    offIcon={<ChevronDown />}
                    onToggleChange={onToggle}
                    aria-label="Toggle filter panel"
                />
                <span className="flex items-center gap-2 font-semibold">
                    <Funnel size={16}/> Filters
                </span>
            </div>
            {isOpen && (
                <div className="grid md:grid-cols-4 gap-4">
                    <DateRangeField
                        label="Select date"
                        value={{
                            from: filters.filter.date.from
                                ? new Date(filters.filter.date.from + 'T00:00:00Z')
                                : undefined,
                            to: filters.filter.date.to
                                ? new Date(filters.filter.date.to + 'T00:00:00Z')
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
                            });
                        }}
                    />

                    <MultiSelect
                        maxSelected={6}
                        label="Select statuses"
                        placeholder="Select statuses"
                        options={statuses.map((status) => ({
                            value: status.id.toString(),
                            label: status.name,
                        }))}
                        value={filters.filter.statuses}
                        onChange={(values: string[]) => {
                            setFilters({
                                filter: {
                                    ...filters.filter,
                                    statuses: values as never[],
                                },
                            });
                        }}
                    />

                    <MultiSelect
                        maxSelected={6}
                        label="Select topics"
                        placeholder="Select topics"
                        options={topics.map((topic) => ({
                            value: topic.id.toString(),
                            label: topic.name,
                        }))}
                        value={filters.filter.topics}
                        onChange={(values: string[]) => {
                            setFilters({
                                filter: {
                                    ...filters.filter,
                                    topics: values as never[],
                                },
                            });
                        }}
                    />

                    <MultiSelect
                        maxSelected={6}
                        label="Select categories"
                        placeholder="Select categories"
                        options={categories.map((category) => ({
                            value: category.id.toString(),
                            label: category.name,
                        }))}
                        value={filters.filter.categories}
                        onChange={(values: string[]) => {
                            setFilters({
                                filter: {
                                    ...filters.filter,
                                    categories: values as never[],
                                },
                            });
                        }}
                    />
                </div>
            )}
        </>
    );
};

export default FilterPanel;
