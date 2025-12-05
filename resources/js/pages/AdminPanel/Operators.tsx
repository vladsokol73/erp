"use client";

import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import InputSearch from "@/components/ui/input-search";
import { CrudTable } from "@/components/common/crud-table";
import useApi from "@/hooks/use-api";
import { useCrudTableState } from "@/hooks/use-crud-table-state";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import DateFormatter from "@/components/common/date-formatter";
import { Input } from "@/components/ui/input";
import { route } from "ziggy-js";
import React from "react";
import { Checkbox } from "@/components/ui/checkbox";

interface Props {
    operators: App.DTO.PaginatedListDto<App.DTO.OperatorListDto>;
    channels: App.DTO.PaginatedListDto<App.DTO.ChannelListDto>;
}

export default function OperatorsPage({ operators, channels }: Props) {
    const api = useApi();

    const [operatorFilters, setOperatorFilters] = useInertiaUrlState(
        {
            operatorSearch: "",
            operatorPage: 1,
        },
        {
            autoSubmit: true,
            routerOptions: {
                preserveState: true,
                preserveScroll: true,
            },
        }
    );

    const [channelFilters, setChannelFilters] = useInertiaUrlState(
        {
            channelSearch: "",
            channelPage: 1,
        },
        {
            autoSubmit: true,
            routerOptions: {
                preserveState: true,
                preserveScroll: true,
            },
        }
    );

    const operatorCrud = useCrudTableState<App.DTO.OperatorListDto>({
        defaultForm: () => ({
            name: "",
        }),
        initialData: operators,
    });

    const channelCrud = useCrudTableState<App.DTO.ChannelListDto>({
        defaultForm: () => ({
            name: "",
        }),
        initialData: channels,
    });

    const handleOperatorUpdate = async (
        id: number,
        data: Partial<App.DTO.OperatorListDto>
    ) => {
        await api.put(
            route("admin-panel.operators.edit", { operatorId: id }),
            {
                name: data.name,
                has_ai_retention: data.has_ai_retention,
            },
            {
                onSuccess: (response) =>
                    operatorCrud.updateItem(id, response.operator),
                onError: (error) =>
                    operatorCrud.setFormError(error || "Unknown error"),
            }
        );
    };

    const handleChannelUpdate = async (
        id: number,
        data: Partial<App.DTO.ChannelListDto>
    ) => {
        await api.put(
            route("admin-panel.operators.channels.edit", { channelId: id }),
            {
                name: data.name,
                has_ai_retention: data.has_ai_retention,
            },
            {
                onSuccess: (response) =>
                    channelCrud.updateItem(id, response.channel),
                onError: (error) =>
                    channelCrud.setFormError(error || "Unknown error"),
            }
        );
    };

    const handleOperatorDelete = async (id: number) => {
        await api.delete(route("admin-panel.operators.delete", { operatorId: id }), {
            onSuccess: () =>
                setOperatorFilters({
                    operatorPage: operatorFilters.operatorPage,
                }),
        });
    };

    const handleChannelDelete = async (id: number) => {
        await api.delete(
            route("admin-panel.operators.channels.delete", { channelId: id }),
            {
                onSuccess: () =>
                    setChannelFilters({
                        channelPage: channelFilters.channelPage,
                    }),
            }
        );
    };

    return (
        <AppLayout>
            <Head title="Manage Operators & Channels" />

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Operators */}
                <div className="flex flex-col gap-6">
                    <h1 className="text-2xl font-bold">Manage Operators</h1>

                    <InputSearch
                        defaultValue={operatorFilters.operatorSearch}
                        onChangeDebounced={(search) =>
                            setOperatorFilters({ operatorSearch: search })
                        }
                    />

                    <CrudTable<App.DTO.OperatorListDto>
                        tableTitle="Operators"
                        tableDescription="List of operators"
                        resourceName="operator"
                        crudState={operatorCrud}
                        onUpdate={handleOperatorUpdate}
                        onDelete={handleOperatorDelete}
                        columns={[
                            { key: "operator_id", title: "ID" },
                            { key: "name", title: "Name" },
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
                                key: "name",
                                label: "Name",
                                render: ({ value, onChange }) => (
                                    <Input
                                        value={String(value)}
                                        onChange={(e) => onChange(e.target.value)}
                                        placeholder="Enter name"
                                    />
                                ),
                            },
                            {
                                key: "has_ai_retention",
                                default: true,
                                render: ({ value, onChange, error }) => (
                                    <div className="flex gap-2 items-center">
                                        <Checkbox
                                            id="has_ai_retention"
                                            checked={value === true}
                                            onCheckedChange={(checked) => onChange(!!checked)}
                                        />
                                        <label
                                            htmlFor="has_ai_retention"
                                            className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                        >
                                            AI Retention
                                        </label>
                                        {error && (
                                            <p className="text-xs text-red-500">{error}</p>
                                        )}
                                    </div>
                                ),
                            },
                        ]}
                        pagination={{
                            currentPage: operators.currentPage,
                            totalPages: operators.lastPage,
                            onPageChange: (page) =>
                                setOperatorFilters({ operatorPage: page }),
                            paginationItemsToDisplay: 3,
                        }}
                    />
                </div>

                {/* Channels */}
                <div className="flex flex-col gap-6">
                    <h1 className="text-2xl font-bold">Manage Channels</h1>

                    <InputSearch
                        defaultValue={channelFilters.channelSearch}
                        onChangeDebounced={(search) =>
                            setChannelFilters({ channelSearch: search })
                        }
                    />

                    <CrudTable<App.DTO.ChannelListDto>
                        tableTitle="Channels"
                        tableDescription="List of channels"
                        resourceName="channel"
                        crudState={channelCrud}
                        onUpdate={handleChannelUpdate}
                        onDelete={handleChannelDelete}
                        columns={[
                            { key: "channel_id", title: "ID" },
                            { key: "name", title: "Name" },
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
                                key: "name",
                                label: "Title",
                                render: ({ value, onChange }) => (
                                    <Input
                                        value={String(value)}
                                        onChange={(e) => onChange(e.target.value)}
                                        placeholder="Enter title"
                                    />
                                ),
                            },
                            {
                                key: "has_ai_retention",
                                default: true,
                                render: ({ value, onChange, error }) => (
                                    <div className="flex gap-2 items-center">
                                        <Checkbox
                                            id="has_ai_retention"
                                            checked={value === true}
                                            onCheckedChange={(checked) => onChange(!!checked)}
                                        />
                                        <label
                                            htmlFor="has_ai_retention"
                                            className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                        >
                                            AI Retention
                                        </label>
                                        {error && (
                                            <p className="text-xs text-red-500">{error}</p>
                                        )}
                                    </div>
                                ),
                            },
                        ]}
                        pagination={{
                            currentPage: channels.currentPage,
                            totalPages: channels.lastPage,
                            onPageChange: (page) =>
                                setChannelFilters({ channelPage: page }),
                            paginationItemsToDisplay: 3,
                        }}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
