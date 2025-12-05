import {useEffect, useState} from "react"

type ModalMode = 'create' | 'edit' | null

/**
 * DTO постраничного ответа API
 */
interface PaginatedListDto<T> {
    items: T[];
    currentPage: number;
    lastPage: number;
    perPage: number;
    total: number;
}
interface UseCrudTableOptions<T> {
    defaultForm: () => Partial<T>;
    initialData?: PaginatedListDto<T>;
}

export function useCrudTableState<T extends { id: number }>(
    { defaultForm, initialData }: UseCrudTableOptions<T>
) {
    const [modalMode, setModalMode] = useState<ModalMode>(null)
    const [formData, setFormData] = useState<Partial<T>>(defaultForm())
    const [currentItem, setCurrentItem] = useState<T | null>(null)
    const [dialogOpen, setDialogOpen] = useState(false)
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({})
    const [formError, setFormError] = useState<string | null>(null)

    const [paginated, setPaginated] = useState<PaginatedListDto<T>>(initialData ?? {
        items: [],
        currentPage: 1,
        lastPage: 1,
        perPage: 10,
        total: 0,
    });

    useEffect(() => {
        if (initialData) {
            setPaginated(initialData)
        }
    }, [initialData])

    const updateItem = (id: number, partial: Partial<T>) => {
        setPaginated((prev) => ({
            ...prev,
            items: prev.items.map((item) =>
                item.id === id ? { ...item, ...partial } : item
            ),
        }));
    };

    const deleteItem = (id: number) => {
        setPaginated((prev) => ({
            ...prev,
            items: prev.items.filter((item) => item.id !== id),
        }));
    };

    const clearItems = () => {
        setPaginated((prev) => ({
            ...prev,
            items: [],
            total: 0,
            currentPage: 1,
            lastPage: 1,
        }));
    };


    const setItems = (items: T[]) => {
        setPaginated((prev) => ({ ...prev, items }));
    };

    const resetErrors = () => {
        setFieldErrors({})
        setFormError(null)
    }

    const openCreateModal = () => {
        setFormData(defaultForm())
        setCurrentItem(null)
        setModalMode("create")
        setDialogOpen(true)
    }

    const openEditModal = (item: T) => {
        setFormData(item)
        setCurrentItem(item)
        setModalMode("edit")
        setDialogOpen(true)
    }

    const closeModal = () => {
        resetModal()
    }

    const resetModal = () => {
        setDialogOpen(false)
        setModalMode(null)
        setCurrentItem(null)
    }

    const setFormValue = <K extends keyof T>(key: K, value: T[K]) => {
        setFormData((prev) => ({ ...prev, [key]: value }))
    }

    const submit = async (
        onCreate?: (data: Partial<T>) => Promise<any>,
        onUpdate?: (id: number, data: Partial<T>) => Promise<any>
    ) => {
        if (modalMode === "create" && onCreate) {
            await onCreate(formData)
        }
        if (modalMode === "edit" && onUpdate && currentItem) {
            await onUpdate(currentItem.id, formData)
        }
    }

    return {
        paginated,
        setPaginated,
        updateItem,
        deleteItem,
        setItems,
        clearItems,

        modalMode,
        dialogOpen,
        formData,
        currentItem,
        openCreateModal,
        openEditModal,
        closeModal,
        setFormValue,
        submit,

        fieldErrors,
        setFieldErrors,
        formError,
        setFormError,
        resetErrors,
    }
}
