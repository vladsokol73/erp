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
import { Plus } from "lucide-react";
import { z } from "zod";
import useApi from "@/hooks/use-api";
import {route} from "ziggy-js";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import {PermissionsField} from "@/components/admin-panel/permissions-field";
import {MultiSelectField} from "@/components/ticket/fields/multi-select-field";

interface Props {
    users: App.DTO.PaginatedListDto<App.DTO.User.UserListDto>
    roles: App.DTO.User.RoleDto[],
    permissions: App.DTO.User.PermissionDto[]
    countries: App.DTO.CountryDto[],
    tags: App.DTO.Creative.TagDto[],
    operators: App.DTO.OperatorDto[],
    channels: App.DTO.ChannelDto[],
    api_tokens: App.DTO.ApiTokenDto[],
}


export default function TagsPage({ users, roles, permissions, countries, tags, operators, channels, api_tokens }: Props) {

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
        setFilters({ search, page: 1 });
    };

    const handlePageChange = (page: number) => {
        setFilters({ page });
    };

    const crud = useCrudTableState<App.DTO.User.UserListDto>({
        defaultForm: () => ({
            name: "",
        }),
        initialData: users
    });

    const handleCreate = async (data: Partial<App.DTO.User.UserListDto>) => {
        await api.post(
            route('admin-panel.users.create'),
            {
                name: data.name,
                email: data.email,
                password: data.password,
                role_id: data.role?.id,

                api_token_ids: Array.isArray(data.api_token_ids) ? data.api_token_ids.map((id) => Number(id)) : [],

                permissions: data.permissions ?? [],
                available_countries: data.available_countries ?? [],
                available_tags: data.available_tags ?? [],
                available_channels: data.available_channels ?? [],
                available_operators: data.available_operators ?? [],
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

    const handleUpdate  = async (id: number, data: Partial<App.DTO.User.UserListDto>) => {

        await api.put(
            route('admin-panel.users.edit', {userId: id}),
            {
                name: data.name,
                email: data.email,
                password: data.password,
                role_id: data.role?.id,

                // Синхронизируем массив ID API-токенов (передаем пустой массив, чтобы очистить привязки)
                api_token_ids: Array.isArray(data.api_token_ids) ? data.api_token_ids.map((id) => Number(id)) : [],

                permissions: data.permissions,
                available_countries: data.available_countries,
                available_tags: data.available_tags,
                available_channels: data.available_channels,
                available_operators: data.available_operators,
            },
            {
                onSuccess: (data) => {
                    crud.updateItem(id, data.user)
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
            route('admin-panel.users.delete', {userId: id}),
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
            <Head title="Users" />
            <h1 className="text-2xl font-bold mb-4">Users</h1>

            <div className="flex items-center gap-4 mb-4">
                <Button className="!py-6 !px-5" onClick={crud.openCreateModal}>
                    <Plus/>
                    New User
                </Button>

                <div className="w-full">
                    <InputSearch defaultValue={filters.search} onChangeDebounced={handleSearchChange} />
                </div>
            </div>

            <CrudTable<App.DTO.User.UserListDto>
                tableTitle="Users"
                tableDescription="List of users"
                resourceName="user"
                dialogContentHeight={500}
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
                        key: "email",
                        title: "Email",
                    },
                    {
                        key: "role",
                        title: "Role",
                        render: (user) => <Badge>{user.role.name}</Badge>,
                    },
                    {
                        key: "two_factor",
                        title: "2FA",
                        render: (user) => <Badge variant={user.two_factor ? "default" : "secondary"}>{user.two_factor ? 'Active' : 'Inactive'}</Badge>,
                    },
                    {
                        key: "last_login",
                        title: "Last Login",
                        render: (user) => user.last_login ? <DateFormatter variant="short" dateString={user.last_login} /> : <span className="text-muted-foreground text-sm">Never</span>,
                    }
                ]}
                fieldTabs={[
                    {
                        label: "Profile", fields: [
                            {
                                key: "name",
                                label: "Name",
                                schema: z.string().min(1, "Name is required"),
                                render: ({ value, onChange, error }) => (
                                    <div className="flex flex-col gap-2">
                                        <Input
                                            value={value ? String(value) : ""}
                                            onChange={(e) => onChange(e.target.value)}
                                            placeholder="Enter user name"
                                        />
                                        {error && <p className="text-xs text-red-500">{error}</p>}
                                    </div>
                                ),
                            },
                            {
                                key: "email",
                                label: "Email",
                                schema: z.string().email("Invalid email"),
                                render: ({ value, onChange, error }) => (
                                    <div className="flex flex-col gap-2">
                                        <Input
                                            value={value ? String(value) : ""}
                                            onChange={(e) => onChange(e.target.value)}
                                            placeholder="Enter user email"
                                        />
                                        {error && <p className="text-xs text-red-500">{error}</p>}
                                    </div>
                                ),
                            },
                            {
                                key: "password",
                                label: "Password",
                                schema: z
                                    .preprocess((val) => val === "" ? null : val, z.string().min(6, "Password must be at least 6 characters").nullable()),
                                render: ({ value, onChange, error }) => (
                                    <div className="flex flex-col gap-2">
                                        <Input
                                            value={value ? String(value) : ""}
                                            onChange={(e) => onChange(e.target.value)}
                                            placeholder="Enter user password"
                                        />
                                        {error && <p className="text-xs text-red-500">{error}</p>}
                                    </div>
                                ),
                            },

                            {
                                key: "api_token_ids",
                                label: "API Tokens",
                                render: ({ value, onChange, error }) => {
                                    const selected = Array.isArray(value)
                                        ? (value as (string | number)[]).map(String)
                                        : [];
                                    const options = api_tokens.map((t) => ({ label: t.email, value: String(t.id) }));

                                    return (
                                        <MultiSelectField
                                            value={selected}
                                            onChange={(selectedIds) => onChange(selectedIds)}
                                            options={options}
                                            error={error}
                                        />
                                    );
                                }
                            },

                            {
                                key: "role",
                                label: "Role",

                                schema: z.object({
                                    id: z.number(),
                                    name: z.string(),
                                }).refine((val) => !!val.name, { message: "Role is required" }),

                                render: ({ value, onChange, error, form }) => {
                                    const selectedName = (value as App.DTO.User.RoleDto)?.name ?? "";

                                    return (
                                        <div className="flex flex-col gap-2">
                                            <Select
                                                value={selectedName}
                                                onValueChange={(selectedName) => {
                                                    const selectedRole = roles.find((r) => r.name === selectedName);
                                                    if (selectedRole) {
                                                        onChange(selectedRole);
                                                    }
                                                }}
                                            >
                                                <SelectTrigger className="w-full capitalize">
                                                    <SelectValue placeholder="Select role" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    {roles.map((role) => (
                                                        <SelectItem
                                                            className="capitalize"
                                                            key={role.id}
                                                            value={role.name}
                                                        >
                                                            {role.name}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                            {error && <p className="text-xs text-red-500">{error}</p>}
                                        </div>
                                    );
                                }
                            }
                        ]
                    },
                    {
                        label: "Permissions", fields: [
                            {
                                key: "permissions",
                                render: ({ value, onChange, error }) => (
                                    <PermissionsField
                                        value={Array.isArray(value) ? value as App.DTO.User.PermissionDto[] : []}
                                        onChange={onChange}
                                        permissions={permissions}
                                        error={error}
                                    />
                                )

                            }
                        ]
                    },
                    {
                        label: "Creatives", fields: [
                            {
                                key: "available_countries",
                                render: ({ value, onChange, error }) => {
                                    const options = [
                                        { label: "All countries", value: "all" },
                                        ...countries.map((country) => ({
                                            label: country.name,
                                            value: country.id.toString(),
                                        })),
                                    ];

                                    const handleChange = (selected: string[]) => {
                                        const stringArray = Array.isArray(value) ? value as string[] : [];
                                        const isAllSelected = selected.includes("all");
                                        const wasAllSelected = stringArray.includes("all");

                                        if (isAllSelected && !wasAllSelected) {
                                            onChange(["all"]);
                                            return;
                                        }

                                        const result = selected.filter((v) => v !== "all");
                                        onChange(result.length > 0 ? result : null);
                                    };

                                    return (
                                        <MultiSelectField
                                            label="Available countries"
                                            value={Array.isArray(value) ? value as string[] : []}
                                            onChange={handleChange}
                                            options={options}
                                            error={error}
                                        />
                                    );
                                }
                            },
                            {
                                key: "available_tags",
                                render: ({ value, onChange, error }) => {
                                    const options = [
                                        { label: "All tags", value: "all" },
                                        ...tags.map((tag) => ({
                                            label: tag.name,
                                            value: tag.id.toString(),
                                        })),
                                    ];

                                    const handleChange = (selected: string[]) => {
                                        const stringArray = Array.isArray(value) ? value as string[] : [];
                                        const isAllSelected = selected.includes("all");
                                        const wasAllSelected = stringArray.includes("all");

                                        if (isAllSelected && !wasAllSelected) {
                                            onChange(["all"]);
                                            return;
                                        }

                                        const result = selected.filter((v) => v !== "all");
                                        onChange(result.length > 0 ? result : null);
                                    };

                                    return (
                                        <MultiSelectField
                                            label="Available tags"
                                            value={Array.isArray(value) ? value as string[] : []}
                                            onChange={handleChange}
                                            options={options}
                                            error={error}
                                        />
                                    );
                                }
                            }
                        ]
                    },
                    {
                        label: "Projects", fields: [
                            {
                                hidden: [
                                    {
                                        custom: ({ form }) =>
                                            !Array.isArray(form.permissions) ||
                                            !form.permissions.some((dto) => dto?.name === "operators.show"),
                                    },
                                ],
                                key: "available_operators",
                                render: ({ value, onChange, error }) => {
                                    const options = [
                                        { label: "All operators", value: "all" },
                                        ...operators.map((operator) => ({
                                            label: operator.name ?? "Unnamed: " + operator.id,
                                            value: operator.id.toString(),
                                        })),
                                    ];

                                    const handleChange = (selected: string[]) => {
                                        const stringArray = Array.isArray(value) ? value as string[] : [];
                                        const isAllSelected = selected.includes("all");
                                        const wasAllSelected = stringArray.includes("all");

                                        if (isAllSelected && !wasAllSelected) {
                                            onChange(["all"]);
                                            return;
                                        }

                                        const result = selected.filter((v) => v !== "all");
                                        onChange(result.length > 0 ? result : null);
                                    };

                                    return (
                                        <MultiSelectField
                                            label="Available operators"
                                            value={Array.isArray(value) ? value as string[] : []}
                                            onChange={handleChange}
                                            options={options}
                                            error={error}
                                        />
                                    );
                                }
                            },
                            {
                                hidden: [
                                    {
                                        and: [
                                            {
                                                custom: ({ form }) =>
                                                    !Array.isArray(form.permissions) ||
                                                    !form.permissions.some((dto) => dto?.name === "clients.show"),
                                            },
                                            {
                                                custom: ({ form }) =>
                                                    !Array.isArray(form.permissions) ||
                                                    !form.permissions.some((dto) => dto?.name === "operators.show"),
                                            },
                                        ]
                                    }
                                ],
                                key: "available_channels",
                                render: ({ value, onChange, error }) => {
                                    const options = [
                                        { label: "All channels", value: "all" },
                                        ...channels.map((channel) => ({
                                            label: channel.name ?? "Unnamed: " + channel.id,
                                            value: channel.id.toString(),
                                        })),
                                    ];

                                    const handleChange = (selected: string[]) => {
                                        const stringArray = Array.isArray(value) ? value as string[] : [];
                                        const isAllSelected = selected.includes("all");
                                        const wasAllSelected = stringArray.includes("all");

                                        if (isAllSelected && !wasAllSelected) {
                                            onChange(["all"]);
                                            return;
                                        }

                                        const result = selected.filter((v) => v !== "all");
                                        onChange(result.length > 0 ? result : null);
                                    };

                                    return (
                                        <MultiSelectField
                                            label="Available channels"
                                            value={Array.isArray(value) ? value as string[] : []}
                                            onChange={handleChange}
                                            options={options}
                                            error={error}
                                        />
                                    );
                                }
                            }

                        ]
                    },
                ]}
                pagination={{
                    currentPage: users.currentPage,
                    totalPages: users.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
            />
        </AppLayout>
    );
}
