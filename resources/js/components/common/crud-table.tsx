import React, { useState, useEffect, useCallback } from "react";
import { useDebouncedCallback } from "use-debounce";
import { z } from "zod";
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
    CardFooter,
} from "@/components/ui/card";
import {
    Table,
    TableHeader,
    TableRow,
    TableHead,
    TableBody,
    TableCell,
} from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from "@/components/ui/dialog";
import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogCancel,
    AlertDialogAction,
} from "@/components/ui/alert-dialog";
import { Pencil, Trash2 } from "lucide-react";
import { TablePagination } from "@/components/ui/table-pagination";
import { useCrudTableState } from "@/hooks/use-crud-table-state";
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from "@/components/ui/tabs";
import PerPageDropdown from "@/components/common/per-page-dropdown";
import {cn} from "@/lib/utils";

type NestedKeyOf<T> = {
    [K in keyof T & string]: T[K] extends object
        ? `${K}` | `${K}.${NestedKeyOf<T[K]>}`
        : `${K}`;
}[keyof T & string];

function getNestedValue(item: any, key: string): any {
    return key
        .split(".")
        .reduce((acc: any, part: string) => (acc != null ? acc[part] : undefined), item);
}

export interface ColumnConfig<T> {
    key: NestedKeyOf<T>;
    title: string;
    render?: (item: T) => React.ReactNode;
    align?: "left" | "center" | "right";
    cellClassName?: string;
}

export type FieldPredicate<
    T,
    K extends keyof T = keyof T
> = (value: T[K] | undefined,
     form: Partial<T>,
     mode: "create" | "edit") => boolean;

export type BasicCondition<T> =
    | "create"
    | "edit"
    | { when: keyof T; is: T[keyof T] }
    | { when: keyof T; isNot: T[keyof T] }
    | { when: keyof T; predicate: FieldPredicate<T> }
    | { custom: (ctx: { mode: "create" | "edit"; form: Partial<T> }) => boolean };

/** Рекурсивные логические группы */
export type HiddenCondition<T> =
    | { and: HiddenCondition<T>[] }
    | { or: HiddenCondition<T>[] }
    | BasicCondition<T>;

export function evaluateCondition<T>(
    cond: HiddenCondition<T>,
    mode: "create" | "edit",
    form: Partial<T>
): boolean {
    // --- 1. Простые строковые условия ---------------------------------------
    if (cond === "create" || cond === "edit") {
        return cond === mode;
    }

    // --- 2. Полностью кастомная функция --------------------------------------
    if ("custom" in cond) {
        return cond.custom({ mode, form });
    }

    // --- 3. Логические группы AND / OR ---------------------------------------
    if ("and" in cond) {
        return cond.and.every(c => evaluateCondition(c, mode, form));
    }
    if ("or" in cond) {
        return cond.or.some(c => evaluateCondition(c, mode, form));
    }

    // --- 4. Условия, привязанные к конкретному полю --------------------------
    const fieldValue = form[cond.when];

    if ("is" in cond) {
        return fieldValue === cond.is;
    }
    if ("isNot" in cond) {
        return fieldValue !== cond.isNot;
    }
    if ("predicate" in cond) {
        return cond.predicate(fieldValue, form, mode);
    }

    // --- 5. Fallback ----------------------------------------------------------
    return false;
}


export interface CrudField<T> {
    key: keyof T;
    label?: string;
    render: (args: {
        value: T[keyof T] | undefined;
        form: Partial<T>;
        onChange: (value: T[keyof T]) => void;
        error?: string;
    }) => React.ReactNode;
    schema?: z.ZodType<T[keyof T], any, any>;
    hidden?: HiddenCondition<T>[];
    default?: T[keyof T];
}

export interface FieldTab<T> {
    label: string;
    fields: CrudField<T>[];
}

export interface CrudTableProps<T extends { id: number }> {
    tableTitle: string;
    tableDescription?: React.ReactNode;
    resourceName: string;
    columns: ColumnConfig<T>[];
    fields?: CrudField<T>[];
    fieldTabs?: FieldTab<T>[];
    crudState: ReturnType<typeof useCrudTableState<T>>;
    onCreate?: (data: Partial<T>) => Promise<unknown>;
    onUpdate?: (id: number, data: Partial<T>) => Promise<unknown>;
    onDelete?: (id: number) => Promise<unknown>;
    enableEdit?: boolean;
    enableDelete?: boolean;
    actions?: (item: T) => React.ReactNode;
    pagination?: {
        currentPage: number;
        totalPages: number;
        onPageChange: (page: number) => void;
        paginationItemsToDisplay?: number;
    };

