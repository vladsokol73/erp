import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import { CrudTable } from "@/components/common/crud-table";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import { useCrudTableState } from "@/hooks/use-crud-table-state";
import useApi from "@/hooks/use-api";
import { Badge } from "@/components/ui/badge";
import { route } from "ziggy-js";

import RestartJobDialog from "@/components/clients/restart-job-dialog";
import RestartAllJobsDialog from "@/components/clients/restart-all-jobs-dialog";


interface Props {
    jobs: App.DTO.PaginatedListDto<App.DTO.Client.FailedJobListDto>;
}

export default function FailedJobsPage({ jobs }: Props) {

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
                preserveScroll: true,
                preserveState: true,
            },
        }
    );

    const crud = useCrudTableState<App.DTO.Client.FailedJobListDto>({
        defaultForm: () => ({}),
        initialData: jobs,
    });

    const handlePageChange = (page: number) => {
        setFilters({ page });
    };

    const handleRestartJob = async (failedJobId: number) => {
        await api.post(
            route("clients.failed.jobs.restart", { failedJobId }),
            {},
            {
                onSuccess: () => {
                    crud.deleteItem(failedJobId);
                },
            }
        );
    };

    const handleRestartAllJobs = async () => {
        await api.post(route("clients.failed.jobs.restart.all"),
            {},
            {
                onSuccess: () => {
                    crud.clearItems();
                },
            });
    };

    return (
        <AppLayout>
            <Head title="Failed Jobs" />

            <h1 className="text-2xl font-bold">Failed Jobs</h1>

            <RestartAllJobsDialog onConfirm={handleRestartAllJobs} />

            <CrudTable<App.DTO.Client.FailedJobListDto>
                tableTitle="Failed Jobs"
                tableDescription="List of failed queue jobs"
                resourceName="job"
                crudState={crud}
                columns={[
                    { key: "id", title: "Id" },
                    {
                        key: "connection",
                        title: "Connection",
                        render: (item) => <Badge variant="outline">{item.connection}</Badge>,
                    },
                    { key: "queue", title: "Queue" },
                    {
                        key: "failed_at",
                        title: "Failed At",
                        render: (item) => new Date(item.failed_at).toLocaleString(),
                    },
                    {
                        key: "exception",
                        title: "Exception",
                        render: (item) => (
                            <div className="max-w-[600px] overflow-x-auto">
                                <pre className="whitespace-nowrap text-sm text-muted-foreground">
                                    {item.exception}
                                </pre>
                            </div>
                        ),
                    },
                ]}
                actions={(item) => <RestartJobDialog jobId={item.id} onConfirm={handleRestartJob} />}
                pagination={{
                    currentPage: jobs.currentPage,
                    totalPages: jobs.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
            />
        </AppLayout>
    );
}
