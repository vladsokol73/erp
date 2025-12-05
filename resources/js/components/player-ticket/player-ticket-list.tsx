"use client";

import React from "react";
import { cn } from "@/lib/utils";
import { Badge } from "@/components/ui/badge";
import { TicketX, LoaderCircle } from "lucide-react";
import DateFormatter from "@/components/common/date-formatter";
import TicketSearchInput from "@/components/ticket/my/ticket-search-input";
import SortDropdown from "@/components/player-ticket/sort-dropdown";
import { Button } from "@/components/ui/button";

interface PlayerTicketListProps {
    tickets: App.DTO.Ticket.PlayerTicketListDto[];
    selectedTicket: App.DTO.Ticket.PlayerTicketListDto | null;
    setSelectedTicket: (ticket: App.DTO.Ticket.PlayerTicketListDto | null) => void;
    filters: any;
    handleSortChange: (value: string) => void;
    handleSearchSubmit: (search: string) => void;
    loadingMore: boolean;
    hasMore: boolean;
    handleLoadMore: () => void;
}

const PlayerTicketList = ({
                              tickets,
                              selectedTicket,
                              setSelectedTicket,
                              filters,
                              handleSortChange,
                              handleSearchSubmit,
                              loadingMore,
                              hasMore,
                              handleLoadMore,
                          }: PlayerTicketListProps) => {
    return (
        <div
            className={cn(
                "col-span-12 lg:col-span-5 2xl:col-span-4 flex flex-col gap-4 px-6 min-h-[900px]",
                selectedTicket && "hidden lg:flex"
            )}
        >
            {/* Панель поиска и сортировки */}
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

            {/* Список тикетов */}
            <div className="flex flex-col gap-2 overflow-y-auto">
                {tickets.length === 0 ? (
                    <div className="flex flex-col items-center gap-2 text-sm text-center text-muted-foreground py-10">
                        <TicketX className="size-8" />
                        No tickets found
                    </div>
                ) : (
                    tickets.map((item) => (
                        <div
                            key={item.id}
                            className={cn(
                                "flex justify-between items-center gap-2 py-3 px-4 hover:bg-muted-foreground/10 rounded-lg cursor-pointer",
                                selectedTicket?.id === item.id && "bg-muted-foreground/10"
                            )}
                            onClick={() => setSelectedTicket(item)}
                        >
                            <div className="flex flex-col gap-2">
                                <h4 className="text-sm font-medium">{item.ticket_number}</h4>
                                <Badge
                                    variant="outline"
                                    className={`px-1.5 py-0.5 text-xs border-${item.status.color} text-${item.status.color}`}
                                >
                                    {item.status.name}
                                </Badge>
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

            {/* Кнопка "Load more" */}
            {hasMore && (
                <div className="flex justify-center mt-4">
                    <Button
                        variant="link"
                        size="sm"
                        onClick={handleLoadMore}
                        className={cn(
                            loadingMore &&
                            "cursor-not-allowed text-muted-foreground pointer-events-none"
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

export default PlayerTicketList;