    perPage?: {
        value: number;
        onChange: (value: number) => void;
        options?: number[];
        show?: boolean;
    };

    dialogContentHeight?: number;
}

function isFieldVisible<T>(
    field: CrudField<T>,
    mode: "create" | "edit",
    form: Partial<T>
): boolean {
    if (!field.hidden) return true;
    return !field.hidden.some(cond => evaluateCondition(cond, mode, form));
}


export function CrudTable<T extends { id: number }>(props: CrudTableProps<T>) {
    const {
        tableTitle,
        tableDescription,
        resourceName,
        columns,
        fields = [],
        fieldTabs,
        crudState,
        onCreate,
        onUpdate,
        onDelete,
        enableEdit = true,
        enableDelete = true,
        actions,
        pagination,
        dialogContentHeight,
    } = props;

    const canEdit = enableEdit && typeof onUpdate === "function";
    const canDelete = enableDelete && typeof onDelete === "function";
    const hasRowActions = canEdit || canDelete || typeof actions === "function";

    const hasTabs = Array.isArray(fieldTabs) && fieldTabs.length > 0;
    const allFields: CrudField<T>[] = hasTabs
        ? fieldTabs!.flatMap((t) => t.fields)
        : fields;

    const [activeTab, setActiveTab] = useState<string>(
        hasTabs ? fieldTabs![0].label : ""
    );
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [localFormData, setLocalFormData] = useState<Partial<T>>({});
    const [deleteTargetId, setDeleteTargetId] = useState<number | null>(null);

    // 初始化 формы при открытии модалки
    useEffect(() => {
        if (!crudState.dialogOpen) return;

        if (crudState.modalMode === "edit" && crudState.currentItem) {
            setLocalFormData(crudState.formData);
        }

        if (crudState.modalMode === "create") {
            const defaults: Partial<T> = {};
            allFields.forEach((field) => {
                if (field.default !== undefined) {
                    defaults[field.key] = field.default;
                    crudState.setFormValue(field.key, field.default);
                } else {
                    crudState.setFormValue(field.key, undefined as T[keyof T]);
                }
            });
            setLocalFormData(defaults);
            if (hasTabs) setActiveTab(fieldTabs![0].label);
        }
    }, [crudState.dialogOpen, crudState.modalMode]);

    // Дебаунс для обновления формы
    const setFormValueDebounced = useDebouncedCallback(
        (key: keyof T, value: T[keyof T]) => {
            crudState.setFormValue(key, value);
        },
        300
    );
    useEffect(() => () => setFormValueDebounced.cancel(), [setFormValueDebounced]);

    // Фильтрация видимых полей
    const visibleFields = (fieldsList: CrudField<T>[]) =>
        fieldsList.filter((field) =>
            isFieldVisible(field, crudState.modalMode as "create" | "edit", localFormData)
        );

    const handleCloseModal = () => {
        crudState.resetErrors();
        crudState.closeModal();
    };

    const handleConfirmDelete = async () => {
        if (deleteTargetId === null || !onDelete) return;
        await onDelete(deleteTargetId);
        setDeleteTargetId(null);
    };

    // Валидация перед сабмитом
    const validateForm = useCallback((): boolean => {
        setFormValueDebounced.flush();
        crudState.resetErrors();
        const errors: Record<string, string> = {};
        allFields.forEach((field) => {
            if (!field.schema) return;
            const result = field.schema.safeParse(crudState.formData[field.key]);
            if (!result.success) {
                errors[field.key as string] = result.error.errors
                    .map((e) => e.message)
                    .join(", ");
            }
        });
        if (Object.keys(errors).length) {
            crudState.setFieldErrors(errors);
            return false;
        }
        return true;
    }, [allFields, crudState, setFormValueDebounced]);

    const handleSubmit = async () => {
        setFormValueDebounced.flush();
        if (!validateForm()) return;
        setIsSubmitting(true);
        try {
            if (crudState.modalMode === "create" && onCreate) {
                await onCreate(crudState.formData);
            }
            if (
                crudState.modalMode === "edit" &&
                crudState.currentItem &&
                onUpdate
            ) {
                await onUpdate(crudState.currentItem.id, crudState.formData);
            }
            handleCloseModal();
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleCancelDelete = () => setDeleteTargetId(null);

    // Фабрика handler’ов, чтобы не вызывать хуки в map
    const handleChangeFactory = useCallback(
        (key: keyof T) => (val: T[keyof T]) => {
            setLocalFormData((prev) => ({ ...prev, [key]: val }));
            setFormValueDebounced(key, val);
        },
        [setFormValueDebounced]
    );

    const ICON_SIZE = 16;

    return (
        <>
            <Card>
                <CardHeader>
                    <CardTitle>{tableTitle}</CardTitle>
                    <CardDescription>{tableDescription}</CardDescription>
                </CardHeader>
                <CardContent className="px-0 border-y">
                    <Table>
                        <TableHeader className="bg-muted/50">
                            <TableRow>
                                {columns.map<React.ReactNode>((column) => (
                                    <TableHead
                                        key={String(column.key)}
                                        className={`first:pl-6 ${
                                            column.align ? `text-${column.align}` : ""
                                        }`}
                                    >
                                        {column.title}
                                    </TableHead>
                                ))}
                                {hasRowActions && (
                                    <TableHead className="text-right">
                                        <span className="mr-3">Actions</span>
                                    </TableHead>
                                )}
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {crudState.paginated.items.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={columns.length + (hasRowActions ? 1 : 0)}
                                        className="text-center"
                                    >
                                        No data found
                                    </TableCell>
                                </TableRow>
                            ) : (
                                crudState.paginated.items.map((item) => (
                                    <TableRow key={item.id}>
                                        {columns.map<React.ReactNode>((column) => (
                                            <TableCell
                                                key={column.key}
                                                className={[
                                                    "first:pl-6 h-14",
                                                    column.align ? `text-${column.align}` : "",
                                                    column.cellClassName || "",
                                                ]
                                                    .filter(Boolean)
                                                    .join(" ")}
                                            >
                                                {column.render
                                                    ? column.render(item)
                                                    : getNestedValue(item, column.key)}
                                            </TableCell>
                                        ))}
                                        {hasRowActions && (
                                            <TableCell className="text-right whitespace-nowrap">
                                                <div className="flex justify-end gap-2 mr-3">
                                                    {canEdit && (
                                                        <Button
                                                            size="icon"
                                                            variant="outline"
                                                            onClick={() => crudState.openEditModal(item)}
                                                        >
                                                            <Pencil width={ICON_SIZE} height={ICON_SIZE} />
                                                        </Button>
                                                    )}
                                                    {canDelete && (
                                                        <Button
                                                            size="icon"
                                                            variant="outline"
                                                            onClick={() => setDeleteTargetId(item.id)}
                                                        >
                                                            <Trash2
                                                                width={ICON_SIZE}
                                                                height={ICON_SIZE}
                                                                className="text-red-500"
                                                            />
                                                        </Button>
                                                    )}
                                                    {actions?.(item)}
                                                </div>
                                            </TableCell>
                                        )}
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </CardContent>
                {(props.perPage || (pagination?.totalPages && pagination.totalPages > 1)) && (
                    <CardFooter
                        className={cn(
                            "flex flex-col gap-4 justify-center md:flex-row",
                            (props.perPage) ? "md:justify-between" : "md:justify-end"
                        )}
                    >
                        {props.perPage?.show !== false && props.perPage && (
                            <PerPageDropdown
                                initialValue={props.perPage.value}
                                onChange={props.perPage.onChange}
                                options={props.perPage.options ?? [16, 32, 48]}
                            />
                        )}

                        {pagination?.totalPages && pagination.totalPages > 1 && (
                            <TablePagination
                                currentPage={pagination.currentPage}
                                totalPages={pagination.totalPages}
                                onPageChange={pagination.onPageChange}
                                paginationItemsToDisplay={pagination.paginationItemsToDisplay ?? 3}
                            />
                        )}
                    </CardFooter>
                )}



            </Card>

            <Dialog
                open={crudState.dialogOpen}
                onOpenChange={(open) => {
                    if (!open) handleCloseModal();
                }}
            >
                <DialogContent
                    className="max-w-md"
                    aria-disabled={isSubmitting}
                >
                    <DialogHeader>
                        <DialogTitle>
                            {crudState.modalMode === "create"
                                ? `Create ${resourceName}`
                                : `Edit ${resourceName}`}
                        </DialogTitle>
                        <DialogDescription>
                            {crudState.modalMode === "create"
                                ? `Enter information about the new ${resourceName}`
                                : `Edit existing ${resourceName}`}
                        </DialogDescription>
                    </DialogHeader>
                    <div
                        className={`
              flex flex-col gap-4
              ${isSubmitting ? "opacity-50 pointer-events-none" : ""}
            `}
                        style={{ height: dialogContentHeight + "px" }}
                    >
                        {crudState.formError && (
                            <div className="text-red-500 text-sm font-medium truncate max-w-md">
                                {crudState.formError}
                            </div>
                        )}

                        {hasTabs ? (
                            <Tabs value={activeTab} onValueChange={setActiveTab}>
                                <TabsList className="before:bg-border relative h-auto w-full gap-0.5 bg-transparent p-0 before:absolute before:inset-x-0 before:bottom-0 before:h-px">
                                    {fieldTabs!.map((tab) => (
                                        <TabsTrigger
                                            key={tab.label}
                                            value={tab.label}
                                            className="bg-muted overflow-hidden rounded-b-none border-x border-t py-2 data-[state=active]:z-10 data-[state=active]:shadow-none border-muted-foreground/20"
                                        >
                                            {tab.label}
                                        </TabsTrigger>
                                    ))}
                                </TabsList>

                                {fieldTabs!.map((tab) => {
                                    const tabFields = visibleFields(tab.fields);
                                    return (
                                        <TabsContent
                                            key={tab.label}
                                            value={tab.label}
                                            className="flex flex-col gap-4"
                                        >
                                            {tabFields.map((field) => {
                                                const value = localFormData[field.key];
                                                const handleChange = handleChangeFactory(field.key);
                                                return (
                                                    <div
                                                        key={String(field.key)}
                                                        className="space-y-2"
                                                    >
                                                        {field.label && (
                                                            <div className="text-sm font-medium">
                                                                {field.label}
                                                            </div>
                                                        )}
                                                        {field.render({
                                                            value,
                                                            form: localFormData,
                                                            onChange: handleChange,
                                                            error:
                                                                crudState.fieldErrors[
                                                                    field.key as string
                                                                    ],
                                                        })}
                                                    </div>
                                                );
                                            })}
                                        </TabsContent>
                                    );
                                })}
                            </Tabs>
                        ) : (
                            visibleFields(allFields).map((field) => {
                                const value = localFormData[field.key];
                                const handleChange = handleChangeFactory(field.key);
                                return (
                                    <div
                                        key={String(field.key)}
                                        className="space-y-2"
                                    >
                                        {field.label && (
                                            <div className="text-sm font-medium">
                                                {field.label}
                                            </div>
                                        )}
                                        {field.render({
                                            value,
                                            form: localFormData,
                                            onChange: handleChange,
                                            error:
                                                crudState.fieldErrors[
                                                    field.key as string
                                                    ],
                                        })}
                                    </div>
                                );
                            })
                        )}
                    </div>

                    <DialogFooter>
                        <Button
                            variant="secondary"
                            onClick={handleCloseModal}
                            disabled={isSubmitting}
                        >
                            Cancel
                        </Button>
                        <Button
                            autoFocus
                            variant="default"
                            disabled={isSubmitting}
                            onClick={handleSubmit}
                        >
                            {isSubmitting
                                ? "Saving..."
                                : crudState.modalMode === "create"
                                    ? "Create"
                                    : "Save"}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <AlertDialog
                open={canDelete && deleteTargetId !== null}
                onOpenChange={(open) => {
                    if (!open) handleCancelDelete();
                }}
            >
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>
                            Delete {resourceName}
                        </AlertDialogTitle>
                        <AlertDialogDescription>
                            This action cannot be undone. It will permanently
                            delete this {resourceName}.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel onClick={handleCancelDelete}>
                            Cancel
                        </AlertDialogCancel>
                        <AlertDialogAction onClick={handleConfirmDelete}>
                            Delete
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </>
    );
}
