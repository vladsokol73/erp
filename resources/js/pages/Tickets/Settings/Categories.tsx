import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import InputSearch from "@/components/ui/input-search";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import { CrudTable } from "@/components/common/crud-table";
import { useCrudTableState } from "@/hooks/use-crud-table-state";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import {Flag, Play, Plus } from "lucide-react";
import { z } from "zod";
import useApi from "@/hooks/use-api";
import {route} from "ziggy-js";
import {Badge} from "@/components/ui/badge";
import { Textarea } from "@/components/ui/textarea";
import {Checkbox} from "@/components/ui/checkbox";
import {NumberInputWithButtons} from "@/components/common/numbe-iInput-with-buttons";
import MultiSelect from "@/components/ui/multi-select";
import {HorizontalTabs} from "@/components/common/horizontal-tabs";

interface Props {
    ticketsCategories: App.DTO.PaginatedListDto<App.DTO.Ticket.TicketCategoriesListDto>
    ticketsStatuses: App.DTO.Ticket.TicketStatusDto[];
}

export default function Statuses({ ticketsCategories, ticketsStatuses }: Props) {
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

    const crud = useCrudTableState<App.DTO.Ticket.TicketCategoriesListDto>({
        defaultForm: () => ({
            name: "",
        }),
        initialData: ticketsCategories
    });

    const handleCreate = async (data: Partial<App.DTO.Ticket.TicketCategoriesListDto>) => {
        await api.post(
            route('tickets.settings.categories.create'),
            {
                name: data.name,
                description: data.description,
                sort_order: data.sort_order,
                is_active: data.is_active,
                statuses: data.statuses
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

    const handleUpdate  = async (id: number, data: Partial<App.DTO.Ticket.TicketCategoriesListDto>) => {
        await api.put(
            route('tickets.settings.categories.update', {categoryId: id}),
            {
                name: data.name,
                description: data.description,
                sort_order: data.sort_order,
                is_active: data.is_active,
                statuses: data.statuses
            },
            {
                onSuccess: (data) => {
                    crud.updateItem(id, data.category)
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
            route('tickets.settings.categories.delete', {categoryId: id}),
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
            <HorizontalTabs
                tabs={[
                    { label: "Categories", href: route("tickets.settings.categories.show") },
                    { label: "Topics", href: route("tickets.settings.topic.show") },
                    { label: "Statuses", href: route("tickets.settings.statuses.show") },
                    { label: "Form Fields", href: route("tickets.settings.fields.show") },
                ]}
            />

            <Head title="Сategories" />

            <div className="flex items-center gap-4 my-4">
                <Button className="!py-5 !px-4" onClick={crud.openCreateModal}>
                    <Plus/>
                    New Category
                </Button>

                <div className="w-full">
                    <InputSearch defaultValue={filters.search} onChangeDebounced={handleSearchChange} />
                </div>
            </div>

            <CrudTable<App.DTO.Ticket.TicketCategoriesListDto>
                tableTitle="Сategories"
                tableDescription="List of categories"
                resourceName="category"
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
                        key: "name",
                        title: "Name",
                    },
                    {
                        key: "description",
                        title: "Description",
                        render: (item) => item.description ? <span className="block truncate max-w-64">{item.description}</span> : <span className="text-muted-foreground">No description</span>
                    },
                    {
                        key: "statuses",
                        title: "Statuses",
                        render: (item) => (
                            <div className="flex flex-wrap gap-1">
                                {item.statuses.map((status, index: number) => (
                                    <Badge key={index} className={`bg-${status.color}`}>
                                        {status.is_default && <Play/>}
                                        {status.is_final && <Flag/>}
                                        {status.name}
                                    </Badge>
                                ))}
                            </div>
                        )
                    },
                    {
                        key: "is_active",
                        title: "Is Active",
                        render: (item) => <Badge variant={`${item.is_active ? "default" : "secondary"}`} >{item.is_active ? "Yes" : "No"}</Badge>
                    },
                    {
                        key: "sort_order",
                        title: "Sort Order",
                    },
                ]}
                fields={[
                    {
                        key: "name",
                        label: "Name",
                        schema: z.string().min(1, "Name is required"),
                        render: ({ value, onChange, error }) => (
                            <div className="flex flex-col gap-2">
                                <Input
                                    value={value?.toString()}
                                    onChange={(e) => onChange(e.target.value)}
                                    placeholder="Enter category name"
                                />
                                {error && <p className="text-xs text-red-500">{error}</p>}
                            </div>
                        ),
                    },
                    {
                        key: "description",
                        label: "Description",
                        schema: z.string().nullable(),
                        render: ({ value, onChange, error }) => (
                            <div className="flex flex-col gap-2">
                                <Textarea
                                    value={value?.toString()}
                                    onChange={(e) => onChange(e.target.value)}
                                    placeholder="Enter category description"
                                />
                                {error && <p className="text-xs text-red-500">{error}</p>}
                            </div>
                        ),
                    },
                    {
                        key: "sort_order",
                        label: "Sort Order",
                        default: 0,
                        schema: z
                            .preprocess((val) => Number(val), z.number()
                                .min(0, "Sort Order must be at least 0")
                                .max(64, "Sort Order must be at most 64")
                            ),
                        render: ({ value, onChange, error }) => (
                            <div className="flex flex-col gap-2">
                                <NumberInputWithButtons
                                    value={value !== undefined ? Number(value) : undefined}
                                    onChange={(number) => onChange(number)}
                                    placeholder="Enter sort order"
                                    minValue={0}
                                    maxValue={64}
                                />
                                {error && <p className="text-xs text-red-500">{error}</p>}
                            </div>
                        ),
                    },
                    {
                        key: "is_active",
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
                    },
                    {
                        key: "statuses",
                        label: "Statuses",
                        render: ({ value = [], onChange, error }) => {
                            const allStatuses = ticketsStatuses; // тип: TicketStatusDto[]

                            const selectedIds = (value as App.DTO.Ticket.TicketStatusDto[] ?? []).map((status) =>
                                status.id.toString()
                            );

                            const handleChange = (ids: string[]) => {
                                const selected = allStatuses.filter((s) =>
                                    ids.includes(s.id.toString())
                                );
                                onChange(selected);
                            };

                            return (
                                <div className="flex flex-col gap-2">
                                    <MultiSelect
                                        maxSelected={6}
                                        placeholder="Select statuses"
                                        options={allStatuses.map((status) => ({
                                            value: status.id.toString(),
                                            label: status.name,
                                        }))}
                                        value={selectedIds}
                                        onChange={handleChange}
                                    />
                                    {error && <p className="text-xs text-red-500">{error}</p>}
                                </div>
                            );
                        },
                    }
                ]}
                pagination={{
                    currentPage: ticketsCategories.currentPage,
                    totalPages: ticketsCategories.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
            />
        </AppLayout>
    );
}
