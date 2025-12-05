import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import { ScrollArea } from "@/components/ui/scroll-area";
import { useState, useMemo } from "react";
import { TagCreative } from "@/components/library/types";

interface TagSelectorProps {
    allTags: TagCreative[];
    selectedIds: number[];
    onChange: (ids: number[]) => void;
    disabled?: boolean;
}

export const TagSelector = ({
                                allTags,
                                selectedIds,
                                onChange,
                                disabled = false,
                            }: TagSelectorProps) => {
    const [search, setSearch] = useState("");

    const toggleTag = (id: number) => {
        onChange(
            selectedIds.includes(id)
                ? selectedIds.filter((tagId) => tagId !== id)
                : [...selectedIds, id]
        );
    };

    const filteredTags = useMemo(() => {
        return allTags.filter(({ name }) =>
            name.toLowerCase().includes(search.toLowerCase())
        );
    }, [search, allTags]);

    return (
        <div className="space-y-4">
            <Input
                placeholder="Search tags..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                disabled={disabled}
            />

            <ScrollArea className="max-h-60">
                <div className="flex flex-wrap gap-2">
                    {filteredTags.map(({ id, name, style }) => (
                        <Badge
                            key={id}
                            variant="default"
                            className={`cursor-pointer py-1 select-none ${
                                selectedIds.includes(id)
                                    ? `bg-${style} text-white`
                                    : `border-${style} bg-transparent text-${style}`
                            }`}
                            onClick={() => !disabled && toggleTag(id)}
                        >
                            {name}
                        </Badge>
                    ))}
                </div>
            </ScrollArea>
        </div>
    );
};
