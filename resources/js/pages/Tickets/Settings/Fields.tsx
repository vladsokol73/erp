import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import InputSearch from "@/components/ui/input-search";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import { CrudTable } from "@/components/common/crud-table";
import { useCrudTableState } from "@/hooks/use-crud-table-state";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import DateFormatter from "@/components/common/date-formatter";
import { Button } from "@/components/ui/button";
import {
    Plus,
    Mail,
    Link,
    Text,
    Hash,
    Calendar,
    FileText,
    Search,
    Ban
} from "lucide-react";
import { z } from "zod";
import useApi from "@/hooks/use-api";
import {route} from "ziggy-js";
import { Checkbox } from "@/components/ui/checkbox";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { OptionsField } from "@/components/ticket/settings/options-field"
import {ValidationRulesEditor} from "@/components/ticket/settings/validation-rules-editor";
import {HorizontalTabs} from "@/components/common/horizontal-tabs";

interface Props {
    ticketsFormFields: App.DTO.PaginatedListDto<App.DTO.Ticket.TicketFormFieldListDto>
}


const FIELD_TYPE_VALUES = [
    "text",
    "number",
    "select",
    "multiselect",
    "country",
    "textarea",
    "date",
    "file",
   // "checkbox",
    "project",
] as const;

type FieldTypeEnum = typeof FIELD_TYPE_VALUES[number];


