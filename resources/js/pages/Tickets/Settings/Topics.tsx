import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import InputSearch from "@/components/ui/input-search";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import { CrudTable } from "@/components/common/crud-table";
import { useCrudTableState } from "@/hooks/use-crud-table-state";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import {BadgeCheck, Plus, Shield, ShieldCheck, Tag, User } from "lucide-react";
import { z } from "zod";
import useApi from "@/hooks/use-api";
import {route} from "ziggy-js";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import {Checkbox} from "@/components/ui/checkbox";
import {Textarea} from "@/components/ui/textarea";
import {NumberInputWithButtons} from "@/components/common/numbe-iInput-with-buttons";
import { ResponsibleUsersSelector } from "@/components/ticket/settings/responsible-users-selector";
import { SortableField } from "@/components/ticket/settings/sortable-field";
import React from "react";


import {
    DndContext,
    closestCenter,
    KeyboardSensor,
    PointerSensor,
    useSensor,
    useSensors,
    DragEndEvent,
} from "@dnd-kit/core";
import {sortableKeyboardCoordinates, arrayMove, verticalListSortingStrategy, SortableContext} from "@dnd-kit/sortable";
import {HorizontalTabs} from "@/components/common/horizontal-tabs";
import { SearchableSelectField } from "@/components/ticket/fields/searchable-select-field";

interface Props {
    ticketsTopics: App.DTO.PaginatedListDto<App.DTO.Ticket.TicketTopicListDto>,
    topicCategories: App.DTO.Ticket.TicketCategoryDto[],
    allUsers: App.DTO.User.UserDto[],
    allRoles: App.DTO.User.RoleDto[],
    allPermissions: App.DTO.User.PermissionDto[],
    allFormFields: App.DTO.Ticket.TicketFormFieldDto[],
}

