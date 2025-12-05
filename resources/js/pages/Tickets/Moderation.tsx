import React, { useEffect, useState } from "react";
import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import {
    Card, CardContent, CardDescription, CardHeader, CardTitle
} from "@/components/ui/card";
import { route } from 'ziggy-js';
import useApi from "@/hooks/use-api";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import FilterPanel from "@/components/ticket/my/filter-panel";
import TicketList from "@/components/ticket/my/ticket-list";
import TicketComments from "@/components/ticket/my/ticket-comments";
import TicketDetailsModerator from "@/components/ticket/moderation/ticket-details-moderator";
import {
    CheckCircle, ClipboardCheck, Folder, XCircle
} from "lucide-react";

interface TicketPageProps {
    tickets: App.DTO.InfiniteScrollDto<App.DTO.Ticket.TicketListDto>;
    countries: App.DTO.CountryDto[];
    statuses: App.DTO.Ticket.TicketStatusDto[];
    topics: App.DTO.Ticket.TicketTopicDto[];
    categories: App.DTO.Ticket.TicketCategoryDto[];
    projects: App.DTO.ProjectDto[]
}

export default function Page({
                                 tickets,
                                 countries,
                                 statuses,
                                 topics,
                                 categories,
                                 projects,
                             }: TicketPageProps) {

    const [ticketList, setTicketList] = useState(tickets.items);
    const [nextCursor, setNextCursor] = useState<string | null>(tickets.nextCursor);
    const [hasMore, setHasMore] = useState<boolean>(tickets.hasMore);

    const [filterPanelOpen, setFilterPanelOpen] = useState(false);
    const [isEditingField, setIsEditingField] = useState(false);
    const [selectedTicket, setSelectedTicket] = useState<App.DTO.Ticket.TicketListDto | null>(null);

    const api = useApi();

    const [filters, setFilters, submitFilters, resetFilters, loadingFilters] = useInertiaUrlState(
        {
            currentTicketId: 0,
            search: '',
            sort: 'date_desc',
            filter: {
                date: {
                    from: undefined as string | undefined,
                    to: undefined as string | undefined,
                },
                statuses: [],
                topics: [],
                categories: [],
                type: 'all',
            },
        },
        {
            omitDefaults: ['search', 'sort', 'currentTicketId','type'],
            autoSubmit: true,
            routerOptions: {
                preserveState: true,
                preserveScroll: true
            }
        }
    );

    const sortTabs = [
        { label: <span className="flex items-center gap-2 text-sm"><Folder size={14} />All</span>, value: "all" },
        { label: <span className="flex items-center gap-2 text-sm"><CheckCircle size={14} />On Approve</span>, value: "approval" },
        { label: <span className="flex items-center gap-2 text-sm"><ClipboardCheck size={14} />Todo</span>, value: "todo" },
        { label: <span className="flex items-center gap-2 text-sm"><XCircle size={14} />Closed</span>, value: "closed" },
    ];

    useEffect(() => {
        setTicketList(tickets.items);
        setNextCursor(tickets.nextCursor);
        setHasMore(tickets.hasMore);
    }, [tickets.items]);

    const handleTabChange = (value: string) => {
        setFilters({
            filter: {
                ...filters.filter,
                type: value
            },
        });
    };

    const handleSearchSubmit = (search: string) => {
        setFilters({ search });
    };

    const handleSortSubmit = (sort: string) => {
        setFilters({ sort });
    };

    const handleAddComment = (ticketId: number, comment: string) => {
        if (!comment.trim()) return;

        api.post(
            route('tickets.comments', { ticketId }),
            { comment },
            {
                onSuccess: (data) => {
                    const newComment = data.comment;

                    setSelectedTicket(prev => {
                        if (!prev || prev.id !== ticketId) return prev;

                        const updated = {
                            ...prev,
                            comments: [...prev.comments, newComment],
                        };

                        setTicketList(list =>
                            list.map(ticket =>
                                ticket.id === ticketId
                                    ? { ...ticket, comments: updated.comments }
                                    : ticket
                            )
                        );

                        return updated;
                    });
                },
            }
        ).then();
    };

    const handleTicketChange = (ticketId: number, statusId: number, result: string | null) => {
        if (result === '')  {
            return;
        }

        api.put(
            route('tickets.updateModerator', { ticketId }),
            {
                status_id: statusId,
                result: result
            },
            {
                onSuccess: (data) => {
                    const updatedTicket = data.ticket;

                    setSelectedTicket(updatedTicket);
                    setTicketList(list =>
                        list.map(ticket =>
                            ticket.id === ticketId ? { ...ticket, ...updatedTicket } : ticket
                        )
                    );
                },
            }
        ).then();
    };

    const handleFilterPanelToggle = (value: boolean) => {
        setFilterPanelOpen(value);
    };

    const toUtcDateString = (date?: Date): string | undefined => {
        if (!date) return undefined;
        return new Date(Date.UTC(
            date.getFullYear(),
            date.getMonth(),
            date.getDate()
        )).toISOString().split('T')[0];
    };

    const handleLoadMore = () => {
        if (!nextCursor) return;

        api.get(
            route('tickets.moderation.show.more',
                {
                    ...filters,
                    cursor: nextCursor,
                }),
            {
                requestName: "loadMoreTickets",
                onSuccess: (data) => {
                    const newTickets = data.tickets as App.DTO.InfiniteScrollDto<App.DTO.Ticket.TicketListDto>;

                    setTicketList((prev) => [...prev, ...newTickets.items]);
                    setNextCursor(newTickets.nextCursor);
                    setHasMore(newTickets.hasMore);
                },
            }
        );
    };

    return (
        <AppLayout>
            <Head title="Moderation" />

            <Card>
                <CardHeader className="border-b">
                    <CardTitle>Moderation Tickets</CardTitle>
                    <CardDescription>List of all tickets</CardDescription>
                </CardHeader>

                <CardContent className="relative flex flex-col gap-6 h-full pb-6 border-b">
                    <FilterPanel
                        isOpen={filterPanelOpen}
                        onToggle={handleFilterPanelToggle}
                        filters={filters}
                        setFilters={setFilters}
                        statuses={statuses}
                        topics={topics}
                        categories={categories}
                        toUtcDateString={toUtcDateString}
                    />
                </CardContent>

                <CardContent className="relative grid grid-cols-12 h-full p-0">
                    <TicketList
                        tickets={ticketList}
                        selectedTicket={selectedTicket}
                        setSelectedTicket={setSelectedTicket}
                        filters={filters}
                        sortTabs={sortTabs}
                        handleSortChange={handleSortSubmit}
                        handleTabChange={handleTabChange}
                        handleSearchSubmit={handleSearchSubmit}

                        hasMore={hasMore}
                        handleLoadMore={handleLoadMore}
                        loadingMore={api.isLoading.request('loadMoreTickets')}
                    />

                    <div className="hidden lg:block absolute -top-6 -bottom-6 left-5/12 2xl:left-4/12 w-px bg-border" />

                    <div className="col-span-12 lg:col-span-7 2xl:col-span-8 px-6 py-0 relative">
                        {selectedTicket && (
                            <>
                                <TicketDetailsModerator
                                    selectedTicket={selectedTicket}
                                    isEditingField={isEditingField}
                                    setIsEditingField={setIsEditingField}
                                    setSelectedTicket={setSelectedTicket}
                                    handleTicketChange={handleTicketChange}
                                    countries={countries}
                                    projects={projects}
                                />

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
        </AppLayout>
    );
}
