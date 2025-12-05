import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import InputSearch from "@/components/ui/input-search";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import { CrudTable } from "@/components/common/crud-table";
import { useCrudTableState } from "@/hooks/use-crud-table-state";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Plus } from "lucide-react";
import { z } from "zod";
import useApi from "@/hooks/use-api";
import {route} from "ziggy-js";
import { ColorPicker } from "@/components/common/color-picker";
import { Checkbox } from "@/components/ui/checkbox";
import { NumberInputWithButtons } from "@/components/common/numbe-iInput-with-buttons";
import {HorizontalTabs} from "@/components/common/horizontal-tabs";

interface Props {
    ticketsStatuses: App.DTO.PaginatedListDto<App.DTO.Ticket.TicketStatusesListDto>
}

export default function Statuses({ ticketsStatuses }: Props) {

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

    const crud = useCrudTableState<App.DTO.Ticket.TicketStatusesListDto>({
        defaultForm: () => ({
            name: "",
        }),
        initialData: ticketsStatuses
    });

    const handleCreate = async (data: Partial<App.DTO.Ticket.TicketStatusesListDto>) => {
        await api.post(
            route('tickets.settings.statuses.create'),
            {
                name: data.name,
                color: data.color,
                is_default: data.is_default,
                is_approval: data.is_approval,
                is_final: data.is_final,
                sort_order: data.sort_order
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

    const handleUpdate  = async (id: number, data: Partial<App.DTO.Ticket.TicketStatusesListDto>) => {
        await api.put(
            route('tickets.settings.statuses.update', {statusId: id}),
            {
                name: data.name,
                color: data.color,
                is_default: data.is_default,
                is_approval: data.is_approval,
                is_final: data.is_final,
                sort_order: data.sort_order
            },
            {
                onSuccess: (data) => {
                    crud.updateItem(id, data.status)
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
            route('tickets.settings.statuses.delete', {statusId: id}),
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

            <Head title="Statuses" />

            <div className="flex items-center gap-4 my-4">
                <Button className="!py-5 !px-4" onClick={crud.openCreateModal}>
                    <Plus/>
                    New Status
                </Button>

                <div className="w-full">
                    <InputSearch defaultValue={filters.search} onChangeDebounced={handleSearchChange} />
                </div>
            </div>

            <CrudTable<App.DTO.Ticket.TicketStatusesListDto>
                tableTitle="Statuses"
                tableDescription={
                    <p className="text-sm text-muted-foreground">
                        List of statuses
                    </p>
                }
                resourceName="status"
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
                        key: "color",
                        title: "Color",
                        render: (item) => <Badge className={`bg-${item.color}`} >{item.color}</Badge>
                    },
                    {
                        key: "is_default",
                        title: "Default",
                        render: (item) => <Badge variant={`${item.is_default ? "default" : "secondary"}`} >{item.is_default ? "Yes" : "No"}</Badge>
                    },
                    {
                        key: "is_approval",
                        title: "Approval",
                        render: (item) => <Badge variant={`${item.is_approval ? "default" : "secondary"}`} >{item.is_approval ? "Yes" : "No"}</Badge>
                    },
                    {
                        key: "is_final",
                        title: "Final",
                        render: (item) => <Badge variant={`${item.is_final ? "default" : "secondary"}`}  >{item.is_final ? "Yes" : "No"}</Badge>
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
                                    placeholder="Enter status name"
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
                        key: "color",
                        label: "Color",
                        schema: z.string().min(1, "Color is required"),
                        render: ({ value, onChange, error }) => (
                            <ColorPicker value={value?.toString() ?? ""} onChange={onChange} error={error} />
                        )
                    },
                    {
                        key: "is_default",
                        default: false,
                        render: ({ value, onChange, error }) => (
                            <div className="flex gap-2 items-center">
                                <Checkbox
                                    id="is_default"
                                    checked={value === true}
                                    onCheckedChange={(checked) => onChange(!!checked)}
                                />
                                <label
                                    htmlFor="is_default"
                                    className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >
                                    Is Default
                                </label>
                                {error && <p className="text-xs text-red-500">{error}</p>}
                            </div>
                        ),
                    },
                    {
                      key: "is_approval",
                      default: false,
                      render: ({ value, onChange, error }) => (
                          <div className="flex gap-2 items-center">
                              <Checkbox
                                  id="is_approval"
                                  checked={value === true}
                                  onCheckedChange={(checked) => onChange(!!checked)}
                              />
                              <label
                                  htmlFor="is_approval"
                                  className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                              >
                                  Is Approval
                              </label>
                              {error && <p className="text-xs text-red-500">{error}</p>}
                          </div>
                      ),
                    },
                    {
                        key: "is_final",
                        default: false,
                        render: ({ value, onChange, error }) => (
                            <div className="flex gap-2 items-center">
                                <Checkbox
                                    id="is_final"
                                    checked={value === true}
                                    onCheckedChange={(checked) => onChange(!!checked)}
                                />
                                <label
                                    htmlFor="is_final"
                                    className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                >
                                    Is Final
                                </label>
                                {error && <p className="text-xs text-red-500">{error}</p>}
                            </div>
                        ),
                    },
                ]}
                pagination={{
                    currentPage: ticketsStatuses.currentPage,
                    totalPages: ticketsStatuses.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
            />
        </AppLayout>
    );
}
