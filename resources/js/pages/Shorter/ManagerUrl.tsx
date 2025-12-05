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
import {Select, SelectContent, SelectItem, SelectTrigger, SelectValue} from "@/components/ui/select";
import React from "react";

interface Props {
    urls: App.DTO.PaginatedListDto<App.DTO.Shorter.UrlListDto>;
    domains: App.DTO.Shorter.DomainDto[];
}

export default function UrlPage({ urls, domains }: Props) {

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

    const crud = useCrudTableState<App.DTO.Shorter.UrlListDto>({
        defaultForm: () => ({

        }),
        initialData: urls
    });

    const handleCreate = async (data: Partial<App.DTO.Shorter.UrlListDto>) => {

    };

    const handleUpdate  = async (id: number, data: Partial<App.DTO.Shorter.UrlListDto>) => {
        await api.put(
            route('shorter.url.edit', {urlId: id}),
            {
                original_url: data.original_url,
                short_code: data.short_code,
                domain: data.domain,
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
            route('shorter.url.delete', {urlId: id}),
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
            <Head title="Manage URL" />

            <h1 className="text-2xl font-bold mb-4">
                Manage URL
            </h1>

            <div className="flex items-center gap-4 mb-4">
                <div className="w-full">
                    <InputSearch defaultValue={filters.search} onChangeDebounced={handleSearchChange} />
                </div>
            </div>

            <CrudTable<App.DTO.Shorter.UrlListDto>
                tableTitle="URL"
                tableDescription="List of urls"
                resourceName="url"
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
                        key: "original_url",
                        title: "Original URL",
                    },
                    {
                        key: "domain",
                        title: "Domain",
                        cellClassName: "text-muted-foreground",
                    },
                    {
                        key: "short_code",
                        title: "Short URL",
                        render: (url) => (
                           <Badge>{url.short_code}</Badge>
                        ),
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
                ]}
                fields={[
                    {
                        key: "original_url",
                        label: "Original URL",
                        render: ({ value, onChange, error }) => (
                            <Input
                                onChange={(e) => onChange(e.target.value)}
                                value={value}
                                type="text"
                            />
                        ),
                    },
                    {
                        key: "short_code",
                        label: "Short code",
                        render: ({ value, onChange, error }) => (
                            <Input
                                onChange={(e) => onChange(e.target.value)}
                                value={value}
                                type="text"
                            />
                        ),
                    },
                    {
                        key: "domain",
                        label: "Domain",
                        render: ({ value, onChange, error }) => (
                            <Select value={String(value)} onValueChange={onChange}>
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Select domain" />
                                </SelectTrigger>
                                <SelectContent>
                                    {domains.map((d) => (
                                        <SelectItem key={d.id} value={d.domain}>
                                            {d.domain}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        ),
                    }
                ]}
                pagination={{
                    currentPage: urls.currentPage,
                    totalPages: urls.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
            />
        </AppLayout>
    );
}