export default function Statuses({
                                     ticketsTopics,
                                     topicCategories,
                                     allUsers,
                                     allRoles,
                                     allPermissions,
                                     allFormFields,
}: Props) {
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

    const crud = useCrudTableState<App.DTO.Ticket.TicketTopicListDto>({
        defaultForm: () => ({
            name: "",
        }),
        initialData: ticketsTopics
    });

    const handleCreate = async (data: Partial<App.DTO.Ticket.TicketTopicListDto>) => {
        await api.post(
            route('tickets.settings.topic.create'),
            {
                name: data.name,
                description: data.description,
                category_id: data.category?.id,
                sort_order: data.sort_order,
                is_active: data.is_active,
                approval: data.approval,
                responsible: data.responsible,
                fields: data.fields,
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

    const handleUpdate  = async (id: number, data: Partial<App.DTO.Ticket.TicketTopicListDto>) => {
        await api.put(
            route('tickets.settings.topic.update', {topicId: id}),
            {
                name: data.name,
                description: data.description,
                category_id: data.category?.id,
                sort_order: data.sort_order,
                is_active: data.is_active,
                approval: data.approval,
                responsible: data.responsible,
                fields: data.fields,
            },
            {
                onSuccess: (data) => {
                    crud.updateItem(id, data.topic)
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
            route('tickets.settings.topic.delete', {topicId: id}),
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

            <Head title="Topics" />

            <div className="flex items-center gap-4 my-4">
                <Button className="!py-5 !px-4" onClick={crud.openCreateModal}>
                    <Plus/>
                    New Topic
                </Button>

                <div className="w-full">
                    <InputSearch defaultValue={filters.search} onChangeDebounced={handleSearchChange} />
                </div>
            </div>

            <CrudTable<App.DTO.Ticket.TicketTopicListDto>
                tableTitle="Topics"
                tableDescription="List of topics"
                resourceName="topics"
                crudState={crud}
                onCreate={handleCreate}
                onUpdate={handleUpdate}
                onDelete={handleDelete}
                dialogContentHeight={500}
                columns={[
                    {
                        key: "id",
                        title: "ID",
                    },
                    {
                        key: "name",
                        title: "Name",
                        render: (item) => <span className="block truncate max-w-32">{item.name}</span>
                    },
                    {
                        key: "category.name",
                        title: "Category",
                        render: (item) => <span className="block truncate max-w-32">{item.category.name}</span>
                    },
                    {
                        key: "description",
                        title: "Description",
                        render: (item) => item.description ? <span className="block truncate max-w-32">{item.description}</span> : <span className="text-muted-foreground">No description</span>
                    },
                    {
                        key: "approval",
                        title: "Approval",
                        render: (item) => {
                            const icons: Record<string, JSX.Element> = {
                                User: <User className="w-4 h-4 text-primary" />,
                                Role: <Tag className="w-4 h-4 text-primary" />,
                                Permission: <Shield className="w-4 h-4 text-primary" />,
                            };

                            return (
                                <div className="flex flex-col gap-1 text-muted-foreground text-xs">
                                    {item.approval.map((res, index) => {
                                        const icon = res.responsible_model_name && icons[res.responsible_model_name] || null;

                                        return (
                                            <span key={index} className="flex items-center gap-1">
                                                        {icon && <span>{icon}</span>}
                                                {res.responsible_title}
                                                    </span>
                                        );
                                    })}
                                </div>
                            );
                        },
                    },
                    {
                        key: "responsible",
                        title: "Responsible",
                        render: (item) => {
                            const icons: Record<string, JSX.Element> = {
                                User: <User className="w-4 h-4 text-primary" />,
                                Role: <Tag className="w-4 h-4 text-primary" />,
                                Permission: <Shield className="w-4 h-4 text-primary" />,
                            };

                            return (
                                <div className="flex flex-col gap-1 text-muted-foreground text-xs">
                                    {item.responsible.map((res, index) => {
                                        const icon = res.responsible_model_name && icons[res.responsible_model_name] || null;

                                        return (
                                            <span key={index} className="flex items-center gap-1">
                                                        {icon && <span>{icon}</span>}
                                                {res.responsible_title}
                                                    </span>
                                        );
                                    })}
                                </div>
                            );
                        },
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
                fieldTabs={[
                    { label: "General", fields: [
                            {
                                key: "category",
                                label: "Category",
                                render: ({ value, onChange, error }) => {
                                    const allCategories = topicCategories; // тип: TicketCategoryDto[]

                                    const selectedId = (value as App.DTO.Ticket.TicketCategoryDto)?.id?.toString();

                                    const handleChange = (selectedId: string) => {
                                        const selected = allCategories.find((cat) => cat.id.toString() === selectedId);
                                        if (selected) {
                                            onChange(selected);
                                        }
                                    };

                                    return (
                                        <div className="flex flex-col gap-2">
                                            <Select value={selectedId} onValueChange={handleChange}>
                                                <SelectTrigger className="w-full">
                                                    <SelectValue placeholder="Select a category" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {allCategories.map((cat) => (
                                                        <SelectItem key={cat.id} value={cat.id.toString()}>
                                                            {cat.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            {error && <p className="text-xs text-red-500">{error}</p>}
                                        </div>
                                    );
                                },
                            },
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
                            }
                        ]
                    },
                    {
                        label: "Responsible", fields: [
                            {
                                key: "approval",
                                label: "Approval User",
                                schema: z.array(z.any()).min(1, "At least one approval user must be selected"),
                                render: ({ value, onChange, error }) => (
                                    <ResponsibleUsersSelector
                                        value={value as App.DTO.Ticket.TicketResponsibleUserDto[]}
                                        onChange={arr =>
                                            onChange(
                                                arr.map(item => ({
                                                    ...item,
                                                    responsible_id: typeof item.responsible_id === "string"
                                                        ? Number(item.responsible_id) || null
                                                        : item.responsible_id
                                                }))
                                            )
                                        }
                                        error={error}
                                        allUsers={allUsers}
                                        allRoles={allRoles}
                                        allPermissions={allPermissions}
                                        addLabel="Add Approval"
                                    />
                                )
                            },
                            {
                                key: "responsible",
                                label: "Responsible Users",
                                schema: z.array(z.any()).min(1, "At least one responsible user must be selected"),
                                render: ({ value, onChange, error }) => (
                                    <ResponsibleUsersSelector
                                        value={value as App.DTO.Ticket.TicketResponsibleUserDto[]}
                                        onChange={arr =>
                                            onChange(
                                                arr.map(item => ({
                                                    ...item,
                                                    responsible_id: typeof item.responsible_id === "string"
                                                        ? Number(item.responsible_id) || null
                                                        : item.responsible_id
                                                }))
                                            )
                                        }
                                        error={error}
                                        allUsers={allUsers}
                                        allRoles={allRoles}
                                        allPermissions={allPermissions}
                                        addLabel="Add Responsible"
                                    />
                                )
                            }
                        ]
                    },
                    {
                        label: "Form Fields",
                        fields: [
                            {
                                key: "fields",
                                label: "Fields",
                                render: ({ value, onChange, error }) => {
                                    const allFields = allFormFields;
                                    const selectedFields = (value as App.DTO.Ticket.TicketFormFieldDto[]) ?? [];
                                    const selectedIds = selectedFields.map((f) => f.id.toString());

                                    const [selectedId, setSelectedId] = React.useState<string>("");

                                    const handleAdd = () => {
                                        if (!selectedId) return;
                                        const fieldToAdd = allFields.find(f => f.id.toString() === selectedId);
                                        if (!fieldToAdd) return;
                                        if (selectedFields.some(f => f.id === fieldToAdd.id)) return;

                                        onChange([...selectedFields, fieldToAdd]);
                                        setSelectedId("");
                                    };

                                    const handleRemove = (idToRemove: number) => {
                                        const updated = selectedFields.filter((f) => f.id !== idToRemove);
                                        onChange(updated);
                                    };

                                    const sensors = useSensors(
                                        useSensor(PointerSensor),
                                        useSensor(KeyboardSensor, {
                                            coordinateGetter: sortableKeyboardCoordinates,
                                        })
                                    );

                                    const handleDragEnd = (event: DragEndEvent) => {
                                        const { active, over } = event;
                                        if (!over || active.id === over.id) return;

                                        const oldIndex = selectedFields.findIndex((f) => f.id.toString() === active.id.toString());
                                        const newIndex = selectedFields.findIndex((f) => f.id.toString() === over.id.toString());

                                        const reordered = arrayMove(selectedFields, oldIndex, newIndex);
                                        onChange(reordered);
                                    };

                                    return (
                                        <div className="flex flex-col gap-3">
                                            <div className="flex items-center gap-2">
                                                <SearchableSelectField
                                                    value={selectedId ? Number(selectedId) : null}
                                                    onChange={(id) => setSelectedId(id?.toString() ?? "")}
                                                    options={allFields
                                                        .filter((f) => !selectedIds.includes(f.id.toString()))
                                                        .map((f) => ({
                                                            id: f.id,
                                                            label: `${f.label} (${f.type})`,
                                                        }))}
                                                    placeholder="Select a field"
                                                />
                                                <Button type="button" onClick={handleAdd}>
                                                    <Plus className="w-4 h-4 mr-1" /> Add
                                                </Button>
                                            </div>

                                            <div className="overflow-y-auto h-[360px] pr-2 overflow-hidden">
                                                <DndContext
                                                    sensors={sensors}
                                                    collisionDetection={closestCenter}
                                                    onDragEnd={handleDragEnd}
                                                >
                                                    <SortableContext
                                                        items={selectedFields.map((f) => f.id.toString())}
                                                        strategy={verticalListSortingStrategy}
                                                    >
                                                        <div className="flex flex-col gap-4">
                                                            {selectedFields.map((field) => (
                                                                <SortableField key={field.id} field={field} onRemove={handleRemove} />
                                                            ))}
                                                        </div>
                                                    </SortableContext>
                                                </DndContext>
                                            </div>

                                            {error && <p className="text-xs text-red-500">{error}</p>}
                                        </div>
                                    );
                                },
                            },
                        ]
                    },
                ]}
                pagination={{
                    currentPage: ticketsTopics.currentPage,
                    totalPages: ticketsTopics.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
            />
        </AppLayout>
    );
}
