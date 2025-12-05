import React from "react";
import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import InputSearch from "@/components/ui/input-search";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import { CrudTable } from "@/components/common/crud-table";
import { useCrudTableState } from "@/hooks/use-crud-table-state";

type ProductLogRow = Omit<App.DTO.Log.ProductLogListDto, 'player_id'> & { player_id: string };

interface Props {
    logs: App.DTO.PaginatedListDto<ProductLogRow>;
}

export default function ProductLogsPage({ logs }: Props) {
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

    const crud = useCrudTableState<ProductLogRow>({
        defaultForm: () => ({}),
        initialData: logs,
    });

    const handleSearchChange = (search: string) => setFilters({ search });
    const handlePageChange = (page: number) => setFilters({ page });

    return (
        <AppLayout>
            <Head title="Product Logs" />

            <h1 className="text-2xl font-bold mb-4">Product Logs</h1>

            <div className="mb-4">
                <InputSearch
                    defaultValue={filters.search}
                    onChangeDebounced={handleSearchChange}
                    placeholder="Search by player_id..."
                />
            </div>

            <CrudTable<ProductLogRow>
                tableTitle="Product Logs"
                tableDescription="Logs from product_logs table"
                resourceName="product-log"
                crudState={crud}
                columns={[
                    { key: "id", title: "ID" },
                    { key: "player_id", title: "Player ID" },
                    { key: "status", title: "Status" },
                    { key: "c2d_channel_id", title: "C2D Channel ID" },
                    { key: "tg_id", title: "Telegram ID" },
                    { key: "prod_id", title: "Product ID" },
                    { key: "dep_sum", title: "Dep Sum" },
                    { key: "operator_id", title: "Operator ID" },
                    { key: "created_at", title: "Created At" },
                ]}
                pagination={{
                    currentPage: logs.currentPage,
                    totalPages: logs.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
            />
        </AppLayout>
    );
}


