import React from "react";
import { cn } from "@/lib/utils";
import { Badge } from "@/components/ui/badge";
import {Dot, LoaderCircle, TicketX} from "lucide-react";
import DateFormatter from "@/components/common/date-formatter";
import TicketSearchInput from "@/components/ticket/my/ticket-search-input";
import TicketSortTabs from "@/components/ticket/my/ticket-sort-tabs";
import {Button} from "@/components/ui/button";
import SortDropdown from "@/components/ticket/sort-dropdown";

interface TicketListProps {
    tickets: App.DTO.Ticket.TicketListDto[];
    selectedTicket: App.DTO.Ticket.TicketListDto | App.DTO.Ticket.TicketListAllDto | null;
    setSelectedTicket: (ticket: App.DTO.Ticket.TicketListDto | App.DTO.Ticket.TicketListAllDto | null) => void;
    filters: any;
    sortTabs?: { label: React.ReactNode; value: string }[];
    handleSortChange: (value: string) => void;
    handleTabChange: (value: string) => void;
    handleSearchSubmit: (search: string) => void;

    loadingMore: boolean;
    hasMore: boolean;
    handleLoadMore: () => void;
}

const TicketList = ({
                        tickets,
                        selectedTicket,
                        setSelectedTicket,
                        filters,
                        sortTabs,
                        handleTabChange,
                        handleSortChange,
                        handleSearchSubmit,

                        loadingMore,
                        hasMore,
                        handleLoadMore
                    }: TicketListProps) => {
    return (
        <div className={cn(
            'col-span-12 lg:col-span-5 2xl:col-span-4 flex flex-col gap-4 px-6 min-h-[900px]',
            selectedTicket && 'hidden lg:flex'
        )}>
            <div className="flex items-center gap-2 justify-between">
                <span className="w-full">
                    <TicketSearchInput
                        defaultValue={filters.search}
                        onSubmit={handleSearchSubmit}
                    />
                </span>

                <SortDropdown
                    initialSortType={filters.sort}
                    onSortChange={handleSortChange}
                />
            </div>

            {sortTabs && (
                <TicketSortTabs
                    value={filters.filter.type}
                    onChange={handleTabChange}
                    tabs={sortTabs}
                />
            )}

            <div className="flex flex-col gap-2 overflow-y-auto">
                {tickets.length === 0 ? (
                    <div className="flex flex-col items-center gap-2 text-sm text-center text-muted-foreground py-10">
                        <TicketX className="size-8" />
                        No tickets found
                    </div>
                ) : (
                    tickets.map((item, index) => (
                        <div
                            key={index}
                            className={cn(
                                "flex justify-between items-center gap-2 py-3 px-4 hover:bg-muted-foreground/10 rounded-lg cursor-pointer",
                                {
                                    "bg-muted-foreground/10": selectedTicket?.id === item.id,
                                }
                            )}
                            onClick={() => setSelectedTicket(item)}
                        >
                            <div className="flex flex-col gap-2">
                                <div className="flex flex-col gap-2">
                                    <Badge
                                        variant="outline"
                                        className={`px-1.5 py-0.5 text-xs border-${item.status.color} text-${item.status.color}`}
                                    >
                                        {item.status.name}
                                    </Badge>
                                    <div className="flex items-center gap-2">
                                        <Dot
                                            className={cn(
                                                '-m-6',
                                                item.priority === "low" && "text-green-500",
                                                item.priority === "middle" && "text-yellow-500",
                                                item.priority === "high" && "text-red-500"
                                            )}
                                            size={64}
                                        />
                                        <h4 className="text-sm font-medium">{item.ticket_number}</h4>
                                    </div>
                                </div>
                                <p className="text-sm text-muted-foreground">{item.topic.name}</p>
                            </div>
                            <span className="text-xs text-muted-foreground">
                                <DateFormatter
                                    variant="relative"
                                    className="text-xs text-muted-foreground"
                                    dateString={item.created_at}
                                />
                            </span>
                        </div>
                    ))
                )}
            </div>

            {hasMore && (
                <div className="flex justify-center mt-4">
                    <Button
                        variant="link"
                        size="sm"
                        onClick={handleLoadMore}
                        className={cn(
                            loadingMore
                                && "cursor-not-allowed text-muted-foreground pointer-events-none"
                        )}
                    >
                        {loadingMore && <LoaderCircle className="animate-spin" />}
                        Load more
                    </Button>
                </div>
            )}

        </div>
    );
};

export default TicketList;
