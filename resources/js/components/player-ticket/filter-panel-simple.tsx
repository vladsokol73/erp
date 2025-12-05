"use client";

import React from "react";
import { ChevronUp, ChevronDown, Funnel } from "lucide-react";
import { IconToggle } from "@/components/ui/icon-toggle";
import MultiSelect from "@/components/ui/multi-select";
import { DateRangeField } from "@/components/ticket/fields/date-range-field";
import { SelectField } from "@/components/ticket/fields/select-field";

interface FilterPanelProps {
    isOpen: boolean;
    onToggle: (value: boolean) => void;
    filters: any;
    setFilters: (filters: any) => void;
    statuses: App.DTO.Ticket.PlayerTicketStatusDto[];
    operators?: App.DTO.User.UserOperatorDto[];
    toUtcDateString: (date?: Date) => string | undefined;
}

const FilterPanelSimple = ({
                               isOpen,
                               onToggle,
                               filters,
                               setFilters,
                               statuses,
                               operators = [],
                               toUtcDateString,
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
                    <Funnel size={16} /> Filters
                </span>
            </div>

            {isOpen && (
                <div className="grid md:grid-cols-4 gap-4 mt-3">
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
                            });
                        }}
                    />

                    <MultiSelect
                        maxSelected={6}
                        label="Select statuses"
                        placeholder="Select statuses"
                        options={statuses.map((status) => ({
                            value: status.name,
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
                        maxSelected={10}
                        label="Select operators"
                        placeholder="Select operators"
                        options={(operators ?? [])
                            .filter((op) => op.operator_id !== null)
                            .map((op) => ({
                                value: String(op.operator_id),
                                label: op.name,
                            }))}
                        value={filters.filter.operators}
                        onChange={(values: string[]) => {
                            setFilters({
                                filter: {
                                    ...filters.filter,
                                    operators: values,
                                },
                            });
                        }}
                    />

                    <SelectField
                        label="Select type"
                        placeholder="Select type"
                        options={["fd", "rd"]}
                        value={filters.filter.type ?? ""}
                        onChange={(val: string) => {
                            setFilters({
                                filter: {
                                    ...filters.filter,
                                    type: val,
                                },
                            });
                        }}
                    />
                </div>
            )}
        </>
    );
};

export default FilterPanelSimple;
