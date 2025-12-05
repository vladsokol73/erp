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
import {CommentCreative} from "@/components/library/types";
import { ColorPicker } from "@/components/common/color-picker";
import {usePerPageStore} from "@/store/usePerPageStore";

interface Props {
    tags: App.DTO.PaginatedListDto<App.DTO.Creative.TagListDto>
}

export default function TagsPage({ tags }: Props) {

    const api = useApi();

    const perPage = usePerPageStore((s) => s.getPerPage('tags', 10));

    const [filters, setFilters] = useInertiaUrlState(
        {
            search: "",
            page: 1,
            perPage: perPage
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

    const handlePerPageChange = (page: number) => {
        usePerPageStore.getState().setPerPage('tags', page);
        setFilters({ perPage: page });
    };

    const crud = useCrudTableState<App.DTO.Creative.TagListDto>({
        defaultForm: () => ({
            name: "",
            style: "",
        }),
        initialData: tags
    });

    const handleCreate = async (data: Partial<App.DTO.Creative.TagListDto>) => {
        await api.post(
            route('creatives.tags.create'),
            {
                name: data.name,
                style: data.style
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

    const handleUpdate  = async (id: number, data: Partial<App.DTO.Creative.TagListDto>) => {
        await api.put(
            route('creatives.tags.update', {tagId: id}),
            {
                name: data.name,
                style: data.style
            },
            {
                onSuccess: (data) => {
                    crud.updateItem(id, data.tag)
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
            route('creatives.tags.delete', {tagId: id}),
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
            <Head title="Tags" />
            <h1 className="text-2xl font-bold mb-4">Tags</h1>

            <div className="flex items-center gap-4 mb-4">
                <Button className="!py-6 !px-5" onClick={crud.openCreateModal}>
                    <Plus/>
                    New Tag
                </Button>

                <div className="w-full">
                    <InputSearch defaultValue={filters.search} onChangeDebounced={handleSearchChange} />
                </div>
            </div>

            <CrudTable<App.DTO.Creative.TagListDto>
                tableTitle="Tags"
                tableDescription="List of tags"
                resourceName="tag"
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
                        key: "style",
                        title: "Color",
                        render: (tag) => (
                            <Badge className={`capitalize bg-${tag.style}`}>
                                {tag.style}
                            </Badge>
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
                        key: "name",
                        label: "Name",
                        schema: z.string().min(1, "Name is required"),
                        render: ({ value, onChange, error }) => (
                            <div className="flex flex-col gap-2">
                                <Input
                                    value={value ?? ""}
                                    onChange={(e) => onChange(e.target.value)}
                                    placeholder="Enter tag name"
                                />
                                {error && <p className="text-xs text-red-500">{error}</p>}
                            </div>
                        ),
                    },
                    {
                        key: "style",
                        label: "Color",
                        schema: z.string().min(1, "Color is required"),
                        render: ({ value, onChange, error }) => (
                            <ColorPicker value={value?.toString() ?? ""} onChange={onChange} error={error} />
                        )
                    },
                ]}
                pagination={{
                    currentPage: tags.currentPage,
                    totalPages: tags.lastPage,
                    onPageChange: handlePageChange,
                    paginationItemsToDisplay: 3,
                }}
                perPage={{
                    value: filters.perPage??10,
                    onChange: handlePerPageChange,
                    options: [10, 25, 50],
                }}
            />
        </AppLayout>
    );
}
