import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import InputSearch from "@/components/ui/input-search";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import { CrudTable } from "@/components/common/crud-table";
import { useCrudTableState } from "@/hooks/use-crud-table-state";
import { Badge } from "@/components/ui/badge";
import useApi from "@/hooks/use-api";
import { route } from "ziggy-js";
import React from "react";
import { Button } from "@/components/ui/button";

import ClientDetailsModal from "@/components/clients/client-details-modal";
import ClientLogsModal from "@/components/clients/client-logs-modal";
import { Eye, List } from "lucide-react";

interface Props {
    clients: App.DTO.PaginatedListDto<App.DTO.Client.ClientListDto>;
}

export default function AllClientsPage({ clients }: Props) {
    const api = useApi();

    const [filters, setFilters] = useInertiaUrlState(
        {
            search: "",
            page: 1,
        },
        {
            autoSubmit: true,
            omitDefaults: ["search", "page"],
            routerOptions: {
                preserveState: true,
                preserveScroll: true,
            },
        }
    );

    const crud = useCrudTableState<App.DTO.Client.ClientListDto>({
        defaultForm: () => ({}),
        initialData: clients,
    });

    const handleSearchChange = (search: string) => {
        setFilters({ search });
    };

    const handlePageChange = (page: number) => {
        setFilters({ page });
    };

    const [detailsClient, setDetailsClient] = React.useState<App.DTO.Client.ClientDetailsDto | null>(null);
    const [selectedClient, setSelectedClient] = React.useState<App.DTO.Client.ClientListDto | null>(null);
    const [detailsOpen, setDetailsOpen] = React.useState(false);

    const [logsOpen, setLogsOpen] = React.useState(false);
    const [clientLogs, setClientLogs] = React.useState<App.DTO.Client.ClientLogDto[] | null>(null);

    const handleOpenDetails = (client: App.DTO.Client.ClientListDto) => {
        setSelectedClient(client);
        setDetailsClient(null);

        api.get( route("clients.details.show", { clientId: client.id }),
            {
                onSuccess: (data) => {
                   setDetailsClient(data.client);
                },
            }
        )
        setDetailsOpen(true);
    };

    const handleOpenLogs = (client: App.DTO.Client.ClientListDto) => {
        setSelectedClient(client);
        setClientLogs(null);

        api.get(route("clients.logs.show", { clientId: client.id }), {
            onSuccess: (data) => {
                setClientLogs(data.logs);
            },
        });

        setLogsOpen(true);
    };

    const handleCloseDetails = () => {
        setDetailsOpen(false);
        setSelectedClient(null);
    };

    const handleCloseLogs = () => {
        setLogsOpen(false);
        setSelectedClient(null);
    };


    return (
        <AppLayout>
            <Head title="Clients" />

            <h1 className="text-2xl font-bold mb-4">All Clients</h1>

            <div className="mb-4">
                <InputSearch
                    defaultValue={filters.search}
                    onChangeDebounced={handleSearchChange}
                />
            </div>

            <CrudTable<App.DTO.Client.ClientListDto>
                tableTitle="Clients"
                tableDescription="Clients list"
                resourceName="client"
                crudState={crud}
                columns={[
                    { key: "id", title: "ID" },
                    {
                        key: "clickid",
                        title: "Click ID",
                        render: (item) =>
                            item.clickid ? (
                                <Badge variant="secondary">{item.clickid}</Badge>
                            ) : (
                                <span className="text-muted-foreground italic">null</span>
                            ),
                    },
                    {
                        key: "tg_id",
                        title: "Telegram ID",
                        render: (item) =>
                            item.tg_id ? item.tg_id : <span className="text-muted-foreground italic">–</span>,
                    },
                    {
                        key: "c2d_channel_id",
                        title: "C2D Channel ID",
                        render: (item) =>
                            item.c2d_channel_id ? item.c2d_channel_id : <span className="text-muted-foreground italic">–</span>,
                    },
                ]}
                actions={(item) => (
                    <div className="flex gap-2">
                        <Button
                            variant="outline"
                            size="icon"
                            onClick={() => handleOpenDetails(item)}
                        >
                            <Eye size={16} />
                        </Button>
                        <Button
                            variant="outline"
                            size="icon"
                            onClick={() => handleOpenLogs(item)}
                        >
                            <List size={16} />
                        </Button>
                    </div>
                )}
                pagination={{
                    currentPage: clients.currentPage,
                    totalPages: clients.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
            />

            <ClientDetailsModal
                open={detailsOpen}
                onClose={handleCloseDetails}
                client={detailsClient}
            />

            <ClientDetailsModal
                open={detailsOpen}
                onClose={handleCloseDetails}
                client={detailsClient}
            />

            <ClientLogsModal
                open={logsOpen}
                onClose={handleCloseLogs}
                logs={clientLogs}
            />

        </AppLayout>
    );
}
