import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import InputSearch from "@/components/ui/input-search";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import { CrudTable } from "@/components/common/crud-table";
import { useCrudTableState } from "@/hooks/use-crud-table-state";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import DateFormatter from "@/components/common/date-formatter";
import useApi from "@/hooks/use-api";
import { route } from "ziggy-js";
import React from "react";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {Button} from "@/components/ui/button";
import {Plus} from "lucide-react";

interface Props {
    tokens: App.DTO.PaginatedListDto<App.DTO.ApiTokenListDto>;
    services: App.Enums.ApiServiceEnum;
}

export default function ApiTokensPage({ tokens, services }: Props) {
    console.log(services);

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

    const crud = useCrudTableState<App.DTO.ApiTokenListDto>({
        defaultForm: () => ({
            service: "",
            email: "",
            token: "",
        }),
        initialData: tokens,
    });

    const handleSearchChange = (search: string) => {
        setFilters({ search });
    };

    const handlePageChange = (page: number) => {
        setFilters({ page });
    };

    const handleCreate = async (data: Partial<App.DTO.ApiTokenListDto>) => {
        await api.post(
            route("admin-panel.api.tokens.create"),
            {
                service: data.service,
                email: data.email,
                token: data.token,
            },
            {
                onSuccess: (data) => {
                    setFilters({ page: 1 });
                },
                onError: (error) => {
                    const message = error || "Unknown error";
                    crud.setFormError(message);
                }
            }
        );
    };

    const handleUpdate = async (id: number, data: Partial<App.DTO.ApiTokenListDto>) => {
        await api.put(
            route("admin-panel.api.tokens.edit", { apiTokenId: id }),
            {
                email: data.email,
            },
            {
                onSuccess: (res) => crud.updateItem(id, res.token),
                onError: (error) => crud.setFormError(error || "Unknown error"),
            }
        );
    };

    const handleDelete = async (id: number) => {
        await api.delete(route("admin-panel.api.token.delete", { id }), {
            onSuccess: () => setFilters({ page: filters.page }),
        });
    };

    return (
        <AppLayout>
            <Head title="Manage API Tokens" />

            <h1 className="text-2xl font-bold mb-4">API Tokens</h1>

            <div className="flex items-center gap-4 mb-4">
                <Button className="!py-5 !px-4" onClick={crud.openCreateModal}>
                    <Plus/>
                    New Token
                </Button>

                <div className="w-full">
                    <InputSearch defaultValue={filters.search} onChangeDebounced={handleSearchChange} />
                </div>
            </div>

            <CrudTable<App.DTO.ApiTokenListDto>
                tableTitle="API Tokens"
                tableDescription="List of system tokens"
                resourceName="token"
                crudState={crud}
                onCreate={handleCreate}
                onUpdate={handleUpdate}
                onDelete={handleDelete}
                columns={[
                    { key: "id", title: "ID" },
                    { key: "service", title: "Service" },
                    { key: "email", title: "Email" },
                    {
                        key: "token",
                        title: "Token",
                        render: (item) => (
                            <Badge variant="secondary" className="truncate max-w-[300px]">
                                {item.token}
                            </Badge>
                        ),
                    },
                    {
                        key: "created_at",
                        title: "Created",
                        render: (item) => (
                            <DateFormatter
                                variant="short"
                                className="text-muted-foreground text-sm"
                                dateString={item.created_at}
                            />
                        ),
                    },
                ]}
                fields={[
                    {
                        hidden: ["edit"],
                        key: "service",
                        label: "Service",
                        render: ({ value, onChange }) => (
                            <Select
                                value={value ? String(value) : ""}
                                onValueChange={onChange}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Select service" />
                                </SelectTrigger>
                                <SelectContent>
                                    {Object.values(services).map((service) => (
                                        <SelectItem value={service} key={service}>
                                            {service}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        ),
                    },
                    {
                        key: "email",
                        label: "Email",
                        render: ({ value, onChange }) => (
                            <Input
                                type="email"
                                value={value || ""}
                                onChange={(e) => onChange(e.target.value)}
                                placeholder="Enter email"
                            />
                        ),
                    },
                    {
                        hidden: ["edit"],
                        key: "token",
                        label: "Token",
                        render: ({ value, onChange }) => (
                            <Input
                                value={value || ""}
                                onChange={(e) => onChange(e.target.value)}
                                placeholder="Enter token"
                            />
                        ),
                    },
                ]}
                pagination={{
                    currentPage: tokens.currentPage,
                    totalPages: tokens.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
            />
        </AppLayout>
    );
}
