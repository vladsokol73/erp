import { useState } from "react";
import { z } from "zod";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import InputWithButton from "@/components/ui/input-with-button";
import CommentList from "./comment-list";

// Определение схемы валидации для комментария
const commentSchema = z
    .string()
    .min(1, "Comment cannot be empty")
    .max(500, "Comment must be less than 500 characters");

interface Comment {
    id: string | number;
    author: {
        name: string;
        avatarFallback: string;
    };
    date: string;
    content: string;
}

interface CommentDialogProps {
    isOpen: boolean;
    onClose: () => void;
    comments: Comment[];
    onAddComment: (comment: string) => void;
    // Дополнительные свойства для настройки валидации
    maxCommentLength?: number;
    customValidation?: (comment: string) => string | null;
}

// Компонент диалога комментариев
const CommentDialog = ({
                           isOpen,
                           onClose,
                           comments,
                           onAddComment,
                           maxCommentLength = 500,
                           customValidation
                       }: CommentDialogProps) => {
    const [comment, setComment] = useState("");
    const [error, setError] = useState<string | null>(null);

    const getDynamicCommentSchema = () => {
        return z
            .string()
            .min(1, "Comment cannot be empty")
            .max(maxCommentLength, `Comment must be less than ${maxCommentLength} characters`);
    };

    // Функция валидации комментария
    const validateComment = (value: string): string | null => {
        try {
            getDynamicCommentSchema().parse(value);

            if (customValidation) {
                const customError = customValidation(value);
                if (customError) return customError;
            }

            return null;
        } catch (error) {
            if (error instanceof z.ZodError) {
                return error.errors[0].message;
            }
            return "Invalid comment";
        }
    };

    // Обработчик изменения текста комментария
    const handleCommentChange = (value: string) => {
        setComment(value);
        if (error) {
            setError(null);
        }
    };

    // Обработчик отправки комментария
    const handleCommentSubmit = (value: string) => {
        const validationError = validateComment(value);

        if (!validationError) {
            onAddComment(value);
            setComment("");
            setError(null);
        } else {
            setError(validationError);
        }
    };

    return (
        <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Comments</DialogTitle>
                </DialogHeader>
                <div className="flex flex-col gap-6">
                    <InputWithButton
                        value={comment}
                        onChange={handleCommentChange}
                        placeholder="Your comment here"
                        onButtonClick={handleCommentSubmit}
                        buttonAriaLabel="Add comment"
                        type="text"
                        error={error}
                    />
                    <div className="max-h-96 overflow-y-auto">
                        <CommentList comments={comments} />
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default CommentDialog;
