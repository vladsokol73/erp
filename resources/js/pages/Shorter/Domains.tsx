import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import InputSearch from "@/components/ui/input-search";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import { CrudTable } from "@/components/common/crud-table";
import { useCrudTableState } from "@/hooks/use-crud-table-state";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import DateFormatter from "@/components/common/date-formatter";
import useApi from "@/hooks/use-api";
import {route} from "ziggy-js";
import React from "react";
import {z} from "zod";
import {Checkbox} from "@/components/ui/checkbox";
import {Button} from "@/components/ui/button";
import {Plus} from "lucide-react";

interface Props {
    domains: App.DTO.PaginatedListDto<App.DTO.Shorter.DomainDto>;
}

export default function DomainPage({ domains }: Props) {

    const api = useApi();

    const [filters, setFilters] = useInertiaUrlState(
        {
            search: "",
            page: 1,
        },
        {
            omitDefaults: ["search", "page"],
            autoSubmit: true,
            routerOptions: {
                preserveState: true,
                preserveScroll: true,
            },
        }
    );

    const handleSearchChange = (search: string) => {
        setFilters({ search });
    };

    const handlePageChange = (page: number) => {
        setFilters({ page });
    };

    const crud = useCrudTableState<App.DTO.Shorter.DomainDto>({
        defaultForm: () => ({
            domain: "",
            redirect_url: "",
            is_active: true,
        }),
        initialData: domains
    });

    const handleCreate = async (data: Partial<App.DTO.Shorter.DomainDto>) => {
        await api.post(
            route('shorter.domain.create'),
            {
                redirect_url: data.redirect_url,
                domain: data.domain
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
        )
    };

    const handleUpdate  = async (id: number, data: Partial<App.DTO.Shorter.DomainDto>) => {
        await api.put(
            route('shorter.domains.edit', {domainId: id}),
            {
                redirect_url: data.redirect_url,
                is_active: data.is_active
            },
            {
                onSuccess: (data) => {
                    crud.updateItem(id, data.url)
                },
                onError: (error) => {
                    const message = error || "Unknown error"
                    crud.setFormError(message)
                }
            }
        )
    }

    const handleDelete = async (id: number) => {
        await api.delete(
            route('shorter.domains.delete', {domainId: id}),
            {
                onSuccess: (data) => {
                    setFilters({ page: filters.page });
                },
                onError: (error) => {

                }
            }
        )
    };

    return (
        <AppLayout>
            <Head title="Manage Domain" />

            <div className="mb-4">
                <h1 className="text-2xl font-bold mb-4">
                    Manage Domain
                </h1>
                <a
                    href="https://services.investingindigital.com/api/shorter/docs#/"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-primary hover:text-primary/50 underline"
                >
                    API Documentation
                </a>
            </div>

            <div className="flex items-center gap-4 mb-4">
                <Button className="!py-5 !px-4" onClick={crud.openCreateModal}>
                    <Plus/>
                    New Domain
                </Button>
                <div className="w-full">
                    <InputSearch defaultValue={filters.search} onChangeDebounced={handleSearchChange} />
                </div>
            </div>

            <CrudTable<App.DTO.Shorter.DomainDto>
                tableTitle="Domain"
                tableDescription="List of domains"
                resourceName="domain"
                crudState={crud}
                onCreate={handleCreate}
                onUpdate={handleUpdate}
                onDelete={handleDelete}
                columns={[
                    {
                        key: "id",
                        title: "ID",
                    },
                    {
                        key: "domain",
                        title: "Domain",
                    },
                    {
                        key: "redirect_url",
                        title: "Redirect URL",
                    },
                    {
                        key: "created_at",
                        title: "Created",
                        render: (tag) => (
                            <DateFormatter
                                variant="short"
                                className="text-muted-foreground text-sm"
                                dateString={tag.created_at}
                            />
                        ),
                    },
                    {
                        key: "is_active",
                        title: "Active",
                        render: (tag) => (
                            <Badge
                                variant={tag.is_active ? "default" : "secondary"}
                            >
                                {tag.is_active ? "Active" : "Inactive"}
                            </Badge>
                        ),
                    },
                ]}
                fields={[
                    {
                        key: "domain",
                        label: "Domain",
                        schema: z.string().min(1, "Domain is required"),
                        hidden: ["edit"],
                        render: ({ value, onChange, error }) => (
                            <div className="flex flex-col gap-2">
                                <Input
                                    value={value ? String(value) : ""}
                                    onChange={(e) => onChange(e.target.value)}
                                    placeholder="Enter domain"
                                />
                                {error && <p className="text-xs text-red-500">{error}</p>}
                            </div>
                        ),
                    },
                    {
                        key: "redirect_url",
                        label: "Redirect URL",
                        schema: z.string().min(1, "Redirect URL is required"),
                        render: ({ value, onChange, error }) => (
                            <div className="flex flex-col gap-2">
                                <Input
                                    value={value ? String(value) : ""}
                                    onChange={(e) => onChange(e.target.value)}
                                    placeholder="Enter redirect url"
                                />
                                {error && <p className="text-xs text-red-500">{error}</p>}
                            </div>
                        ),
                    },
                    {
                        key: "is_active",
                        hidden: ["create"],
                        default: true,
                        render: ({ value, onChange, error }) => (
                            <div className="flex gap-2 items-center">
                                <Checkbox
                                    id="is_active"
                                    checked={value === true}
                                    onCheckedChange={(checked) => onChange(!!checked)}
                                />
                                <label
                                    htmlFor="is_active"
                                    className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >
                                    Is Active
                                </label>
                                {error && <p className="text-xs text-red-500">{error}</p>}
                            </div>
                        ),
                    }
                ]}
                pagination={{
                    currentPage: domains.currentPage,
                    totalPages: domains.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
            />
        </AppLayout>
    );
}
