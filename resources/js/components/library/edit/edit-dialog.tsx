import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { useEffect, useState } from "react";
import { TagCreative } from "@/components/library/types";
import { TagSelector } from "@/components/common/tag-selector";

interface CreativeEditDialogProps {
    allTags: TagCreative[];
    currentTags: TagCreative[];
    isOpen: boolean;
    onClose: () => void;
    onSave: (selectedTags: TagCreative[]) => Promise<void>;
    onDelete: () => Promise<void>;
}

const EditDialog = ({
                        allTags,
                        currentTags,
                        isOpen,
                        onClose,
                        onSave,
                        onDelete,
                    }: CreativeEditDialogProps) => {
    const [selectedIds, setSelectedIds] = useState<number[]>([]);
    const [search, setSearch] = useState("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (isOpen) {
            setSelectedIds(currentTags.map(tag => tag.id));
            setError(null); // сброс ошибки при новом открытии
        }
    }, [isOpen]);

    const filteredTags = allTags.filter(({ name }) =>
        name.toLowerCase().includes(search.toLowerCase())
    );

    const handleSave = async () => {
        setLoading(true);
        setError(null);

        const selected = allTags.filter(tag => selectedIds.includes(tag.id));

        try {
            await onSave(selected);
            onClose();
        } catch (e) {
            setError("Error saving tags");
        } finally {
            setLoading(false);
        }
    };

    const handleDelete = async () => {
        setLoading(true);
        setError(null);

        try {
            await onDelete();
            onClose();
        } catch (e) {
            setError("Error deleting creative");
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Creative Edit</DialogTitle>
                </DialogHeader>

                <div className="space-y-6">

                    <TagSelector
                        allTags={allTags}
                        selectedIds={selectedIds}
                        onChange={setSelectedIds}
                        disabled={loading}
                    />

                    {error && (
                        <p className="text-sm text-red">
                            {error}
                        </p>
                    )}

                    <div className="flex justify-between mt-4">
                        <Button variant="destructive" onClick={handleDelete} disabled={loading}>
                            {loading ? "Deleting..." : "Delete"}
                        </Button>
                        <div className="space-x-2">
                            <Button variant="outline" onClick={onClose} disabled={loading}>
                                Cancel
                            </Button>
                            <Button onClick={handleSave} disabled={loading}>
                                {loading ? "Saving..." : "Save"}
                            </Button>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default EditDialog;
