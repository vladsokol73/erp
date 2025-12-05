import React, {useEffect, useState} from "react";
import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import {
    Card, CardContent, CardDescription, CardHeader, CardTitle,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { route } from "ziggy-js";
import useApi from "@/hooks/use-api";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";

import ConfirmDeleteDialog from "@/components/ticket/confirm-delete-dialog";
import PlayerTicketList from "@/components/player-ticket/player-ticket-list";
import TicketDetails from "@/components/player-ticket/ticket-details";
import TicketComments from "@/components/ticket/my/ticket-comments";
import FilterPanelSimple from "@/components/player-ticket/filter-panel-simple";
import CreateTicketDialog from "@/components/player-ticket/create-ticket-dialog";

interface TicketPageProps {
    tickets: App.DTO.InfiniteScrollDto<App.DTO.Ticket.PlayerTicketListDto>;
    statusCatalog: App.DTO.Ticket.PlayerTicketStatusDto[];
}

export default function Page({ tickets, statusCatalog }: TicketPageProps) {
    const [ticketList, setTicketList] = useState(tickets.items);
    const [nextCursor, setNextCursor] = useState<string | null>(tickets.nextCursor);
    const [hasMore, setHasMore] = useState<boolean>(tickets.hasMore);

    const [selectedTicket, setSelectedTicket] = useState<App.DTO.Ticket.PlayerTicketListDto | null>(null);
    const [filterPanelOpen, setFilterPanelOpen] = useState(false);

    // состояние диалога создания
    const [createOpen, setCreateOpen] = useState(false);

    const api = useApi();

    const [filters, setFilters] = useInertiaUrlState(
        {
            currentTicketId: 0,
            search: "",
            sort: "date_desc",
            filter: {
                date: {
                    from: undefined as string | undefined,
                    to: undefined as string | undefined,
                },
                statuses: [],
            },
        },
        {
            omitDefaults: ["search", "sort", "currentTicketId"],
            autoSubmit: true,
            routerOptions: {
                preserveState: true,
                preserveScroll: true,
            },
        }
    );

    useEffect(() => {
        setTicketList(tickets.items);
        setNextCursor(tickets.nextCursor);
        setHasMore(tickets.hasMore);
    }, [tickets.items, tickets.nextCursor, tickets.hasMore]);

    const handleSearchSubmit = (search: string) => setFilters({ search });
    const handleSortSubmit = (sort: string) => setFilters({ sort });

    const handleAddComment = (ticketId: number, comment: string) => {
        if (!comment.trim()) return;

        api.post(
            route("tickets.player.comments", { ticketId }),
            { comment },
            {
                onSuccess: (data) => {
                    const newComment = data.comment;

                    setSelectedTicket((prev) => {
                        if (!prev || prev.id !== ticketId) return prev;

                        const updated = { ...prev, comments: [...prev.comments, newComment] };

                        setTicketList((list) =>
                            list.map((ticket) =>
                                ticket.id === ticketId ? { ...ticket, comments: updated.comments } : ticket
                            )
                        );

                        return updated;
                    });
                },
            }
        ).then();
    };

    const toUtcDateString = (date?: Date): string | undefined => {
        if (!date) return undefined;
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()))
            .toISOString()
            .split("T")[0];
    };

    const handleLoadMore = () => {
        if (!nextCursor) return;

        api.get(
            route("tickets.player.show.more", { ...filters, cursor: nextCursor }),
            {
                requestName: "loadMoreTickets",
                onSuccess: (data) => {
                    const newTickets =
                        data.tickets as App.DTO.InfiniteScrollDto<App.DTO.Ticket.PlayerTicketListDto>;

                    setTicketList((prev) => [...prev, ...newTickets.items]);
                    setNextCursor(newTickets.nextCursor);
                    setHasMore(newTickets.hasMore);
                },
            }
        );
    };

    const handleCreated = (ticket: App.DTO.Ticket.PlayerTicketListDto) => {
        setTicketList((prev) => [ticket, ...prev]);
        setSelectedTicket(ticket);
    };

    return (
        <AppLayout>
            <Head title="My Ticket" />

            <Card>
                <CardHeader className="border-b">
                    <div className="flex items-center justify-between">
                        <div>
                            <CardTitle>My Ticket</CardTitle>
                            <CardDescription>List of your tickets</CardDescription>
                        </div>

                        <Button variant="outline" onClick={() => setCreateOpen(true)}>
                            Create Ticket
                        </Button>
                    </div>
                </CardHeader>

                <CardContent className="relative flex flex-col gap-6 pb-6 border-b">
                    <FilterPanelSimple
                        isOpen={filterPanelOpen}
                        onToggle={setFilterPanelOpen}
                        filters={filters}
                        setFilters={setFilters}
                        statuses={statusCatalog}
                        toUtcDateString={toUtcDateString}
                    />
                </CardContent>

                <CardContent className="relative grid grid-cols-12 p-0">
                    <PlayerTicketList
                        tickets={ticketList}
                        selectedTicket={selectedTicket}
                        setSelectedTicket={setSelectedTicket}
                        filters={filters}
                        handleSortChange={handleSortSubmit}
                        handleSearchSubmit={handleSearchSubmit}
                        hasMore={hasMore}
                        handleLoadMore={handleLoadMore}
                        loadingMore={api.isLoading.request("loadMoreTickets")}
                    />

                    <div className="hidden lg:block absolute -top-6 -bottom-6 left-5/12 2xl:left-4/12 w-px bg-border" />

                    <div className="col-span-12 lg:col-span-7 2xl:col-span-8 px-6 py-0 relative">
                        {selectedTicket && (
                            <>
                                <TicketDetails ticket={selectedTicket} />

                                <TicketComments
                                    comments={selectedTicket.comments}
                                    ticketId={selectedTicket.id}
                                    handleAddComment={handleAddComment}
                                />
                            </>
                        )}
                    </div>
                </CardContent>
            </Card>

            <CreateTicketDialog
                open={createOpen}
                onOpenChange={setCreateOpen}
                onCreated={handleCreated}
            />
        </AppLayout>
    );
}
