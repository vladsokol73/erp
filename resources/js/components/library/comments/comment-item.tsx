import React, { useState } from "react";
import { Dot, Pen, Trash2, Check, X, MoreVertical } from "lucide-react";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { DateFormatter } from "@/components/common/date-formatter";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Textarea } from "@/components/ui/textarea";

interface CommentItemProps {
    commentId: number;
    author: {
        name: string;
        avatarFallback: string;
    };
    date: string;
    content: string;
    onEdit?: (commentId: number, newContent: string) => void;
    onDelete?: () => void;
}

const CommentItem = ({
                         commentId,
                         author,
                         date,
                         content,
                         onEdit,
                         onDelete,
                     }: CommentItemProps) => {
    const [isEditing, setIsEditing] = useState(false);
    const [editValue, setEditValue] = useState(content);

    const handleSave = () => {
        const trimmed = editValue.trim();
        if (!trimmed) return;
        onEdit?.(commentId, trimmed);
        setIsEditing(false);
    };

    const handleCancel = () => {
        setEditValue(content);
        setIsEditing(false);
    };

    return (
        <div className="flex flex-col gap-2 group">
            <div className="flex text-sm gap-2 w-full">
                <Avatar>
                    <AvatarFallback>{author.avatarFallback}</AvatarFallback>
                </Avatar>

                <div className="flex flex-col gap-2 w-full">
                    <div className="flex items-center justify-between">
                        <div className="flex gap-0.5 items-center text-sm font-medium">
                            {author.name}
                            <Dot size={24} />
                            <DateFormatter
                                variant="shortWithTime"
                                className="text-xs text-muted-foreground"
                                dateString={date}
                            />
                        </div>

                        {(onEdit || onDelete) && (
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        className="h-6 w-6"
                                    >
                                        <MoreVertical className="w-4 h-4 text-muted-foreground" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent side="left" align="center">
                                    {onEdit && !isEditing && (
                                        <DropdownMenuItem onClick={() => setIsEditing(true)}>
                                            <Pen className="w-4 h-4" />
                                            Edit
                                        </DropdownMenuItem>
                                    )}
                                    {onDelete && (
                                        <DropdownMenuItem onClick={onDelete}>
                                            <Trash2 className="w-4 h-4 text-destructive" />
                                            Delete
                                        </DropdownMenuItem>
                                    )}
                                </DropdownMenuContent>
                            </DropdownMenu>
                        )}
                    </div>

                    <div className="bg-accent/50 text-accent-foreground px-5 py-4 rounded-md text-sm">
                        {isEditing ? (
                            <div className="flex flex-col gap-4">
                                <Textarea
                                    value={editValue}
                                    onChange={(e) => setEditValue(e.target.value)}
                                    placeholder="Edit your comment"
                                    className="text-sm"
                                />
                                <div className="flex gap-2 justify-end">
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={handleCancel}
                                        className="text-muted-foreground"
                                    >
                                        Cancel
                                    </Button>
                                    <Button
                                        variant="secondary"
                                        size="sm"
                                        onClick={handleSave}
                                        className="text-muted-foreground"
                                    >
                                        <Check className="text-primary" />
                                        Save
                                    </Button>
                                </div>
                            </div>
                        ) : (
                            content
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CommentItem;
