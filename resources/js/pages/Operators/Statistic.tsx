import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import { CrudTable } from "@/components/common/crud-table";
import { useCrudTableState } from "@/hooks/use-crud-table-state";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import { Badge } from "@/components/ui/badge";
import { format } from "date-fns";
import { formatMinutes } from "@/lib/format-time";
import { route } from "ziggy-js";
import useApi from "@/hooks/use-api";
import React from "react";
import StatisticFilterPanel from "@/components/operators/statistic-filter-panel";
import { Button } from "@/components/ui/button";
import { Eye } from "lucide-react";
import OperatorReportsModal from "@/components/operators/operator-reports-modal";

interface Props {
    statistics: App.DTO.PaginatedListDto<App.DTO.Operator.OperatorStatisticListDto>;
    statistic_totals: App.DTO.Operator.OperatorStatisticTotalsDto;
    operators: App.DTO.OperatorDto[];
    channels: App.DTO.ChannelDto[];
}

export default function OperatorStatisticPage({ statistics, statistic_totals, operators, channels }: Props) {

    const api = useApi();

    const [filters, setFilters] = useInertiaUrlState(
        {
            filter: {
                date: {
                    from: undefined as string | undefined,
                    to: undefined as string | undefined,
                },
                operators: ["all"],
                channels: ["all"],
            },
            page: 1,
        },
        {
            autoSubmit: true,
            omitDefaults: ["page"],
            routerOptions: {
                preserveScroll: true,
                preserveState: true,
            },
        }
    );

    const [reportsOpen, setReportsOpen] = React.useState(false);
    const [reports, setReports] = React.useState<App.DTO.Operator.AiRetentionReportDto[] | null>(null);

    const handleOpenReports = (operatorId: number) => {
        setReports(null);
        api.get(
                route("operators.reports.show", { operatorId }) +
                '?date[from]=' + filters.filter.date.from +
                '&date[to]=' + filters.filter.date.to,
            {
                onSuccess: (data) => {
                    setReports(data.reports);
            },
        });
        setReportsOpen(true);
    };

    const handleCloseReports = () => {
        setReportsOpen(false);
        setReports(null);
    };

    const [filterOpen, setFilterOpen] = React.useState(true);


    const crud = useCrudTableState<App.DTO.Operator.OperatorStatisticListDto>({
        defaultForm: () => ({}),
        initialData: statistics,
    });

    const handlePageChange = (page: number) => {
        setFilters({ page });
    };

    const toUtcDateString = (date?: Date): string | undefined =>
        date ? new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate())).toISOString().slice(0, 10) : undefined;


    return (
        <AppLayout>
            <Head title="Operator Statistics" />

            <h1 className="text-2xl font-bold mb-4">Operator Statistics</h1>

            <StatisticFilterPanel
                isOpen={filterOpen}
                onToggle={setFilterOpen}
                filters={filters}
                setFilters={setFilters}
                toUtcDateString={toUtcDateString}
                operators={operators}
                channels={channels}
                totalClients={statistic_totals.all_clients}
                totalNewClients={statistic_totals.all_new_clients}
            />


            <CrudTable<App.DTO.Operator.OperatorStatisticListDto>
                tableTitle="Statistics"
                tableDescription="Daily performance of operators"
                resourceName="statistic"
                crudState={crud}
                columns={[
                    { key: "operator_name", title: "Operator" },
                    {
                        key: "new_client_chats",
                        title: "New Chats",
                    },
                    {
                        key: "total_clients",
                        title: "Total Clients",
                    },
                    {
                        key: "reg_count",
                        title: "Registrations",
                    },
                    {
                        key: "dep_count",
                        title: "Deposits",
                    },
                    {
                        key: "fd",
                        title: "FD",
                    },
                    {
                        key: "cr_dialog_to_fd",
                        title: "CR Dialog2FD",
                    },
                    {
                        key: "inbox_messages",
                        title: "Inbox Msg",
                        render: (item) =>
                            item.inbox_messages !== null ? (
                                item.inbox_messages
                            ) : (
                                <span className="text-muted-foreground italic">–</span>
                            ),
                    },
                    {
                        key: "outbox_messages",
                        title: "Outbox Msg",
                        render: (item) =>
                            item.outbox_messages !== null ? (
                                item.outbox_messages
                            ) : (
                                <span className="text-muted-foreground italic">–</span>
                            ),
                    },
                    {
                        key: "start_time",
                        title: "Start Time",
                        render: (item) =>
                            item.start_time
                                ? format(new Date(item.start_time), "yyyy-MM-dd HH:mm")
                                : <span className="text-muted-foreground italic">–</span>,
                    },
                    {
                        key: "end_time",
                        title: "End Time",
                        render: (item) =>
                            item.end_time
                                ? format(new Date(item.end_time), "yyyy-MM-dd HH:mm")
                                : <span className="text-muted-foreground italic">–</span>,
                    },
                    {
                        key: "total_time",
                        title: "Total Time",
                        render: (item) => (
                            <Badge variant="default">
                                {formatMinutes(item.total_time)}
                            </Badge>
                        ),
                    },

                    {
                        key: "operator_score",
                        title: "Score",
                        render: (item) => {
                            const score = item.operator_score ?? 0;
                            let colorClass = "";

                            if (score > 0) {
                                if (score <= 1) {
                                    colorClass = "!bg-red/15 text-red";
                                } else if (score < 4) {
                                    colorClass = "!bg-orange/15 text-orange";
                                } else {
                                    colorClass = "!bg-green/15 text-green";
                                }
                            }

                            return (
                                <Badge variant="secondary" className={colorClass}>
                                    {score || "—"}
                                </Badge>
                            );
                        },


                    }
                ]}
                actions={(item) => (
                    <Button
                        variant="outline"
                        size="icon"
                        onClick={() => handleOpenReports(item.operator_id)}
                    >
                        <Eye size={16} />
                    </Button>
                )}
                pagination={{
                    currentPage: statistics.currentPage,
                    totalPages: statistics.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
            />


            <OperatorReportsModal
                open={reportsOpen}
                onClose={handleCloseReports}
                reports={reports}
            />
        </AppLayout>
    );
}