export default function Fields({ ticketsFormFields }: Props) {

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

    const crud = useCrudTableState<App.DTO.Ticket.TicketFormFieldListDto>({
        defaultForm: () => ({
            name: "",
        }),
        initialData: ticketsFormFields
    });

    const handleCreate = async (data: Partial<App.DTO.Ticket.TicketFormFieldListDto>) => {
        await api.post(
            route('tickets.settings.fields.create'),
            {
                name: data.name,
                label: data.label,
                type: data.type,
                is_required: data.is_required,
                options: data.options,
                validation_rules: data.validation_rules,
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

    const handleUpdate  = async (id: number, data: Partial<App.DTO.Ticket.TicketFormFieldListDto>) => {
        await api.put(
            route('tickets.settings.fields.update', {fieldsId: id}),
            {
                name: data.name,
                label: data.label,
                type: data.type,
                is_required: data.is_required,
                options: data.options,
                validation_rules: data.validation_rules,
            },
            {
                onSuccess: (data) => {
                    crud.updateItem(id, data.formField)
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
            route('tickets.settings.fields.delete', {fieldsId  : id}),
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

            <Head title="Form Fields" />

            <div className="flex items-center gap-4 my-4">
                <Button className="!py-5 !px-4" onClick={crud.openCreateModal}>
                    <Plus/>
                    New Form Field
                </Button>

                <div className="w-full">
                    <InputSearch defaultValue={filters.search} onChangeDebounced={handleSearchChange} />
                </div>
            </div>

            <CrudTable<App.DTO.Ticket.TicketFormFieldListDto>
                tableTitle="Form Fields"
                tableDescription="List of form fields"
                resourceName="form field"
                crudState={crud}
                onCreate={handleCreate}
                onUpdate={handleUpdate}
                onDelete={handleDelete}
                dialogContentHeight={450}
                columns={[
                    {
                        key: "id",
                        title: "ID",
                    },
                    {
                        key: "label",
                        title: "Label",
                        render: (item) =>
                            <div className="flex flex-col gap-1">
                                <span>{item.label}</span>
                                <span className="text-xs text-muted-foreground">{item.name}</span>
                            </div>
                    },
                    {
                        key: "type",
                        title: "Type",
                        render: (item) => <Badge className="capitalize" variant="secondary">{item.type}</Badge>
                    },
                    {
                        key: "validation_rules",
                        title: "Validation Rules",
                        render: (item) => {
                            const icons: Record<string, JSX.Element> = {
                                email: <Mail className="w-4 h-4 text-primary" />,
                                url: <Link className="w-4 h-4 text-primary" />,
                                max_length: <Text className="w-4 h-4 text-primary" />,
                                min_length: <Text className="w-4 h-4 text-primary" />,
                                max_number: <Hash className="w-4 h-4 text-primary" />,
                                min_number: <Hash className="w-4 h-4 text-primary" />,
                                max_date: <Calendar className="w-4 h-4 text-primary" />,
                                min_date: <Calendar className="w-4 h-4 text-primary" />,
                                file_type: <FileText className="w-4 h-4 text-primary" />,
                                contains: <Search className="w-4 h-4 text-primary" />,
                                not_contains: <Ban className="w-4 h-4 text-primary" />,
                            }

                            const labels: Record<string, string> = {
                                email: "Email",
                                url: "URL",
                                max_length: "Max Length",
                                min_length: "Min Length",
                                max_number: "Max Number",
                                min_number: "Min Number",
                                max_date: "Max Date",
                                min_date: "Min Date",
                                file_type: "File Type",
                                contains: "Contains",
                                not_contains: "Not Contains",
                            }

                            const rules = item.validation_rules ?? []

                            return (
                                <div className="flex flex-col gap-1 text-muted-foreground text-xs">
                                    {rules.length === 0 ? (
                                        <span className="text-muted-foreground">Not specified</span>
                                    ) : (
                                        rules.map((rule, index) => {
                                            const icon = icons[rule.type] ?? null
                                            const label = labels[rule.type] ?? rule.type
                                            const value =
                                                rule.value !== null && rule.value !== undefined
                                                    ? `: ${rule.value}`
                                                    : ""

                                            return (
                                                <span key={index} className="flex items-center gap-1">
                                                            {icon}
                                                    {label}
                                                    {value}
                                                        </span>
                                            )
                                        })
                                    )}
                                </div>
                            )
                        },
                    },
                    {
                        key: "is_required",
                        title: "Is Required",
                        render: (item) => <Badge variant={`${item.is_required ? "default" : "secondary"}`} >{item.is_required ? "Yes" : "No"}</Badge>
                    },
                ]}
                fieldTabs={[
                    {
                        label: "General", fields: [
                            {
                                key: "name",
                                label: "Name",
                                schema: z.string().min(1, "Name is required"),
                                render: ({ value, onChange, error }) => (
                                    <div className="flex flex-col gap-2">
                                        <Input
                                            value={value?.toString()}
                                            onChange={(e) => onChange(e.target.value)}
                                            placeholder="Enter field name"
                                        />
                                        {error && <p className="text-xs text-red-500">{error}</p>}
                                    </div>
                                ),
                            },
                            {
                                key: "label",
                                label: "Label",
                                schema: z.string().min(1, "Label is required"),
                                render: ({ value, onChange, error }) => (
                                    <div className="flex flex-col gap-2">
                                        <Input
                                            value={value?.toString()}
                                            onChange={(e) => onChange(e.target.value)}
                                            placeholder="Enter field label"
                                        />
                                        {error && <p className="text-xs text-red-500">{error}</p>}
                                    </div>
                                ),
                            },
                            {
                                key: "type",
                                label: "Type",
                                schema: z.enum(FIELD_TYPE_VALUES),
                                render: ({ value, onChange, error }) => (
                                    <div className="flex flex-col gap-2">
                                        <Select
                                            value={value?.toString()}
                                            onValueChange={(e) => onChange(e)}
                                            defaultValue={value?.toString()}
                                        >
                                            <SelectTrigger className="w-full capitalize">
                                                <SelectValue placeholder="Select field type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {FIELD_TYPE_VALUES.map((type) => (
                                                    <SelectItem className="capitalize" key={type} value={type}>
                                                        {type}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {error && <p className="text-xs text-red-500">{error}</p>}
                                    </div>
                                ),
                            },
                            {
                                key: "options",
                                label: "Options",
                                hidden: [
                                    {
                                        and: [
                                            { when: "type", isNot: "select" },
                                            { when: "type", isNot: "multiselect" },
                                        ],
                                    },
                                ],
                                render: ({ value, onChange, error }) => (
                                    <OptionsField
                                        value={Array.isArray(value) ? value as string[] : []}
                                        onChange={(opts) => onChange(opts)}
                                        error={error}
                                    />
                                ),
                            },
                            {
                                key: "is_required",
                                default: false,
                                render: ({ value, onChange, error }) => (
                                    <div className="flex gap-2 items-center">
                                        <Checkbox
                                            id="is_required"
                                            checked={value === true}
                                            onCheckedChange={(checked) => onChange(!!checked)}
                                        />
                                        <label
                                            htmlFor="is_required"
                                            className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                        >
                                            Is Required
                                        </label>
                                        {error && <p className="text-xs text-red-500">{error}</p>}
                                    </div>
                                ),
                            },
                        ]
                    },
                    {
                        label: "Validation", fields: [
                            {
                                key: "validation_rules",
                                label: "Validation Rules",
                                render: ({ value, onChange, error }) => (
                                    <div className="h-[350px] overflow-y-auto pr-2">
                                        <ValidationRulesEditor
                                            value={value as App.DTO.Ticket.ValidationRuleDto[]}
                                            onChange={(rules) => onChange(rules)}
                                            error={error}
                                        />
                                    </div>
                                ),
                            },
                        ]
                    },
                ]}
                pagination={{
                    currentPage: ticketsFormFields.currentPage,
                    totalPages: ticketsFormFields.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
            />
        </AppLayout>
    );
}
